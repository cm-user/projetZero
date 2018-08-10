<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 13/04/2018
 * Time: 15:36
 */

namespace GravureBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GravureController extends Controller
{

    /**
     * @Route("/order/uncheck/{id_prestashop}", name="order_uncheck", options={"expose"=true})
     */
    public function orderUncheckedAction($id_prestashop)
    {
        $order = $this->get('repositories.order')->findByIdPrestashop($id_prestashop);

        //modifie la valeur du check en fonction de son etat précédent
        if ($order['checked'] == 1) {
            $this->get('repositories.order')->setCheckedAndSession($order['id'], 0);
        } else {
            $this->get('repositories.order')->setChecked($order['id'], 1);
        }

        return new Response("image uncheck");
    }


    /**
     * @Route("/order/addBox/{id_prestashop}/{box}", name="order_add_box", options={"expose"=true})
     */
    public function orderAddBoxAction($id_prestashop, $box)
    {
        $order = $this->get('repositories.order')->findByIdPrestashop($id_prestashop);

        //modifie la valeur du check en fonction de son etat précédent
        if ($order['checked'] == 1) {
            $this->get('repositories.order')->updateCheckedAndBox($order['id'], 0, 0);
            $this->get('repositories.gravure')->updateSessionAndStatusByIdOrder($this->getParameter('status_EN_ATTENTE'), $order['id']); //remet à null la session et le statut en attente des gravures liée à cette commande
        } else {
            $this->get('repositories.order')->updateCheckedAndBox($order['id'], 1, $box);

        }

        return new Response("box change and check");
    }

    /**
     * @Route("/gravure/number", name="gravure_number", options={"expose"=true})
     */
    public function gravureNumberAction()
    {

        $datetime = $this->get('creator.datetime.limit')->getDateTime();

        $response = $this->get('repositories.gravure')->countGravureNumber($datetime, $this->getParameter('status_TERMINE'));

        return new JsonResponse($response);
    }

