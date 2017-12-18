<?php

namespace CM\ServiceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Solution
 *
 * @ORM\Table(name="solution")
 * @ORM\Entity
 */
class Solution
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
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="text_solution", type="text")
     */
    private $textSolution;

    /**
     * @var bool
     *
     * @ORM\Column(name="mail_bool", type="boolean")
     */
    private $mailBool;

    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="CM\ServiceClientBundle\Entity\Branch", inversedBy="solutions", cascade={"persist"})
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    protected $branche;

    /**
     * @var Mail
     * @ORM\ManyToMany(targetEntity="CM\ServiceClientBundle\Entity\Mail", mappedBy="solutions")
     * @ORM\JoinColumn(name="mail_id", referencedColumnName="id", nullable=true)
     */
    protected $mails;


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
     * Set textSolution
     *
     * @param string $textSolution
     *
     * @return Solution
     */
    public function setTextSolution($textSolution)
    {
        $this->textSolution = $textSolution;

        return $this;
    }

    /**
     * Get textSolution
     *
     * @return string
     */
    public function getTextSolution()
    {
        return $this->textSolution;
    }

    /**
     * Set mailBool
     *
     * @param boolean $mailBool
     *
     * @return Solution
     */
    public function setMailBool($mailBool)
    {
        $this->mailBool = $mailBool;

        return $this;
    }

    /**
     * Get mailBool
     *
     * @return bool
     */
    public function getMailBool()
    {
        return $this->mailBool;
    }
    

    /**
     * Set solutionBranch
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $branche
     *
     * @return Solution
     */
    public function setSolutionBranch(\CM\ServiceClientBundle\Entity\Branch $branche = null)
    {
        $this->branche = $branche;

        return $this;
    }

    /**
     * Get branche
     *
     * @return \CM\ServiceClientBundle\Entity\Branch
     */
    public function getSolutionBranch()
    {
        return $this->branche;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Solution
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
     * Constructor
     */
    public function __construct()
    {
        $this->mails = new \Doctrine\Common\Collections\ArrayCollection();
//        $this->branches = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mail
     *
     * @param \CM\ServiceClientBundle\Entity\Mail $mail
     *
     * @return Solution
     */
    public function addMail(\CM\ServiceClientBundle\Entity\Mail $mail)
    {
        $this->mails[] = $mail;

        return $this;
    }

    /**
     * Remove mail
     *
     * @param \CM\ServiceClientBundle\Entity\Mail $mail
     */
    public function removeMail(\CM\ServiceClientBundle\Entity\Mail $mail)
    {
        $this->mails->removeElement($mail);
    }

    /**
     * Get mails
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     * Set branche
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $branche
     *
     * @return Solution
     */
    public function setBranche(\CM\ServiceClientBundle\Entity\Branch $branches = null)
    {
        $this->branche = $branches;

        return $this;
    }

    /**
     * Get branche
     *
     * @return \CM\ServiceClientBundle\Entity\Branch
     */
    public function getBranche()
    {
        return $this->branche;
    }

    /**
     * Add branch
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $branch
     *
     * @return Solution
     */
    public function addBranch(\CM\ServiceClientBundle\Entity\Branch $branch)
    {
        $this->branches[] = $branch;

        return $this;
    }

    /**
     * Remove branch
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $branch
     */
    public function removeBranch(\CM\ServiceClientBundle\Entity\Branch $branch)
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
     * Set branches
     *
     * @param \CM\ServiceClientBundle\Entity\Branch $branches
     *
     * @return Solution
     */
    public function setBranches(\CM\ServiceClientBundle\Entity\Branch $branches = null)
    {
        $this->branches = $branches;

        return $this;
    }

    /**
     * Get mail
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set mail
     *
     * @param \CM\ServiceClientBundle\Entity\Mail $mail
     *
     * @return Solution
     */
    public function setMail(\CM\ServiceClientBundle\Entity\Mail $mail = null)
    {
        $this->mail = $mail;

        return $this;
    }
}
