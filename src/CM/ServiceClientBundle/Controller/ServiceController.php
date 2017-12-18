<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CM\ServiceClientBundle\Form\MailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Mail controller.
 *
 * @Route("service")
 */

class ServiceController extends Controller
{
    /**
     * @Route("/", name="service_index")
     */
    public function indexAction()
    {
    
        $ListeMail="ok";
   

        return $this->render('ServiceClientBundle:service:index.html.twig', array(
            'mails' =>$ListeMail,
        ));

    }

    
}
