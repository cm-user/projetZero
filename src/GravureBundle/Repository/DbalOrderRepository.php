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

    public function findAll()
    {
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

    public function findById($id)
    {

        $sql = "SELECT * FROM gravure_order WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    public function findByIdPrestashop($id)
    {

        $sql = "SELECT * FROM gravure_order WHERE id_prestashop = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row == null) {
            return null;
        }
        return $row;
    }

    public function findAllWithSessionAndEngrave($id){
        $sql = 'SELECT gravure_order.id, 
gravure_product.alias,
gravure_order.id_prestashop,
gravure_machine.type,
gravure_order.updated_at
FROM gravure 
LEFT JOIN gravure_order ON gravure.id_order = gravure_order.id 
LEFT JOIN gravure_product on gravure.id_product= gravure_product.id
LEFT JOIN gravure_category on gravure_product.id_category = gravure_category.id
LEFT JOIN gravure_machine ON gravure_machine.id = gravure.id_machine
WHERE gravure.id_session = :id
AND gravure_order.engrave = 1
GROUP BY gravure_order.id
ORDER BY gravure_order.id_prestashop
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $gravures = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $gravures;
    }


    public function findLast()
    {
        $sql = "SELECT id_prestashop FROM `gravure_order` WHERE id = (SELECT MAX(id) FROM gravure_order)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function updateCheckedAndBox($id, $bool, $box){
        $sql = "UPDATE gravure_order SET 
        box = :box,
         checked = :checked,
                updated_at  = :updated_at 
        WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'box' => $box,
            'checked' => $bool,
            'updated_at' => (new \DateTime())->format('Y-m-d H:m:s'),
            "id" => $id,
        ]);
    }

    public function cleanBoxAndChecked($orderToLock){
        $sql = "UPDATE gravure_order SET 
        box = :box,
                checked = :checked,
                updated_at  = :updated_at 
          WHERE engrave = 0
        AND gravure_order.id NOT IN ( '" . implode( "', '" , $orderToLock ) . "' ) "
        ;

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'box' => 0,
            'checked' => 0,
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
        ]);
    }

    public function update(Order $order)
    {

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
            "id" => (int)$order->getId(),
        ]);
    }

    public function updateStatePrestashop($id, $state)
    {

        $sql = "UPDATE gravure_order SET 
        state_prestashop = :state_prestashop,
                updated_at  = :updated_at 
        WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'state_prestashop' => $state,
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            "id" => $id,
        ]);
    }

    public function findAllWithEngraveFinishOrOnLoad($statusFinish, $statusOnLoad){
        $sql = "SELECT gravure_order.id
FROM gravure_order
LEFT JOIN gravure g on gravure_order.id = g.id_order
WHERE g.id_status = :id_status_En_Cours OR g.id_status = :id_status_Termine
AND gravure_order.engrave = 0;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id_status_Termine", $statusFinish);
        $stmt->bindValue("id_status_En_Cours", $statusOnLoad);
        $stmt->execute();
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $orders;

        if ($orders == null) {
            return null;
        }
        return $orders;
    }

    public function setEngrave($id){
        $sql = "UPDATE gravure_order SET 
        engrave = 1,
                updated_at  = :updated_at 
        WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            "id" => $id,
        ]);
    }

    public function isEngraved($status, $idOrder){
        $sql = "SELECT * FROM gravure_order
LEFT JOIN gravure ON gravure.id_order = gravure_order.id
WHERE gravure_order.id = :id_order AND gravure.id_status = :id_status
AND (SELECT COUNT(id) FROM gravure WHERE id_order = :id_order AND gravure.id_status = :id_status) 
=
(SELECT COUNT(id) FROM gravure WHERE id_order = :id_order)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id_status", $status);
        $stmt->bindValue("id_order", $idOrder);
        $stmt->execute();
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $orders;

        if ($orders == null) {
            return null;
        }
        return $orders;
    }

    public function setCancelEngrave($chainNumber){
        $sql = "UPDATE gravure_order
 SET engrave = 0,
 updated_at  = :updated_at 
 WHERE id IN
  (SELECT gravure.id_order FROM gravure
   LEFT JOIN gravure_chain_session ON gravure_chain_session.id_gravure = gravure.id
   WHERE chain_number = :chain_number) ";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("chain_number", $chainNumber);
        $stmt->bindValue("updated_at", (new \DateTime())->format('Y-m-d h:m:s'));
        $stmt->execute();

        return $stmt;
    }

    public function delete($id)
    {
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