<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/03/2018
 * Time: 12:20
 */

namespace GravureBundle\Entity\Domain;


use GravureBundle\Form\CategorySubmission;

class Category
{
    private $id;
    private $idMachine;
    private $surname;
    private $folder;
    private $nameGabarit;
    private $pathGabarit;
    private $maxGabarit;
    private $createdAt;
    private $updatedAt;

    /**
     * Category constructor.
     * @param $surname
     * @param $folder
     * @param $pathGabarit
     * @param $maxGabarit
     */
    public function __construct($idMachine, $surname, $folder, $nameGabarit, $pathGabarit, $maxGabarit)
    {
        $this->idMachine = $idMachine;
        $this->surname = $surname;
        $this->folder = $folder;
        $this->nameGabarit = $nameGabarit;
        $this->pathGabarit = $pathGabarit;
        $this->maxGabarit = $maxGabarit;
    }

    public static function addCategory(CategorySubmission $categorySubmission){

        return new self($categorySubmission->idMachine, $categorySubmission->surname, $categorySubmission->folder, $categorySubmission->nameGabarit, $categorySubmission->pathGabarit, $categorySubmission->maxGabarit);

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
    public function getIdMachine()
    {
        return $this->idMachine;
    }

    /**
     * @param mixed $idMachine
     */
    public function setIdMachine($idMachine)
    {
        $this->idMachine = $idMachine;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return mixed
     */
    public function getNameGabarit()
    {
        return $this->nameGabarit;
    }

    /**
     * @param mixed $nameGabarit
     */
    public function setNameGabarit($nameGabarit)
    {
        $this->nameGabarit = $nameGabarit;
    }

    /**
     * @return mixed
     */
    public function getPathGabarit()
    {
        return $this->pathGabarit;
    }

    /**
     * @param mixed $pathGabarit
     */
    public function setPathGabarit($pathGabarit)
    {
        $this->pathGabarit = $pathGabarit;
    }

    /**
     * @return mixed
     */
    public function getMaxGabarit()
    {
        return $this->maxGabarit;
    }

    /**
     * @param mixed $maxGabarit
     */
    public function setMaxGabarit($maxGabarit)
    {
        $this->maxGabarit = $maxGabarit;
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
            $data['id_machine'],
            $data['surname'],
            $data['folder'],
            $data['name_gabarit'],
            $data['path_gabarit'],
            $data['max_gabarit']
        );
    }



}

