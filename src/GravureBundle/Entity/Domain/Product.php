<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 21/03/2018
 * Time: 10:27
 */

namespace GravureBundle\Entity\Domain;


use GravureBundle\Form\ProductSubmission;

class Product
{

    private $id;
    private $idCategory;
    private $productId;
    private $time;
    private $alias;

    /**
     * Product constructor.
     * @param $idCategory
     * @param $productId
     * @param $time
     * @param $alias
     */
    public function __construct($idCategory, $productId, $time, $alias)
    {
        $this->idCategory = $idCategory;
        $this->productId = $productId;
        $this->time = $time;
        $this->alias = $alias;
    }


    public static function addProduct(ProductSubmission $productSubmission){

        return new self($productSubmission->idCategory, $productSubmission->productId, $productSubmission->time, $productSubmission->alias);
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
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * @param mixed $idCategory
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    

    /**
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['id_category'],
            $data['product_id'],
            $data['time'],
            $data['alias']
        );
    }
}