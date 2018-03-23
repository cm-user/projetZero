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

    /**
     * Text constructor.
     * @param $nameBlock
     * @param $value
     */
    public function __construct($nameBlock, $value)
    {
        $this->nameBlock = $nameBlock;
        $this->value = $value;
    }

    public static function addText($nameBlock, $value){

        return new self($nameBlock, $value);
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
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['name_block'],
            $data['value']
        );
    }

}