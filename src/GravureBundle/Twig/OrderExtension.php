<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 14:36
 */

namespace GravureBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class OrderExtension extends \Twig_Extension
{
    private $container;
    public function __construct(Container $container=null)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'getAliasProduct' => new \Twig_Function_Method($this, 'FindAliasProduct'),
            'getCreateSession' => new \Twig_Function_Method($this, 'FindCreateSession'),
            'getNameStatus' => new \Twig_Function_Method($this, 'FindNameStatus'),
            'getNameMachine' => new \Twig_Function_Method($this, 'FindNameMachine'),
        );
    }

    public function FindAliasProduct($id){
        $Product = $this->container->get('repositories.product')->findById($id);
        if($Product == null){
            return "pas de produit lié";
        }
        return $Product->getAlias();
    }

    public function FindCreateSession($id){
        $Session = $this->container->get('repositories.session')->findById($id);
        if($Session == null){
            return "pas de session liée";
        }
        return $Session->getCreatedAt();
    }

    public function FindNameStatus($id){
        $nameStatus = $this->container->get('repositories.status')->findById($id);
        return $nameStatus;
    }

    public function FindNameMachine($id){
        $Machine = $this->container->get('repositories.machine')->findById($id);
        if($Machine == null){
            return "pas de machine liée";
        }
        return $Machine->getName();
    }

}