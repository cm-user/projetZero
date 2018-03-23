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

        return $this->render('GravureBundle:order:list_gravure.html.twig', array(
            'gravures' => $gravures,
        ));
    }
}