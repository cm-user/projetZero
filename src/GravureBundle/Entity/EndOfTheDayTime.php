<?php

namespace GravureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EndOfTheDayTime
 *
 * @ORM\Table(name="gravure_end_of_the_day_time")
 * @ORM\Entity(repositoryClass="GravureBundle\Repository\EndOfTheDayTimeRepository")
 */
class EndOfTheDayTime
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
     * @var \DateTime
     *
     * @ORM\Column(name="hours", type="time")
     */
    private $hours;

    /**
     * @var string
     *
     * @ORM\Column(name="last_speaker", type="string", length=255)
     */
    private $lastSpeaker;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hours
     *
     * @param \DateTime $hours
     *
     * @return EndOfTheDayTime
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return \DateTime
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @return string
     */
    public function getLastSpeaker()
    {
        return $this->lastSpeaker;
    }

    /**
     * @param string $lastSpeaker
     */
    public function setLastSpeaker($lastSpeaker)
    {
        $this->lastSpeaker = $lastSpeaker;
    }



    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return EndOfTheDayTime
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
     * @return EndOfTheDayTime
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
}

