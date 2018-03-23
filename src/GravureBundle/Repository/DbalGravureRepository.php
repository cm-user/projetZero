<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 12:16
 */

namespace GravureBundle\Repository;

use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Gravure;

class DbalGravureRepository
{
    private $connection;

    /**
     * DbalGravureRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(){
        $gravures = $this->connection->fetchAll('SELECT * FROM gravure');

        return $gravures;
    }

    public function save(Gravure $gravure)
    {

        $query = <<<SQL
INSERT INTO gravure
    (box, gift, engrave, checked, id_prestashop, state_prestashop, date_prestashop, created_at, updated_at)
VALUES
    (:box, :gift, :engrave, :checked, :id_prestashop, :state_prestashop, :date_prestashop, :created_at, :updated_at)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'box' => (int)$gravure->getBox(),
            'gift' => (bool)$gravure->getGift(),
            'engrave' => (bool)$gravure->getEngrave(),
            'checked' => (bool)$gravure->getChecked(),
            'id_prestashop' => (int)$gravure->getIdPrestashop(),
            'state_prestashop' => (int)$gravure->getStatePrestashop(),
            'date_prestashop' => (string)$gravure->getDatePrestashop(),
            'created_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
        ]);
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    public function findAllByIdOrder($id)
    {
        $sql = "SELECT * FROM gravure WHERE id_order = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $gravures = [];

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $gravures[] = $this->hydrateFromRow($row);
        }

        return $gravures;
    }

    public function update(Gravure $gravure){

        $sql = "UPDATE gravure SET 
        box = :box,
        gift = :gift,
        engrave = :engrave,
        checked = :checked,
        id_prestashop = :id_prestashop,
        state_prestashop = :state_prestashop,
        date_prestashop = :date_prestashop,
        updated_at  = :updated_at 
        WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'box' => (int)$gravure->getBox(),
            'gift' => (bool)$gravure->getGift(),
            'engrave' => (bool)$gravure->getEngrave(),
            'checked' => (bool)$gravure->getChecked(),
            'id_prestashop' => (int)$gravure->getIdPrestashop(),
            'state_prestashop' => (int)$gravure->getStatePrestashop(),
            'date_prestashop' => (string)$gravure->getDatePrestashop(),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            "id" => (int) $gravure->getId(),
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Gravure::fromArray($row);
    }
}