<?php

namespace GravureBundle\Controller;

use GravureBundle\Entity\Domain\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class SPAController extends Controller
{
    /**
     * @Route("/", name="spa_index")
     */
    public function indexAction()
    {
        return $this->render('GravureBundle:spa:index.html.twig');
    }

    /**
     * @Route("/new-gravure/json", name="new_gravure_json", options={"expose"=true})
     */
    public function searchNewGravureAction()
    {

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
                if($currentOrder == null){

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
                    var_dump("bien la");

                        //creation des gravures
                        $gravures = $this->get('factory.gravure')->createGravures($order, $result_config_cart['config_carts']['config_cart']);
                        var_dump($gravures);
                    }





                }
                else{ //si la commande est déjà en bdd
                   //$currentOrder object disponible
                    var_dump($id_order['id']);
                    var_dump("coucou");
                }


            }
        }
        $formatted = [];
//        return new JsonResponse($formatted);
        return $this->render('GravureBundle:spa:index.html.twig');

    }

    private function checkGift($persta, $idOrder){
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
}
