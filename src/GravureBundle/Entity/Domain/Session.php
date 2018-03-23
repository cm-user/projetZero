<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 10:36
 */

namespace GravureBundle\Entity\Domain;


class Session
{
    private $id;
    private $user;
    private $gravureTotal;
    private $createdAt;
    private $updatedAt;

    /**
     * Session constructor.
     * @param $user
     * @param $gravureTotal
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($user, $gravureTotal, $createdAt, $updatedAt)
    {
        $this->user = $user;
        $this->gravureTotal = $gravureTotal;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function addSession($user, $gravureTotal)
    {
        $createdAt = (new \DateTime())->format('Y-m-d h:m:s');
        $updatedAt = (new \DateTime())->format('Y-m-d h:m:s');
        return new self($user, $gravureTotal, $createdAt, $updatedAt);
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getGravureTotal()
    {
        return $this->gravureTotal;
    }

    /**
     * @param mixed $gravureTotal
     */
    public function setGravureTotal($gravureTotal)
    {
        $this->gravureTotal = $gravureTotal;
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
            $data['user'],
            $data['gravure_total'],
            $data['created_at'],
            $data['updated_at']
        );
    }

}