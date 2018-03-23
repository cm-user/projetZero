<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/03/2018
 * Time: 15:04
 */

namespace GravureBundle\Twig;


use GravureBundle\Repository\DbalMachineRepository;
use Symfony\Component\DependencyInjection\Container;

class CategoryExtension extends \Twig_Extension
{
    private $container;
    public function __construct(Container $container=null)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'getNameMachine' => new \Twig_Function_Method($this, 'FindNameMachine'),
        );
    }

    public function FindNameMachine($id){
        $Machine = $this->container->get('repositories.machine')->findById($id);
        return $Machine->getName();
    }

}