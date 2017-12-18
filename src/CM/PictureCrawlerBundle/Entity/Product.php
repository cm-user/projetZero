<?php

namespace FaultyProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="faulty_product")
 * @ORM\Entity
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_prestashop_product", type="integer", nullable=true)
     */
    protected $idPrestashopProduct;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    protected $link;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="string", length=255)
     */
    protected $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price", type="string", length=255, nullable=true)
     */
    protected $costPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_reference", type="string", length=255, nullable=true)
     */
    protected $supplierReference;

    // Mapping
    /**
     * @var Faulty
     *
     * @ORM\OneToOne(targetEntity="FaultyProductBundle\Entity\Faulty", inversedBy="product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="faulty_id", referencedColumnName="id", unique=true)
     * })
     */
    protected $faulty;

    /**
     * @var Supplier
     *
     * @ORM\ManyToOne(targetEntity="FaultyProductBundle\Entity\Supplier", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     * })
     */
    protected $supplier;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FaultyProductBundle\Entity\ProductImage", mappedBy="product", cascade={"persist"})
     */
    protected $productImages;

    /**
     * @var string
     */
    protected $search;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->productImages = new ArrayCollection();
    }

    public static function locationName(){
        return [
            'Chez Cadeau Maestro' => 'at_warehouse',
            'Chez le client' => 'at_customer'
        ];
    }

    public function getFormattedLocation(){
        $array = [
            ''  => 'Non renseigné',
            'at_warehouse' => 'Chez Cadeau Maestro',
            'at_customer' => 'Chez le client',
        ];
        return $array[$this->getLocation()];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getIdPrestashopProduct()
    {
        return $this->idPrestashopProduct;
    }

    /**
     * @param int $idPrestashopProduct
     */
    public function setIdPrestashopProduct($idPrestashopProduct)
    {
        $this->idPrestashopProduct = $idPrestashopProduct;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * @param string $price
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return Faulty
     */
    public function getFaulty()
    {
        return $this->faulty;
    }

    /**
     * @param Faulty $faulty
     */
    public function setFaulty($faulty)
    {
        $this->faulty = $faulty;
    }

    /**
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param Faulty $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param string $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * @return string
     */
    public function getSupplierReference()
    {
        return $this->supplierReference;
    }

    /**
     * @param string $supplierReference
     */
    public function setSupplierReference($supplierReference)
    {
        $this->supplierReference = $supplierReference;
    }

    /**
     * @return ArrayCollection
     */
    public function getProductImages()
    {
        return $this->productImages;
    }

    /**
     * @param ArrayCollection $productImages
     */
    public function setProductImages($productImages)
    {
        $this->productImages = $productImages;
    }
}