    /**
     * @Route("/gravure/jpg/{id_prestashop}", name="order_jpg_json", options={"expose"=true})
     */
    public function gravureJpgJsonAction($id_prestashop)
    {

        $gravures = $this->get('repositories.gravure')->findByOrderIdPrestashop($id_prestashop);
        $formatted = [];

        foreach ($gravures as $gravure) {

            $formatted[] = [
                'jpg' => $gravure['path_jpg']
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/gravure/tomorrow", name="new_gravure_tomorrow", options={"expose"=true})
     */
    public function getNewGravureTomorrow()
    {

        $datetime = $this->get('creator.datetime.limit')->getDateTime();

        //récupération de toutes les gravures sans session et qui sont arrivées après l'heure limite de fin de journée
        $gravures = $this->get('repositories.gravure')->findAllWithoutSessionAfterDateLimit($datetime);

        $formatted = [];

        foreach ($gravures as $gravure) {

            $formatted[] = [
                'id' => $gravure['id'],
                'id_prestashop' => $gravure['id_prestashop'],
                'jpg' => $gravure['path_jpg'],
                'pdf' => $gravure['path_pdf'],
                'id_product' => $gravure['product_id']
            ];
        }

        return new JsonResponse($formatted);

    }

    /**
     * @Route("/gravure/soon-available", name="new_gravure_soon_available", options={"expose"=true})
     */
    public function getNewGravureSoonAvailable()
    {
        //récupération de toutes les gravures sans session et qui sont arrivées après l'heure limite de fin de journée
        $gravures = $this->get('repositories.gravure')->findAllWithoutSessionAndNotStatePrestaExpe();

        $formatted = [];

        foreach ($gravures as $gravure) {

            $formatted[] = [
                'id' => $gravure['id'],
                'id_prestashop' => $gravure['id_prestashop'],
                'jpg' => $gravure['path_jpg'],
                'pdf' => $gravure['path_pdf'],
                'id_product' => $gravure['product_id']
            ];
        }

        return new JsonResponse($formatted);

    }

    /**
     * @Route("/gravure/chain-number/json", name="gravure_chain_number_json", options={"expose"=true})
     */
    public function getGravureWithChainNumber()
    {
        //récupération de toutes les gravures avec la plus haute session, qui sont checked et qui n'ont pas le statut engrave
        $gravures = $this->get('repositories.gravure')->findAllWithHighSessionAndStatusNotFinish();

        $orderToLock = $this->get('repositories.gravure')->findAllWithEngraveFinishOrOnLoad($this->getParameter('status_TERMINE'), $this->getParameter('status_EN_COURS')); //récupére les commandes qui ont des gravures dans les chaînes en cours
        //formatage du tableau pour avoir les id dans un tableau à une dimension
        $orderToLock = array_map(function ($value) {
            return $value['id'];
        }, $orderToLock);

        $formatted = [];

        foreach ($gravures as $gravure) {

            if (in_array($gravure['id'], $orderToLock)) {
                $orderLocked = 1;
            } else {
                $orderLocked = 0;
            }

            $colorGravure = $this->get('repositories.gravure')->findColorMachineById($gravure['id']);

            $formatted[] = [
                'id' => $gravure['id'],
                'id_prestashop' => $gravure['id_prestashop'],
                'jpg' => $gravure['path_jpg'],
                'box' => $gravure['box'],
                'status' => $gravure['id_status'],
                'chain_number' => $gravure['chain_number'],
                'colorCategory' => $gravure['color'],
                'colorGravure' => $colorGravure['color'],
                'series_number' => $gravure['series_number'],
                'alias' => $gravure['alias'],
                'locked' => $orderLocked
            ];


        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/gravure/default-machine/{id}", name="gravure_change_machine_default", options={"expose"=true})
     */
    public function setDefaultMachineFront($id)
    {
        $machine = $this->get('repositories.gravure')->findColorMachineForceById($id);
        //vérifie que la catégorie de la gravure ne soit pas liée à une machine
        if ($machine != 1) {
            $response = "machine did not change for this gravure : $id";
        } else { //sinon on peut changer la machine de la gravure
            $idMachine = $this->get('session')->get('id_machine_used'); //récupére l'id machine dans la session
            $this->get('repositories.gravure')->updateMachine($id, $idMachine);
            $response = "machine changed for this gravure : $id";
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/gravure/status/on-load/{chainNumber}", name="gravure_status_on_load", options={"expose"=true})
     */
    public function setStatusOnLoad($chainNumber)
    {
        $request = $this->get('repositories.gravure')->setStatusByChainNumber($this->getParameter('status_EN_COURS'), $chainNumber); //modification du statut pour les gravures liées à la chaîne
        $this->get('repositories.chain_session')->setLockedPosition($chainNumber);  // verouillage de la chaîne

        if ($request == true) {
            return new JsonResponse("Changement de statut effectué");
        } else {
            return new JsonResponse("Impossible de modifier le statut des gravures lié à la chaîn N° " . $chainNumber);
        }

    }

    /**
     * @Route("/gravure/status/finish/{chainNumber}", name="gravure_status_finish", options={"expose"=true})
     */
    public function setStatusFinish($chainNumber)
    {
        $this->get('repositories.gravure')->setStatusByChainNumber($this->getParameter('status_TERMINE'), $chainNumber); //modification du statut pour les gravures liées à la chaîne

        $orders = $this->get('repositories.gravure')->findOrderByChainNumber($chainNumber); // récupére les commandes liées à la chaîne

        if ($orders != null) {
            $arrayOrderWithGift = [];
            $arrayOrderWithoutGift = [];
            foreach ($orders as $order) {
                $orderFinish = $this->get('repositories.order')->isEngraved($this->getParameter('status_TERMINE'), $order['id_order']); //récupére la commande si toutes ses gravures sont terminées
                if ($orderFinish != null) {
                    $this->get('repositories.order')->setEngrave($order['id_order']); //Renseigne que la commande est gravé
                    if ($orderFinish[0]['gift'] == 1) {
                        $arrayOrderWithGift[] = $orderFinish[0]['box'];
                    } else {
                        $arrayOrderWithoutGift[] = $orderFinish[0]['box'];
                    }
                }
            }

            return new JsonResponse([
                $arrayOrderWithoutGift == [] ? '' : 'Amener la ou les caisse(s) N° ' . implode(", ", $arrayOrderWithoutGift) . ' à l\'expé',
                $arrayOrderWithGift == [] ? '' : ' Amener la ou les caisse(s) N° ' . implode(", ", $arrayOrderWithGift) . ' au paquet cadeau'
            ]);
        } else {
            return new JsonResponse(0); //Aucune commande n'est prête
        }


    }

    /**
     * @Route("/gravure/cancel/finish/{chainNumber}", name="gravure_cancel_finish", options={"expose"=true})
     */
    public function cancelFinish($chainNumber)
    {
        $request = $this->get('repositories.gravure')->setStatusByChainNumber($this->getParameter('status_EN_COURS'), $chainNumber); //modification du statut pour les gravures liées à la chaîne
        $this->get('repositories.order')->setCancelEngrave($chainNumber); //change le statut des commandes en passant engrave à 0
        $this->get('repositories.chain_session')->setLockedPosition($chainNumber);  // verouillage de la chaîne

        if ($request == true) {
            return new JsonResponse("Changement de statut effectué");
        } else {
            return new JsonResponse("Impossible de modifier le statut des gravures lié à la chaîn N° " . $chainNumber);
        }

    }

//    /**
//     * @Route("/gravure/edit/session/", name="gravure_edit_session", options={"expose"=true})
//     */
//    public function editSession($chainNumber)
//    {
//        $gravures = $this->get('repositories.gravure')->findAllInSession($this->getParameter('status_EN_COURS')); //modification du statut pour les gravures liées à la chaîne
//
//        if ($request == true) {
//            return new JsonResponse("Changement de statut effectué");
//        } else {
//            return new JsonResponse("Impossible de modifier le statut des gravures lié à la chaîn N° " . $chainNumber);
//        }
//
//    }

    /**
     * @Route("/gravure/text/{id}", name="gravure_mono_text", options={"expose"=true})
     */
    public function getText($id)
    {

        $gravure = $this->get('repositories.gravure')->findById($id); //trouve la gravure
        $idSession = $this->get('repositories.session')->findMaxId(); // recherche la dernière session

        $this->get('repositories.gravure')->updateSessionAndStatusById($idSession, $this->getParameter('status_TERMINE'), $id); //maj du statut en termine et de la session pour la gravure

        //maj du nombre total de gravure de la session
        $number = $this->get('repositories.session')->countNumberEngraveInLastSession();
        $this->get('repositories.session')->updateNumberEngrave($number);

        $idOrder = $gravure->getIdOrder();

        $orderFinish = $this->get('repositories.order')->isEngraved($this->getParameter('status_TERMINE'), $idOrder); //récupére la commande si toutes ses gravures sont terminées
        if ($orderFinish != null) {
            $this->get('repositories.order')->setEngrave($idOrder); //Renseigne que la commande est gravé
        }

        $texts = $this->get('repositories.gravure_text')->findTextByIdGravure($id);

        return new JsonResponse($texts);
    }

}