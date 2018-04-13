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
    (id_product, id_session, id_order, id_machine, id_status, path_jpg, path_pdf, config_id, position_gabarit, created_at, updated_at)
VALUES
    (:id_product, :id_session, :id_order, :id_machine, :id_status, :path_jpg, :path_pdf, :config_id, :position_gabarit, :created_at, :updated_at)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'id_product' => $gravure->getIdProduct(),
            'id_session' => null,
            'id_order' => (int)$gravure->getIdOrder(),
            'id_machine' => null,
            'id_status' => 1,
            'path_jpg' => (string)$gravure->getPathJpg(),
            'path_pdf' => (string)$gravure->getPathPdf(),
            'config_id' => (int)$gravure->getConfigId(),
            'position_gabarit' => null,
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

    public function findByIdConfig($id){

        $sql = "SELECT * FROM gravure WHERE config_id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function findAllWithoutSessionByDateLimitAndState($datetime){
        $sql = 'SELECT 
gravure.id, 
gravure_order.id_prestashop, 
gravure_order.state_prestashop, 
gravure.path_jpg, 
gravure.path_pdf, 
gravure_product.product_id, 
gravure_order.checked, 
gravure_product.time,
gravure_order.box 
FROM gravure 
LEFT JOIN gravure_order on gravure.id_order = gravure_order.id 
LEFT JOIN gravure_product on gravure.id_product = gravure_product.id 
WHERE gravure.id_session IS NULL 
AND gravure_order.date_prestashop < :datetime
AND (gravure_order.state_prestashop = 3 OR gravure_order.state_prestashop = 4)
ORDER BY gravure_order.state_prestashop DESC, gravure_order.id_prestashop';


        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("datetime", $datetime->format('Y-m-d h:m:s'));
        $stmt->execute();
        $gravures = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $gravures;
    }

    public function findAllWithoutSessionAfterDateLimit($datetime){
        $sql = 'SELECT 
gravure.id, 
gravure_order.id_prestashop, 
gravure.path_jpg, 
gravure.path_pdf, 
gravure_product.product_id
FROM gravure 
LEFT JOIN gravure_order on gravure.id_order = gravure_order.id 
LEFT JOIN gravure_product on gravure.id_product = gravure_product.id 
WHERE gravure.id_session IS NULL 
AND gravure_order.date_prestashop > :datetime
ORDER BY gravure_order.state_prestashop DESC, gravure_order.id_prestashop';


        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("datetime", $datetime->format('Y-m-d h:m:s'));
        $stmt->execute();
        $gravures = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $gravures;

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

    public function findByOrderIdPrestashop($id_prestashop){
        $sql = "SELECT * FROM `gravure` as g LEFT JOIN gravure_order as o ON g.id_order=o.id WHERE o.id_prestashop = :id_prestashop";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id_prestashop", $id_prestashop);
        $stmt->execute();
        $gravures =  $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $gravures;

    }

    public function countGravureNumber($datetime)
    {

        $sql =('SELECT COUNT(g.id) 
FROM gravure as g 
LEFT JOIN gravure_order as o on g.id_order = o.id
where o.state_prestashop = 4
AND g.id_session IS NULL
AND o.date_prestashop < :datetime');

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("datetime", $datetime->format('Y-m-d h:m:s'));
        $stmt->execute();
        $expe = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $sql =('SELECT COUNT(g.id) 
FROM gravure as g 
LEFT JOIN gravure_order as o on g.id_order = o.id
where o.state_prestashop = 3
AND g.id_session IS NULL
AND o.date_prestashop < :datetime');

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("datetime", $datetime->format('Y-m-d h:m:s'));
        $stmt->execute();
        $prepa = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $sql =('SELECT COUNT(g.id) 
FROM gravure as g 
LEFT JOIN gravure_order as o on g.id_order = o.id
where o.engrave = 0
AND g.id_session IS NULL
AND o.date_prestashop > :datetime');

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("datetime", $datetime->format('Y-m-d h:m:s'));
        $stmt->execute();
        $tomorrow = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ['NumberExpe' => (int)$expe[0]['COUNT(g.id)'] , 'NumberPrepa' => (int)$prepa[0]['COUNT(g.id)'], 'NumberTomorrow' => (int)$tomorrow[0]['COUNT(g.id)']];
    }

//    public function update(Gravure $gravure){
//
//        $sql = "UPDATE gravure SET
//        box = :box,
//        gift = :gift,
//        engrave = :engrave,
//        checked = :checked,
//        id_prestashop = :id_prestashop,
//        state_prestashop = :state_prestashop,
//        date_prestashop = :date_prestashop,
//        updated_at  = :updated_at
//        WHERE id = :id";
//
//        $stmt = $this->connection->prepare($sql);
//        $stmt->execute([
//            'box' => (int)$gravure->getBox(),
//            'gift' => (bool)$gravure->getGift(),
//            'engrave' => (bool)$gravure->getEngrave(),
//            'checked' => (bool)$gravure->getChecked(),
//            'id_prestashop' => (int)$gravure->getIdPrestashop(),
//            'state_prestashop' => (int)$gravure->getStatePrestashop(),
//            'date_prestashop' => (string)$gravure->getDatePrestashop(),
//            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
//            "id" => (int) $gravure->getId(),
//        ]);
//    }

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