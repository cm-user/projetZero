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
            $this->get('repositories.order')->setChecked($order['id'], 0);
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
            $this->get('repositories.order')->setChecked($order['id'], 0);
            $this->get('repositories.order')->setBox($order['id'], 0);
        } else {
            $this->get('repositories.order')->setChecked($order['id'], 1);
            $this->get('repositories.order')->setBox($order['id'], $box);

        }


        return new Response("box change and check");
    }

    /**
     * @Route("/gravure/number", name="gravure_number", options={"expose"=true})
     */
    public function gravureNumberAction()
    {

        $datetime = $this->get('creator.datetime.limit')->getDateTime();
        $response = $this->get('repositories.gravure')->countGravureNumber($datetime);

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
     * @Route("/gravure/today", name="new_gravure_today", options={"expose"=true})
     */
    public function getNewGravureToday()
    {

        $datetime = $this->get('creator.datetime.limit')->getDateTime();

        //récupération de toutes les gravures sans session et qui sont arrivées après l'heure limite de fin de journée
        $gravures = $this->get('repositories.gravure')->findAllWithoutSessionBeforeDateLimit($datetime);

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

        //récupération de toutes les gravures sans session et qui sont arrivées après l'heure limite de fin de journée
        $gravures = $this->get('repositories.gravure')->findAllWithChainNumber();

        $formatted = [];

        foreach ($gravures as $gravure) {

            $colorGravure = $this->get('repositories.gravure')->findColorMachineById($gravure['id']);

            $formatted[] = [
                'id' => $gravure['id'],
                'id_prestashop' => $gravure['id_prestashop'],
                'jpg' => $gravure['path_jpg'],
                'box' => $gravure['box'],
                'gift' => $gravure['gift'],
                'status' => $gravure['id_status'],
                'chain_number' => $gravure['chain_number'],
                'colorCategory' => $gravure['color'],
                'colorGravure' => $colorGravure['color'],
                'series_number' => $gravure['series_number'],
                'alias' => $gravure['alias']
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
        if ($machine != null) {
            $response = "machine did not change for this gravure : $id";
        } else { //sinon on peut changer la machine de la gravure
            $idMachine = $this->get('session')->get('id_machine_used'); //on stock l'id machine
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
        $this->get('repositories.gravure')->setStatusByChainNumber($this->getParameter('status_EN_COURS'), $chainNumber);

        return new JsonResponse("Changement de statut effectué");
    }

}