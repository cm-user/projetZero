<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 15/03/2018
 * Time: 11:23
 */

namespace GravureBundle\Entity\Domain;


use GravureBundle\Form\MachineSubmission;

class Machine
{

    private $id;
    private $name;
    private $type;

    /**
     * Machine constructor.
     * @param string $name
     */
    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }


    public static function addMachine(MachineSubmission $machineSubmission){

        return new self($machineSubmission->name, $machineSubmission->type);

    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['name'],
            $data['type']
        );
    }



}