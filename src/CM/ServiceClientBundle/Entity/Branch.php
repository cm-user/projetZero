<?php

namespace CM\ServiceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Branch
 *
 * @ORM\Table(name="branch")
 * @ORM\Entity
 */
class Branch
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
     */
    private $rank;

//    /**
//     * @var branches
//     *
//     * @ORM\OneToMany(targetEntity="CM\ServiceClientBundle\Entity\Branch_child", mappedBy="branch_parent")
//     */
//    protected $branches;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CM\ServiceClientBundle\Entity\Solution", mappedBy="branche")
     */
    protected $solutions;

    /**
     * @ORM\OneToMany(targetEntity="CM\ServiceClientBundle\Entity\Branch", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="CM\ServiceClientBundle\Entity\Branch", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;


    public function __toString()
    {
        return strval($this->id);
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Branch
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     *
     * @return Branch
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->branches = new \Doctrine\Common\Collections\ArrayCollection();
        $this->solutions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add branch
     *
     * @param \CM\ServiceClientBundle\Entity\Branch_child $branch
     *
     * @return Branch
     */
    public function addBranch(\CM\ServiceClientBundle\Entity\Branch_child $branch)
    {
        $this->branches[] = $branch;

        return $this;
    }

    /**
     * Remove branch
     *
     * @param \CM\ServiceClientBundle\Entity\Branch_child $branch
     */
    public function removeBranch(\CM\ServiceClientBundle\Entity\Branch_child $branch)
    {
        $this->branches->removeElement($branch);
    }

    /**
     * Get branches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * Add solution
     *
     * @param \CM\ServiceClientBundle\Entity\Mail $solution
     *
     * @return Branch
     */
    public function addSolution(\CM\ServiceClientBundle\Entity\Mail $solution)
    {
        $this->solutions[] = $solution;

        return $this;
    }

    /**
     * Remove solution
     *
     * @param \CM\ServiceClientBundle\Entity\Mail $solution
     */
    public function removeSolution(\CM\ServiceClientBundle\Entity\Mail $solution)
    {
        $this->solutions->removeElement($solution);
    }

    /**
     * Get solutions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSolutions()
    {
        return $this->solutions;
    }

    /**
     * Add child
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $child
     *
     * @return Branch
     */
    public function addChild(\CM\ServiceClientBundle\Entity\Branch $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $child
     */
    public function removeChild(\CM\ServiceClientBundle\Entity\Branch $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $parent
     *
     * @return Branch
     */
    public function setParent(\CM\ServiceClientBundle\Entity\Branch $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CM\ServiceClientBundle\Entity\Branch
     */
    public function getParent()
    {
        return $this->parent;
    }
}
