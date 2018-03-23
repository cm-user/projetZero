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

    public function createGravureFromQuantity($id_config, $productId, $quantity, $idOrder)
    {

        //////Methode pour récupérer l'id produit de chaque gravure pour trouver son produit lié///////
        $product = $this->container->get('repositories.product')->findByProductId($productId);
        if ($product == null) {
            $idProduct = null;
        } else {
            $idProduct = $product->getId();
        }

        //ajoute en fonction de la quantite
        for ($i = 0; $i < $quantity; $i++) {

            $gravure = Gravure::addGravure($idProduct, $idOrder, $id_config);

            self::addListGravure($gravure);
        }

    }

    public function createGravures(Order $order, $arrayConfigCart)
    {

        $order = $this->container->get('repositories.order')->findByIdPrestashop($order->getIdPrestashop());
        var_dump($order);
        //Si il n'y a qu'un seul produit gravé dans le panier
        if (isset($arrayConfigCart['id'])) {

            $id_config = $arrayConfigCart['id'];
            $productId = $arrayConfigCart['id_product'];
            $quantity = $arrayConfigCart['quantity'];

            self::createGravureFromQuantity($id_config, $productId, $quantity, $order->getId());
        } //sinon on doit récupérer chaque produit
        else {
            foreach ($arrayConfigCart as $key => $config_cart) {

                $id_config = $arrayConfigCart[$key]['id'];
                $productId = $arrayConfigCart[$key]['id_product'];
                $quantity = $arrayConfigCart[$key]['quantity'];

                self::createGravureFromQuantity($id_config, $productId, $quantity, $order->getId());
            }
        }

        return  $this->listGravure;
    }

}