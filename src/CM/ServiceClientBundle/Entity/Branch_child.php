<?php

namespace CM\ServiceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Branch_child
 *
 * @ORM\Table(name="branch_child")
 * @ORM\Entity(repositoryClass="CM\ServiceClientBundle\Repository\Branch_childRepository")
 */
class Branch_child
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
     * @var branch_parent
     *
     * @ORM\ManyToOne(targetEntity="CM\ServiceClientBundle\Entity\Branch", inversedBy="branches")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $branch_parent;

    /**
     * @ORM\OneToOne(targetEntity="CM\ServiceClientBundle\Entity\Branch", cascade={"persist"})
     */
    protected $child;

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
     * Set branchParent
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $branchParent
     *
     * @return Branch_child
     */
    public function setBranchParent(\CM\ServiceClientBundle\Entity\Branch $branchParent = null)
    {
        $this->branch_parent = $branchParent;

        return $this;
    }

    /**
     * Get branchParent
     *
     * @return \CM\ServiceClientBundle\Entity\Branch
     */
    public function getBranchParent()
    {
        return $this->branch_parent;
    }

    /**
     * Set child
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $child
     *
     * @return Branch_child
     */
    public function setChild(\CM\ServiceClientBundle\Entity\Branch $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return \CM\ServiceClientBundle\Entity\Branch
     */
    public function getChild()
    {
        return $this->child;
    }
}
