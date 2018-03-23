<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 10:54
 */

namespace GravureBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Boxnumber controller.
 *
 * @Route("session")
 */
class SessionController extends Controller
{

    /**
     * @Route("/", name="gravure_session_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $sessions = $this->get('repositories.session')->findAll();

        return $this->render('GravureBundle:session:index.html.twig', array(
            'sessions' => $sessions,
        ));
    }
}