<?php

namespace CM\ServiceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Mail
 *
 * @ORM\Entity
 * @ORM\Table(name="mail")
 */
class Mail
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
     * @var string
     *
     * @ORM\Column(name="text_mail", type="text")
     */
    private $textMail;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="CM\ServiceClientBundle\Entity\Solution", inversedBy="mails")
     * @ORM\JoinTable(name="sc_mail_solution")
     */
    protected $solutions;

    public function __construct()
    {
        $this->solutions = new \Doctrine\Common\Collections\ArrayCollection();

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
     * Set textMail
     *
     * @param string $textMail
     *
     * @return Mail
     */
    public function setTextMail($textMail)
    {
        $this->textMail = $textMail;

        return $this;
    }

    /**
     * Get textMail
     *
     * @return string
     */
    public function getTextMail()
    {
        return $this->textMail;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Mail
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get textMail
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set solutions
     *
     * @param \CM\ServiceClientBundle\Entity\Solution $solutions
     *
     * @return Mail
     */
    public function setSolutions(\CM\ServiceClientBundle\Entity\Solution $solutions = null)
    {
        $this->solutions = $solutions;

        return $this;
    }

    /**
     * Get solutions
     *
     * @return \CM\ServiceClientBundle\Entity\Solution
     */
    public function getSolutions()
    {
        return $this->solutions;
    }
    

    /**
     * Add solution
     *
     * @param \CM\ServiceClientBundle\Entity\Solution $solution
     *
     * @return Mail
     */
    public function addSolution(\CM\ServiceClientBundle\Entity\Solution $solution)
    {
        $this->solutions[] = $solution;        

        return $this;
    }

    /**
     * Remove solution
     *
     * @param \CM\ServiceClientBundle\Entity\Solution $solution
     */
    public function removeSolution(\CM\ServiceClientBundle\Entity\Solution $solution)
    {
        $this->solutions->removeElement($solution);
    }


}
