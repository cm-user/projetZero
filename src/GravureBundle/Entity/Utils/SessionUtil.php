<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 27/06/2018
 * Time: 11:47
 */

namespace GravureBundle\Entity\Utils;


use GravureBundle\Entity\Domain\Session;
use Symfony\Component\DependencyInjection\Container;

class SessionUtil
{
    private $container;

    /**
     * GravureFactory constructor.
     *
     */
    public function __construct(Container $container = null )
    {
        $this->container = $container;
    }

    public function getRecentOrNewIdSession($user){

        $empty = $this->container->get('repositories.chain_session')->isEmpty(); //vérifie si la table chain_session est vide

        if($empty != []){ //si elle n'est pas vide, reprend le numéro de la session en cours
            $idSession = $this->container->get('repositories.session')->findMaxId();//recherche de la dernière session créé
        }
        else{ //si elle vide, création d'une nouvelle session
            $session = Session::addSession($user->getName() , 0); //création nouvelle session
            $this->container->get('repositories.session')->save($session);
            $idSession = $this->container->get('repositories.session')->findMaxId();//recherche de la dernière session créé
        }

        return $idSession;
    }

    public function getRecentOrFutureIdSession(){
        $empty = $this->container->get('repositories.chain_session')->isEmpty(); //vérifie si la table chain_session est vide

        if($empty != []){ //si elle n'est pas vide, reprend le numéro de la session en cours
            $idSession = $this->container->get('repositories.session')->findMaxId();//recherche de la dernière session créé
        }
        else{ //si elle vide, création d'une nouvelle session
            $idSession = $this->container->get('repositories.session')->findMaxId();//recherche de la dernière session créé
            $idSession = $idSession + 1;
        }

        return $idSession;
    }
}