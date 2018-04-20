<?php

namespace GravureBundle\Controller;

use GravureBundle\Entity\Domain\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class SPAController extends Controller
{
    /**
     * @Route("/", name="spa_index")
     */
    public function indexAction()
    {
        $datetime = $this->get('creator.datetime.limit')->getDateTime();

        return $this->render('GravureBundle:spa:index.html.twig',['datetime' => $datetime]);
    }

    /**
     * @Route("/new-gravure/json/{bool}", name="new_gravure_json", options={"expose"=true})
     */
    public function searchNewGravureAction($bool){
        //permet de garder en mémoire le choix pour les caisses en cas de coupure internet
        //si le bouton actualiser est cliqué on cherche simplement les nouvelles gravures en supprimant les numéros de caisse et l'état checked

        $last_order = self::actualise();

        if ($bool == 0) {
            $response = self::getNewGravureAndCleanBoxChecked($last_order);
        } else {
            $response = self::getNewGravure($last_order);
        }

        return new JsonResponse($response);
    }


    private function actualise()
    {
        set_time_limit(500); //permet d'augmenter le temps maximal d'execution

        $em = $this->getDoctrine()->getManager();
        $array_state = [1, 2, 3, 4, 30, 31]; //tableau contenant les "bons" etats des commandes
        $persta = $this->get('iq2i_prestashop_web_service')->getInstance(); //instance prestashop web service

        $last_order = $this->get('repositories.order')->findLast()['id_prestashop']; //récupère l'id de la dernière commande sur prestashop
        $regulator = $em->getRepository('GravureBundle:OrderRegulator')->find(1); //objet OrderRegulator
        $diff = $regulator->getNumber(); //valeur du regulateur
        //détermine la fourchette de recherche
        $id_min_presta = $last_order - $diff;
        $id_max_presta = $last_order + $diff;

        //récupère les X dernières commandes
        $result = $persta->get(array(
            "resource" => "orders",
            "filter[id]" => '[' . $id_min_presta . ',' . $id_max_presta . ']',
            "display" => '[id,id_cart,current_state,date_add]',
        ));

        $result = json_decode(json_encode((array)$result), TRUE);

        $array_id_order = $result['orders']['order']; //récupère  les commandes

//        var_dump($array_id_order);

        //parcourt de toutes les commandes
        foreach ($array_id_order as $id_order) {
            //vérifie si l'état de la commande est adéquat
            if (in_array($id_order['current_state'], $array_state)) {

                //vérifie si cette commande est déjà présente dans la bdd locale
                $currentOrder = $this->get('repositories.order')->findByIdPrestashop($id_order['id']);
                //si elle ne l'est pas on vérifie on va vérifier si elle a des gravures liées
                if ($currentOrder == null) {

                    //vérifie la présence d'objet à graver
                    $result_config_cart = $persta->get(array(
                        "resource" => "config_carts",  //trouver l'acces
                        "filter[id_cart]" => '[' . $id_order['id_cart'] . ']',
                        "display" => '[id,id_product,quantity]',
                    ));

                    $result_config_cart = json_decode(json_encode((array)$result_config_cart), TRUE);
//                    var_dump($id_order['id_cart']);
//                    var_dump($result_config_cart['config_carts']);
                    //si il y a une bien une gravure on va la rajoute à notre bdd
                    if (isset($result_config_cart['config_carts']['config_cart'])) {
//                        var_dump($result_config_cart['config_carts']['config_cart']);

                        $boolGift = self::checkGift($persta, $id_order); //boolean pour le papier cadeau
                        var_dump($boolGift);
                        //Creation de la commande
                        $order = Order::addOrder($boolGift, $id_order['id'], $id_order['current_state'], $id_order['date_add']);
                        $this->get('repositories.order')->save($order);

                        //                    var_dump($id_order['id']);

                        //creation des gravures
                        $gravures = $this->get('factory.gravure')->createGravures($order, $result_config_cart['config_carts']['config_cart']);
                        var_dump($gravures);

                        foreach ($gravures as $gravure) {
                            $this->get('repositories.gravure')->save($gravure);
                            //recuperation de l'id de la gravure
                            $idGravure = $this->get('repositories.gravure')->findByIdConfig($gravure->getConfigId())['id'];
                            var_dump($idGravure);

                            //récupère tous les éléments de la table config_cart pour une gravure
                            $result_config_cart_block = $persta->get(array(
                                "resource" => "config_carts",
                                "filter[id]" => '[' . $gravure->getConfigId() . ']',
                                "display" => 'full',
                            ));

                            $result_config_cart_block = json_decode(json_encode((array)$result_config_cart_block), TRUE);

                            //vérifie s'il y a un seul block texte lié à la gravure
                            if (array_key_exists('value', $result_config_cart_block['config_carts']['config_cart']['associations']['config_option']['config_option'])) {
                                $blockValue = $result_config_cart_block['config_carts']['config_cart']['associations']['config_option']['config_option']['value']; //valeur du block entré par le client
                                $blockId = $result_config_cart_block['config_carts']['config_cart']['associations']['config_option']['config_option']['id_block']; //id du block

                                //cherche le nom du block grâce à son id
                                $result_config_product_block = $persta->get(array(
                                    "resource" => "config_block",
                                    "filter[id]" => '[' . $blockId . ']',
                                    "display" => '[name]',
                                ));

                                $result_config_product_block = json_decode(json_encode((array)$result_config_product_block), TRUE);
                                $blockName = $result_config_product_block['config_product_blocks']['config_product_block']['name'];

                                $this->get('repositories.text')->saveTextAndLinkGravureText($idGravure, $blockValue, $blockName);

                            } else //s'il y a plusieurs blocks texte
                            {
                                foreach ($result_config_cart_block['config_carts']['config_cart']['associations']['config_option']['config_option'] as $block) {
                                    $blockValue = $block['value']; //valeur du block entré par le client
                                    $blockId = $block['id_block']; //id du block

                                    //cherche le nom du block grâce à son id
                                    $result_config_product_block = $persta->get(array(
                                        "resource" => "config_block",
                                        "filter[id]" => '[' . $blockId . ']',
                                        "display" => '[name]',
                                    ));

                                    $result_config_product_block = json_decode(json_encode((array)$result_config_product_block), TRUE);
                                    $blockName = $result_config_product_block['config_product_blocks']['config_product_block']['name'];

                                    $this->get('repositories.text')->saveTextAndLinkGravureText($idGravure, $blockValue, $blockName);

                                }
                            }
                        }
                        $this->get('factory.gravure')->clearListGravure(); //vide le tableau qui contient la liste des gravures

                    }


                } else { //si la commande est déjà en bdd
                    // maj de l'état prestashop
//                    self::UpdateState($currentOrder, $persta);
                }
            }
        }

        return $last_order;

    }

    //vérifie la présence d'emballage cadeau dans la commande
    private function checkGift($persta, $idOrder)
    {
        $result = $persta->get(array(
            "resource" => "giftextra_cart",
            "filter[id_cart]" => '[' . $idOrder['id_cart'] . ']',
            "display" => '[id]'
        ));

        $result = json_decode(json_encode((array)$result), TRUE);
        //détermine la présence de papier cadeau dans une des commandes
        if (isset($result['nq_giftextra_carts']['nq_giftextra_cart'])) {
            return 1;
        } else {
            return 0;
        }
    }


    //fonction pour pour mettre à jour l'état des gravures si celles-ci ne sont pas expédition en cours
    private function UpdateState($currentOrder, $persta)
    {

        if ($currentOrder['state_prestashop'] != 4) {
            //recherche de l'état actuel de chaque commande
            $result = $persta->get(array(
                "resource" => "orders",
                "filter[id]" => '[' . $currentOrder['id_prestashop'] . ']',
                "display" => '[current_state]',
            ));

            $result = json_decode(json_encode((array)$result), TRUE);
            $order_state = $result['orders']['order']['current_state']; //récupère l'état

            $this->get('repositories.order')->updateStatePrestashop($currentOrder['id'], $order_state); //mis à jour de l'état

        }
    }

    //retourne un tableau contenant les gravures sans session et leurs données
    private function getNewGravure($last_order)
    {
        $new_last_order = $this->get('repositories.order')->findLast()['id_prestashop']; //récupère l'id de la dernière commande sur prestashop

        //récupération de toutes les gravures sans session et qui sont arrivées avant l'heure limite de fin de journée
        $gravures = $this->get('repositories.gravure')->findAllWithoutSessionByState();

        $formatted = [];
        $time = 0;

        foreach ($gravures as $gravure) {
            $time += $gravure['time']; //calcul du temps total

            $formatted[] = [
                'id' => $gravure['id'],
                'id_prestashop' => $gravure['id_prestashop'],
                'state_prestashop' => $gravure['state_prestashop'],
                'jpg' => $gravure['path_jpg'],
                'pdf' => $gravure['path_pdf'],
                'id_product' => $gravure['product_id'],
                'box' => $gravure['box'],
                'checked' => $gravure['checked']
            ];
        }
        //ajout du temps au tableau
        $formatted[] = ['time' => $time];

        //ajout d'un message si il n'y a pas de nouvelles commandes
        if ($new_last_order == $last_order) {
            $formatted[] = ['msg' => "NoNewOrder"]; //enverra un message pour l'utilisateur
        }

        return $formatted;
    }


    public function getNewGravureAndCleanBoxChecked($last_order){

        $new_last_order = $this->get('repositories.order')->findLast()['id_prestashop']; //récupère l'id de la dernière commande sur prestashop

        //récupération des commandes qui n'ont pas le statut gravé et met à 0 le numéro de caisse et le checked
        $this->get('repositories.order')->cleanBoxAndChecked();
        //récupération de toutes les gravures sans session et qui sont arrivées avant l'heure limite de fin de journée
        $gravures = $this->get('repositories.gravure')->findAllWithoutSessionByState();

        $formatted = [];
        $time = 0;

        foreach ($gravures as $gravure) {
            $time += $gravure['time']; //calcul du temps total

            $formatted[] = [
                'id' => $gravure['id'],
                'id_prestashop' => $gravure['id_prestashop'],
                'state_prestashop' => $gravure['state_prestashop'],
                'jpg' => $gravure['path_jpg'],
                'pdf' => $gravure['path_pdf'],
                'id_product' => $gravure['product_id'],
                'box' => $gravure['box'],
                'checked' => $gravure['checked']
            ];
        }
        //ajout du temps au tableau
        $formatted[] = ['time' => $time];

        //ajout d'un message si il n'y a pas de nouvelles commandes
        if ($new_last_order == $last_order) {
            $formatted[] = ['msg' => "NoNewOrder"]; //enverra un message pour l'utilisateur
        }

        return $formatted;
    }



}
