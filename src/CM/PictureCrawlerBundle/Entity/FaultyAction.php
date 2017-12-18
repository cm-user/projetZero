<?php

namespace FaultyProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FaultyAction
 *
 * @ORM\Table(name="faulty_action")
 * @ORM\Entity
 */
class FaultyAction
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
     * @ORM\Column(name="message", type="string")
     */
    protected $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var Faulty
     *
     * @ORM\ManyToOne(targetEntity="FaultyProductBundle\Entity\Faulty", inversedBy="faultyActions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="faulty_id", referencedColumnName="id")
     * })
     */
    protected $faulty;

    /**
     * FaultyAction constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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


}