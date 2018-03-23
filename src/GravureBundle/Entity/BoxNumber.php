<?php

namespace GravureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoxNumber
 *
 * @ORM\Table(name="gravure_box_number")
 * @ORM\Entity(repositoryClass="GravureBundle\Repository\BoxNumberRepository")
 */
class BoxNumber
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
     * @ORM\Column(name="box_column", type="integer")
     */
    private $boxColumn;

    /**
     * @var int
     *
     * @ORM\Column(name="box_row", type="integer")
     */
    private $boxRow;

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
     * BoxNumber constructor.
     *
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
     * Set boxColumn
     *
     * @param integer $boxColumn
     *
     * @return BoxNumber
     */
    public function setBoxColumn($boxColumn)
    {
        $this->boxColumn = $boxColumn;

        return $this;
    }

    /**
     * Get boxColumn
     *
     * @return int
     */
    public function getBoxColumn()
    {
        return $this->boxColumn;
    }

    /**
     * Set boxRow
     *
     * @param integer $boxRow
     *
     * @return BoxNumber
     */
    public function setBoxRow($boxRow)
    {
        $this->boxRow = $boxRow;

        return $this;
    }

    /**
     * Get boxRow
     *
     * @return int
     */
    public function getBoxRow()
    {
        return $this->boxRow;
    }

    /**
     * Set lastSpeaker
     *
     * @param string $lastSpeaker
     *
     * @return BoxNumber
     */
    public function setLastSpeaker($lastSpeaker)
    {
        $this->lastSpeaker = $lastSpeaker;

        return $this;
    }

    /**
     * Get lastSpeaker
     *
     * @return string
     */
    public function getLastSpeaker()
    {
        return $this->lastSpeaker;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BoxNumber
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
     * @return BoxNumber
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

