<?php
//
//namespace CM\EngravingBundle\Entity;
//
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * Category
// *
// * @ORM\Table(name="engraving_category")
// * @ORM\Entity
// */
//class Category
//{
//    /**
//     * @var int
//     *
//     * @ORM\Column(name="id", type="integer")
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="AUTO")
//     */
//    private $id;
//
//    /**
//     *
//     * @ORM\OneToMany(targetEntity="CM\EngravingBundle\Entity\IdProduct", mappedBy="idProduct")
//     *
//     */
//    private $idProducts;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="surname", type="string", length=255)
//     */
//    private $surname;
//
//    /**
//     * @var int
//     *
//     * @ORM\Column(name="time", type="integer", options={"default":0}, nullable=true)
//     */
//    private $time;
//
//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="created_at", type="datetime")
//     */
//    private $createdAt;
//
//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="updated_at", type="datetime")
//     */
//    private $updatedAt;
//
//    /**
//     * @ORM\OneToMany(targetEntity="CM\EngravingBundle\Entity\Picture", mappedBy="category")
//     */
//    private $pictures;
//
//    /**
//     * Constructor
//     */
//    public function __construct()
//    {
//        $this->createdAt = new \DateTime();
//        $this->updatedAt = new \DateTime();
//        $this->idProducts = new ArrayCollection();
//    }
//
//
//    /**
//     * Get id
//     *
//     * @return int
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
////    /**
////     * Set idProduct
////     *
////     * @param string $idProduct
////     *
////     * @return Category
////     */
////    public function setIdProduct($idProduct)
////    {
////        $this->idProduct = $idProduct;
////
////        return $this;
////    }
//
//    /**
//     * Get idProducts
//     *
//     * @return string
//     */
//    public function getIdProducts()
//    {
//        return $this->idProducts;
//    }
//
//    /**
//     * Set surname
//     *
//     * @param string $surname
//     *
//     * @return Category
//     */
//    public function setSurname($surname)
//    {
//        $this->surname = $surname;
//
//        return $this;
//    }
//
//    /**
//     * Get surname
//     *
//     * @return string
//     */
//    public function getSurname()
//    {
//        return $this->surname;
//    }
//
//    /**
//     * Set createdAt
//     *
//     * @param \DateTime $createdAt
//     *
//     * @return Category
//     */
//    public function setCreatedAt($createdAt)
//    {
//        $this->createdAt = $createdAt;
//
//        return $this;
//    }
//
//    /**
//     * Get createdAt
//     *
//     * @return \DateTime
//     */
//    public function getCreatedAt()
//    {
//        return $this->createdAt;
//    }
//
//    /**
//     * Set updatedAt
//     *
//     * @param string $updatedAt
//     *
//     * @return Category
//     */
//    public function setUpdatedAt($updatedAt)
//    {
//        $this->updatedAt = $updatedAt;
//
//        return $this;
//    }
//
//    /**
//     * Get updatedAt
//     *
//     * @return string
//     */
//    public function getUpdatedAt()
//    {
//        return $this->updatedAt;
//    }
//
//    /**
//     * Add picture
//     *
//     * @param \CM\EngravingBundle\Entity\Picture $picture
//     *
//     * @return Category
//     */
//    public function addPicture(\CM\EngravingBundle\Entity\Picture $picture)
//    {
//        $this->pictures[] = $picture;
//
//        return $this;
//    }
//
//    /**
//     * Remove picture
//     *
//     * @param \CM\EngravingBundle\Entity\Picture $picture
//     */
//    public function removePicture(\CM\EngravingBundle\Entity\Picture $picture)
//    {
//        $this->pictures->removeElement($picture);
//    }
//
//    /**
//     * Get pictures
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getPictures()
//    {
//        return $this->pictures;
//    }
//
//    /**
//     * Set time
//     *
//     * @param integer $time
//     *
//     * @return Category
//     */
//    public function setTime($time)
//    {
//        $this->time = $time;
//
//        return $this;
//    }
//
//    /**
//     * Get time
//     *
//     * @return integer
//     */
//    public function getTime()
//    {
//        return $this->time;
//    }
//
//    /**
//     * Set idProducts
//     *
//     * @param string $idProducts
//     *
//     * @return Category
//     */
//    public function setIdProducts($idProducts)
//    {
//        $this->idProducts = $idProducts;
//
//        return $this;
//    }
//
//    /**
//     * Add idProduct
//     *
//     * @param \CM\EngravingBundle\Entity\IdProduct $idProduct
//     *
//     * @return Category
//     */
//    public function addIdProduct(\CM\EngravingBundle\Entity\IdProduct $idProduct)
//    {
//        $this->idProducts[] = $idProduct;
//
//        return $this;
//    }
//
//    /**
//     * Remove idProduct
//     *
//     * @param \CM\EngravingBundle\Entity\IdProduct $idProduct
//     */
//    public function removeIdProduct(\CM\EngravingBundle\Entity\IdProduct $idProduct)
//    {
//        $this->idProducts->removeElement($idProduct);
//    }
//}



namespace CM\EngravingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="engraving_category")
 * @ORM\Entity
 */
class Category
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
     * @ORM\Column(name="id_product", type="string", length=255, options={"default":null})
     */
    private $idProduct;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var int
     *
     * @ORM\Column(name="time", type="integer", options={"default":0}, nullable=true)
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", length=255)
     */
    private $folder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="CM\EngravingBundle\Entity\Picture", mappedBy="category")
     */
    private $pictures;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }


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
     * Set idProduct
     *
     * @param string $idProduct
     *
     * @return Category
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return string
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Category
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Category
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param string $updatedAt
     *
     * @return Category
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add picture
     *
     * @param \CM\EngravingBundle\Entity\Picture $picture
     *
     * @return Category
     */
    public function addPicture(\CM\EngravingBundle\Entity\Picture $picture)
    {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture
     *
     * @param \CM\EngravingBundle\Entity\Picture $picture
     */
    public function removePicture(\CM\EngravingBundle\Entity\Picture $picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return Category
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return Category
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
