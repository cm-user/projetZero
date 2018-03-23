<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 13:45
 */

namespace GravureBundle\Entity\Domain;


use GravureBundle\Entity\Utils\CreatorLinkFile;

class Gravure
{
    private $id;
    private $idProduct;
    private $idSession;
    private $idOrder;
    private $idMachine;
    private $idStatus;
    private $pathJpg;
    private $pathPdf;
    private $configId;
    private $position;
    private $createdAt;
    private $updatedAt;

    /**
     * Gravure constructor.
     * @param $idProduct
     * @param $idSession
     * @param $idOrder
     * @param $idMachine
     * @param $idStatus
     * @param $pathJpg
     * @param $pathPdf
     * @param $configId
     * @param $position
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($idProduct, $idSession, $idOrder, $idMachine, $idStatus, $pathJpg, $pathPdf, $configId, $position, $createdAt, $updatedAt)
    {
        $this->idProduct = $idProduct;
        $this->idSession = $idSession;
        $this->idOrder = $idOrder;
        $this->idMachine = $idMachine;
        $this->idStatus = $idStatus;
        $this->pathJpg = $pathJpg;
        $this->pathPdf = $pathPdf;
        $this->configId = $configId;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }


    public static function addGravure($idProduct, $idOrder, $config_id)
    {
        $creatorLinkFile = new CreatorLinkFile();
        $createdAt = (new \DateTime())->format('Y-m-d h:m:s');
        $updatedAt = (new \DateTime())->format('Y-m-d h:m:s');
        $path_jpg = $creatorLinkFile->createJpg($config_id, $idProduct);
        $path_pdf = $creatorLinkFile->createPdf($config_id, $idProduct);
        return new self($idProduct, null, $idOrder, null, 1, $path_jpg, $path_pdf, $config_id, null, $createdAt, $updatedAt);
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
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * @return mixed
     */
    public function getIdSession()
    {
        return $this->idSession;
    }

    /**
     * @return mixed
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * @return mixed
     */
    public function getIdMachine()
    {
        return $this->idMachine;
    }

    /**
     * @return mixed
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * @return mixed
     */
    public function getPathJpg()
    {
        return $this->pathJpg;
    }

    /**
     * @return mixed
     */
    public function getPathPdf()
    {
        return $this->pathPdf;
    }

    /**
     * @return mixed
     */
    public function getConfigId()
    {
        return $this->configId;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * This method should be used only to hydrate object from a persistent storage
     * and never to create / sign up a Member.
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['id_product'],
            $data['id_session'],
            $data['id_order'],
            $data['id_machine'],
            $data['id_status'],
            $data['path_jpg'],
            $data['path_pdf'],
            $data['config_id'],
            $data['position'],
            $data['created_at'],
            $data['updated_at']
        );
    }
}