<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 11:57
 */

namespace GravureBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Boxnumber controller.
 *
 * @Route("order")
 */
class OrderController extends Controller
{
    /**
     * @Route("/", name="order_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $orders = $this->get('repositories.order')->findAll();
        return $this->render('GravureBundle:order:index.html.twig', array(
            'orders' => $orders,
        ));
    }

    /**
     * @Route("/display/gravure/{id}", name="gravure_order_display_gravure")
     * @Method("GET")
     */
    public function GetGravureAction($id)
    {
        $gravures = $this->get('repositories.gravure')->findAllByIdOrder($id);

        foreach ($gravures as $key => $gravure){
            $texts = $this->get('repositories.gravure_text')->findTextByIdGravure($gravure['id']);

          $gravures[$key]['texts'] = $texts;
        }

        return $this->render('GravureBundle:order:list_gravure.html.twig', array(
            'gravures' => $gravures,
        ));
    }

    /**
     * @Route("/display/clean", name="gravure_order_display_clean")
     * @Method("GET")
     */
    public function cleanOrderIndexAction()
    {
        return $this->render('GravureBundle:order:clean.html.twig', array());
    }

    /**
     * @Route("/clean/dashboard/{idPrestashop}", name="order_clean_dashboard", options={"expose"=true})
     */
    public function cleanOrder($idPrestashop)
    {
        $idSession = $this->get('repositories.session')->findMaxId(); // recherche la dernière session

        $this->get('repositories.gravure')->updateSessionAndStatusForEngrave($this->getParameter('status_TERMINE'), $idSession, $idPrestashop);
        $this->get('repositories.order')->updateEngrave($idPrestashop);

        //maj du nombre total de gravure de la session
        $number = $this->get('repositories.session')->countNumberEngraveInLastSession();
        $this->get('repositories.session')->updateNumberEngrave($number);

        return new JsonResponse(1);
    }

    /**
     * @Route("/search/{idPrestashop}", name="order_search_json", options={"expose"=true})
     */
    public function searchAction($idPrestashop)
    {
        $order = $this->get('repositories.order')->findByIdPrestashop($idPrestashop);

        return new JsonResponse($order);
    }

    /**
     * @Route("/delete/{idPrestashop}", name="order_delete_json", options={"expose"=true})
     */
    public function deleteAction($idPrestashop)
    {
        $this->get('repositories.order')->deleteByIdPrestashop($idPrestashop);

        return new JsonResponse("Commande supprimée");
    }
}