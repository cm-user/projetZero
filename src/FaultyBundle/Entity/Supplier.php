<?php

namespace FaultyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Supplier
 *
 * @ORM\Table(name="supplier")
 * @ORM\Entity(repositoryClass="FaultyBundle\Repository\SupplierRepository")
 */
class Supplier
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="IdPrestashop", type="integer")
     */
    private $idPrestashop;

//    /**
//     * @var Product
//     *
//     * @ORM\OneToMany(targetEntity="FaultyBundle\Entity\Product", mappedBy="supplier")
//     */
//    protected $products;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Supplier
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set idPrestashop
     *
     * @param integer $idPrestashop
     *
     * @return Supplier
     */
    public function setIdPrestashop($idPrestashop)
    {
        $this->idPrestashop = $idPrestashop;

        return $this;
    }

    /**
     * Get idPrestashop
     *
     * @return int
     */
    public function getIdPrestashop()
    {
        return $this->idPrestashop;
    }
    

//    /**
//     * Constructor
//     */
//    public function __construct()
//    {
//        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
//    }
//
//    /**
//     * Add product
//     *
//     * @param \FaultyBundle\Entity\Product $product
//     *
//     * @return Supplier
//     */
//    public function addProduct(\FaultyBundle\Entity\Product $product)
//    {
//        $this->products[] = $product;
//
//        return $this;
//    }
//
//    /**
//     * Remove product
//     *
//     * @param \FaultyBundle\Entity\Product $product
//     */
//    public function removeProduct(\FaultyBundle\Entity\Product $product)
//    {
//        $this->products->removeElement($product);
//    }
//
//    /**
//     * Get products
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getProducts()
//    {
//        return $this->products;
//    }
}
