<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 11:13
 */

namespace GravureBundle\Entity\Domain;


class Order
{

    private $id;
    private $box;
    private $gift;
    private $engrave;
    private $checked;
    private $idPrestashop;
    private $statePrestashop;
    private $datePrestashop;
    private $createdAt;
    private $updatedAt;

    /**
     * Order constructor.
     * @param $box
     * @param $gift
     * @param $engrave
     * @param $checked
     * @param $idPrestashop
     * @param $statePrestashop
     * @param $datePrestashop
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($box, $gift, $engrave, $checked , $idPrestashop, $statePrestashop, $datePrestashop, $createdAt, $updatedAt)
    {
        $this->box = $box;
        $this->gift = $gift;
        $this->engrave = $engrave;
        $this->checked = $checked;
        $this->idPrestashop = $idPrestashop;
        $this->statePrestashop = $statePrestashop;
        $this->datePrestashop = $datePrestashop;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function addOrder($gift, $idPrestashop, $statePrestashop, $datePrestashop)
    {
        $createdAt = (new \DateTime())->format('Y-m-d h:m:s');
        $updatedAt = (new \DateTime())->format('Y-m-d h:m:s');
        return new self(null, $gift, 0,0, $idPrestashop, $statePrestashop, $datePrestashop, $createdAt, $updatedAt);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * @param mixed $box
     */
    public function setBox($box)
    {
        $this->box = $box;
    }

    /**
     * @return mixed
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * @param mixed $gift
     */
    public function setGift($gift)
    {
        $this->gift = $gift;
    }

    /**
     * @return mixed
     */
    public function getEngrave()
    {
        return $this->engrave;
    }

    /**
     * @param mixed $engrave
     */
    public function setEngrave($engrave)
    {
        $this->engrave = $engrave;
    }

    /**
     * @return mixed
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * @param mixed $checked
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    /**
     * @return mixed
     */
    public function getIdPrestashop()
    {
        return $this->idPrestashop;
    }

    /**
     * @param mixed $idPrestashop
     */
    public function setIdPrestashop($idPrestashop)
    {
        $this->idPrestashop = $idPrestashop;
    }

    /**
     * @return mixed
     */
    public function getStatePrestashop()
    {
        return $this->statePrestashop;
    }

    /**
     * @param mixed $statePrestashop
     */
    public function setStatePrestashop($statePrestashop)
    {
        $this->statePrestashop = $statePrestashop;
    }

    /**
     * @return mixed
     */
    public function getDatePrestashop()
    {
        return $this->datePrestashop;
    }

    /**
     * @param mixed $datePrestashop
     */
    public function setDatePrestashop($datePrestashop)
    {
        $this->datePrestashop = $datePrestashop;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['box'],
            $data['gift'],
            $data['engrave'],
            $data['checked'],
            $data['id_prestashop'],
            $data['state_prestashop'],
            $data['date_prestashop'],
            $data['created_at'],
            $data['updated_at']
        );
    }

}