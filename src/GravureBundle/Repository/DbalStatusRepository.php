<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 10:23
 */

namespace GravureBundle\Repository;


use Doctrine\DBAL\Connection;

class DbalStatusRepository
{

    private $connection;

    /**
     * DbalTextRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    private function hydrateFromRow($data){
        return $data['name'];
    }
}