<?php

namespace FaultyProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Faulty
 *
 * @ORM\Table(name="faulty")
 * @ORM\Entity
 */
class Faulty
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
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=true)
     */
    protected $idOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string")
     */
    protected $reason;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reception", type="boolean", nullable=true, options={"default":0})
     */
    protected $reception;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_mail_at", type="datetime", nullable=true)
     */
    protected $sendMailAt;

    // Mapping
    /**
     * @var Product
     *
     * @ORM\OneToOne(targetEntity="FaultyProductBundle\Entity\Product", mappedBy="faulty", cascade={"all"})
     */
    protected $product;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FaultyProductBundle\Entity\FaultyAction", mappedBy="faulty", cascade={"all"})
     */
    protected $faultyActions;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="FaultyProductBundle\Entity\User", inversedBy="faultys")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $user;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->faultyActions = new ArrayCollection();
    }

    public static function statusName(){
        return [
            'Nouveau' => 'new',
            'Mail envoyé au fournisseur' => 'mail_send',
            'A renvoyer par le fournisseur' => 'balance',
            'Remboursé' => 'refunded',
            'Perte' => 'loss',
            'Pièces détachées' => 'piece',
        ];
    }

    public function getFormattedStatus(){
        return array_flip(Faulty::statusName())[$this->getStatus()];
    }

    public function setStatusToNew(){
        $this->status = 'new';
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
     * @return int
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * @param int $idOrder
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;
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
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return ArrayCollection
     */
    public function getFaultyActions()
    {
        return $this->faultyActions;
    }

    /**
     * @param ArrayCollection $faultyActions
     */
    public function setFaultyActions($faultyActions)
    {
        $this->faultyActions = $faultyActions;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return \DateTime
     */
    public function getSendMailAt()
    {
        return $this->sendMailAt;
    }

    /**
     * @param \DateTime $sendMailAt
     */
    public function setSendMailAt($sendMailAt)
    {
        $this->sendMailAt = $sendMailAt;
    }

    /**
     * @return boolean
     */
    public function getReception()
    {
        return $this->reception;
    }

    /**
     * @param boolean $reception
     */
    public function setReception($reception)
    {
        $this->reception = $reception;
    }


}