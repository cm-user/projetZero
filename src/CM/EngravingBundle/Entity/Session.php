<?php

namespace CM\EngravingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @ORM\Table(name="engraving_session")
 * @ORM\Entity
 */
class Session
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
     * @ORM\Column(name="time_total", type="integer")
     */
    private $timeTotal;
    
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
     * @ORM\OneToMany(targetEntity="CM\EngravingBundle\Entity\Picture", mappedBy="session")
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
     * Set name
     *
     * @param string $name
     *
     * @return Session
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Session
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
     * @return Session
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
     * Add picture
     *
     * @param \CM\EngravingBundle\Entity\Picture $picture
     *
     * @return Session
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
     * Set timeTotal
     *
     * @param integer $timeTotal
     *
     * @return Session
     */
    public function setTimeTotal($timeTotal)
    {
        $this->timeTotal = $timeTotal;

        return $this;
    }

    /**
     * Get timeTotal
     *
     * @return integer
     */
    public function getTimeTotal()
    {
        return $this->timeTotal;
    }
}
