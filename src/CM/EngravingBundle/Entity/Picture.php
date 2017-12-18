<?php

namespace CM\EngravingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Picture
 *
 * @ORM\Table(name="engraving_picture")
 * @ORM\Entity
 */
class Picture
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
     * @var string
     *
     * @ORM\Column(name="path_jpg", type="string", length=255)
     */
    private $pathJpg;

    /**
     * @var string
     *
     * @ORM\Column(name="path_pdf", type="string", length=255)
     */
    private $pathPdf;

    /**
     * @var string
     *
     * @ORM\Column(name="id_product", type="string", length=255)
     */
    private $idProduct;

    /**
     * @var string
     *
     * @ORM\Column(name="id_config", type="string", length=255)
     */
    private $idConfig;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", options={"default":1}, nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="machine", type="string", length=255, nullable=true)
     */
    private $machine;

    /**
     * @var int
     *
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var int
     *
     * @ORM\Column(name="time", type="integer", options={"default":0}, nullable=true)
     */
    private $time;

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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="CM\EngravingBundle\Entity\Category", inversedBy="pictures")
     * @ORM\JoinColumn(name="id_category", referencedColumnName="id", nullable=true)
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="CM\EngravingBundle\Entity\Session", inversedBy="pictures" , cascade={"persist"})
     * @ORM\JoinColumn(name="id_session", referencedColumnName="id", nullable=true)
     */
    private $session;

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
     * Set name
     *
     * @param string $name
     *
     * @return Picture
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
     * Set idProduct
     *
     * @param string $idProduct
     *
     * @return Picture
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
     * @return Picture
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
     * @return Picture
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
     * @param \DateTime $updatedAt
     *
     * @return Picture
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set category
     *
     * @param \CM\EngravingBundle\Entity\Category $category
     *
     * @return Picture
     */
    public function setCategory(\CM\EngravingBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \CM\EngravingBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set session
     *
     * @param \CM\EngravingBundle\Entity\Session $session
     *
     * @return Picture
     */
    public function setSession(\CM\EngravingBundle\Entity\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return \CM\EngravingBundle\Entity\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set pathJpg
     *
     * @param string $pathJpg
     *
     * @return Picture
     */
    public function setPathJpg($pathJpg)
    {
        $this->pathJpg = $pathJpg;

        return $this;
    }

    /**
     * Get pathJpg
     *
     * @return string
     */
    public function getPathJpg()
    {
        return $this->pathJpg;
    }

    /**
     * Set pathPdf
     *
     * @param string $pathPdf
     *
     * @return Picture
     */
    public function setPathPdf($pathPdf)
    {
        $this->pathPdf = $pathPdf;

        return $this;
    }

    /**
     * Get pathPdf
     *
     * @return string
     */
    public function getPathPdf()
    {
        return $this->pathPdf;
    }

    /**
     * Set idConfig
     *
     * @param string $idConfig
     *
     * @return Picture
     */
    public function setIdConfig($idConfig)
    {
        $this->idConfig = $idConfig;

        return $this;
    }

    /**
     * Get idConfig
     *
     * @return string
     */
    public function getIdConfig()
    {
        return $this->idConfig;
    }

    /**
     * Set machine
     *
     * @param string $machine
     *
     * @return Picture
     */
    public function setMachine($machine)
    {
        $this->machine = $machine;

        return $this;
    }

    /**
     * Get machine
     *
     * @return string
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Picture
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     *
     * @return Picture
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return integer
     */
    public function getEtat()
    {
        return $this->etat;
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
}
