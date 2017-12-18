<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 01/12/2017
 * Time: 10:59
 */

namespace CM\EngravingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IdProduct
 *
 * @ORM\Table(name="engraving_idproduct")
 * @ORM\Entity
 */
class IdProduct
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
     * @ORM\ManyToOne(targetEntity="CM\EngravingBundle\Entity\Category", inversedBy="idProducts" , cascade={"persist"})
     * @ORM\JoinColumn(name="id_product", referencedColumnName="id", nullable=true)
     *
     */
    private $idProduct;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;
    }
}
