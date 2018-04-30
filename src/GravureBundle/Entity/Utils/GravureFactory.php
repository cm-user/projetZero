<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 11:13
 */

namespace GravureBundle\Entity\Utils;


use GravureBundle\Entity\Domain\Gravure;
use GravureBundle\Entity\Domain\Order;
use Symfony\Component\DependencyInjection\Container;

class GravureFactory
{

    private $listGravure = [];
    private $container;

    /**
     * GravureFactory constructor.
     *
     */
    public function __construct(Container $container = null )
    {
        $this->container = $container;
    }

    /**
     * @param array $listGravure
     */
    public function addListGravure(Gravure $gravure)
    {
        $this->listGravure[] = $gravure;
    }

    public function clearListGravure(){
        $this->listGravure = [];
    }

    public function createGravureFromQuantity($id_config, $productId, $quantity, $idOrder)
    {

        //////Methode pour récupérer l'id produit de chaque gravure pour trouver son produit lié///////
        $Product = $this->container->get('repositories.product')->findByProductId($productId);

//        var_dump($Product);


        if ($Product == null) {
            $idProduct = null;
            $idMachine = null;
//            throw new \Exception("gravure sans id produit");
        } else {
            $idProduct = $Product['id'];
            $idMachine = $this->container->get('repositories.product')->findMachineByIdProduct($idProduct);
        }

        //si il n'y a pas de machine liée à la catégorie, on renseigne la machine par défaut
        if($idMachine == null){
            $idMachine = $this->container->get('repositories.machine')->getDefaultId();
        }

        $path_jpg = $this->container->get('creator.link.file')->createJpg($id_config, $productId); //creation des liens de l'image
        $path_pdf = $this->container->get('creator.link.file')->createPdf($id_config, $productId); //creation des liens de l'image


        //ajoute en fonction de la quantite
        for ($i = 0; $i < $quantity; $i++) {

            $gravure = Gravure::addGravure($idProduct, $idOrder, $idMachine, $id_config, $path_jpg, $path_pdf);

            self::addListGravure($gravure);
        }

    }

    public function createGravures(Order $order, $arrayConfigCart)
    {
        //permet de récupérer notre instance Order avec son id
        $Order = $this->container->get('repositories.order')->findByIdPrestashop($order->getIdPrestashop());
        var_dump($Order);
        //Si il n'y a qu'un seul produit gravé dans le panier
        if (isset($arrayConfigCart['id'])) {

            $id_config = $arrayConfigCart['id'];
            var_dump($id_config);
            $productId = $arrayConfigCart['id_product'];
            var_dump($productId);
            $quantity = $arrayConfigCart['quantity'];
            var_dump($quantity);

            self::createGravureFromQuantity($id_config, $productId, $quantity, $Order['id']);
        } //sinon on doit récupérer chaque produit
        else {
            foreach ($arrayConfigCart as $key => $config_cart) {

                $id_config = $arrayConfigCart[$key]['id'];
                var_dump($id_config);
                $productId = $arrayConfigCart[$key]['id_product'];
                var_dump($productId);
                $quantity = $arrayConfigCart[$key]['quantity'];
                var_dump($quantity);

                self::createGravureFromQuantity($id_config, $productId, $quantity, $Order['id']);
            }
        }

        return  $this->listGravure;
    }

}