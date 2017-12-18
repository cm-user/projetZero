<?php

namespace CM\EngravingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ViewController extends Controller
{
    /**
     * @Route("/default")
     */
    public function indexAction()
    {
        return $this->render('EngravingBundle:view:index.html.twig');
    }
}
