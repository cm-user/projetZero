<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 11:25
 */

namespace GravureBundle\Repository;

use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Order;

class DbalOrderRepository
{
    private $connection;

    /**
     * DbalOrderRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(){
        $orders = $this->connection->fetchAll('SELECT * FROM gravure_order');

        return $orders;
    }

    public function save(Order $order)
    {

        $query = <<<SQL
INSERT INTO gravure_order
    (box, gift, engrave, checked, id_prestashop, state_prestashop, date_prestashop, created_at, updated_at)
VALUES
    (:box, :gift, :engrave, :checked, :id_prestashop, :state_prestashop, :date_prestashop, :created_at, :updated_at)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'box' => (int)$order->getBox(),
            'gift' => (int)$order->getGift(),
            'engrave' => (int)$order->getEngrave(),
            'checked' => (int)$order->getChecked(),
            'id_prestashop' => (int)$order->getIdPrestashop(),
            'state_prestashop' => (int)$order->getStatePrestashop(),
            'date_prestashop' => (string)$order->getDatePrestashop(),
            'created_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
        ]);
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_order WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    public function findByIdPrestashop($id){

        $sql = "SELECT * FROM gravure_order WHERE id_prestashop = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }
        return $this->hydrateFromRow($row);
    }

    public function findLast(){

        $sql = "SELECT MAX(id),id_prestashop FROM `gravure_order` GROUP BY id_prestashop";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function update(Order $order){

        $sql = "UPDATE gravure_order SET 
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
            'box' => (int)$order->getBox(),
            'gift' => (bool)$order->getGift(),
            'engrave' => (bool)$order->getEngrave(),
            'checked' => (bool)$order->getChecked(),
            'id_prestashop' => (int)$order->getIdPrestashop(),
            'state_prestashop' => (int)$order->getStatePrestashop(),
            'date_prestashop' => (string)$order->getDatePrestashop(),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            "id" => (int) $order->getId(),
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure_order WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Order::fromArray($row);
    }
}