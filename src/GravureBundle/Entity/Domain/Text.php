<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 09:58
 */

namespace GravureBundle\Entity\Domain;


class Text
{
    private $id;
    private $nameBlock;
    private $value;
    private $createdAt;
    private $updatedAt;

    /**
     * Text constructor.
     * @param $nameBlock
     * @param $value
     */
    public function __construct($nameBlock, $value, $createdAt, $updatedAt)
    {
        $this->nameBlock = $nameBlock;
        $this->value = $value;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function addText($nameBlock, $value){
        $createdAt = (new \DateTime())->format('Y-m-d h:m:s');
        $updatedAt = (new \DateTime())->format('Y-m-d h:m:s');
        return new self($nameBlock, $value, $createdAt, $updatedAt);
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
    public function getNameBlock()
    {
        return $this->nameBlock;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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
            $data['name_block'],
            $data['value'],
            $data['created_at'],
            $data['updated_at']
        );
    }

}