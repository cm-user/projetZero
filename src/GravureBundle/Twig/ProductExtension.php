<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 21/03/2018
 * Time: 13:50
 */

namespace GravureBundle\Twig;


use Symfony\Component\DependencyInjection\Container;

class ProductExtension extends \Twig_Extension
{
    private $container;
    public function __construct(Container $container=null)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'getSurnameCategory' => new \Twig_Function_Method($this, 'FindSurnameCategory'),
        );
    }

    public function FindSurnameCategory($id){
        $Category = $this->container->get('repositories.category')->findById($id);
        return $Category->getSurname();
    }
}