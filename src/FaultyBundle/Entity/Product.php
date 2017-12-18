<?php

namespace FaultyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="FaultyBundle\Repository\ProductRepository")
 */
class Product
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
     * @var Faulty
     *
     * @ORM\OneToOne(targetEntity="FaultyBundle\Entity\Faulty", inversedBy="product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="faulty_id", referencedColumnName="id", unique=true)
     * })
     */
    protected $faulty;

    /**
     * @var Supplier
     *
     * @ORM\ManyToOne(targetEntity="FaultyBundle\Entity\Supplier", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     * })
     */
    protected $supplier;
    

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
     * @return Product
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
     * Set faulty
     *
     * @param \FaultyBundle\Entity\Faulty $faulty
     *
     * @return Product
     */
    public function setFaulty(\FaultyBundle\Entity\Faulty $faulty = null)
    {
        $this->faulty = $faulty;

        return $this;
    }

    /**
     * Get faulty
     *
     * @return \FaultyBundle\Entity\Faulty
     */
    public function getFaulty()
    {
        return $this->faulty;
    }

    /**
     * Set supplier
     *
     * @param \FaultyBundle\Entity\Supplier $supplier
     *
     * @return Product
     */
    public function setSupplier(\FaultyBundle\Entity\Supplier $supplier = null)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return \FaultyBundle\Entity\Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }
}
