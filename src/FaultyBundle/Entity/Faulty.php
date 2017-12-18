<?php

namespace FaultyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Faulty
 *
 * @ORM\Table(name="faulty")
 * @ORM\Entity(repositoryClass="FaultyBundle\Repository\FaultyRepository")
 */
class Faulty
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
     * @var int
     *
     * @ORM\Column(name="IdOrder", type="integer")
     */
    private $idOrder;

    /**
     * @var Product
     *
     * @ORM\OneToOne(targetEntity="FaultyBundle\Entity\Product", mappedBy="faulty", cascade={"all"})
     */
    protected $product;


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
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return Faulty
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return int
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set product
     *
     * @param \FaultyProductBundle\Entity\Product $product
     *
     *
     */
    public function setProduct($product = null)
    {
        $this->product = $product;
    }

    /**
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
