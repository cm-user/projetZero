<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 21/03/2018
 * Time: 10:26
 */

namespace GravureBundle\Repository;


use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Product;

class DbalProductRepository
{
    private $connection;

    /**
     * DbalProductRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Product $product)
    {
        $query = <<<SQL
INSERT INTO gravure_product
    (id_category, product_id, time, alias)
VALUES
    (:id_category, :product_id, :time, :alias)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'id_category' => (int)$product->getIdCategory(),
            'product_id' => (int)$product->getProductId(),
            'time' => (int)$product->getTime(),
            'alias' => (string)$product->getAlias(),
        ]);
    }

    public function findAll(){
        $products = $this->connection->fetchAll('SELECT * FROM gravure_product');

        return $products;
    }

    public function findByProductId($id){
        $sql = "SELECT * FROM gravure_product WHERE product_id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }
        return $this->hydrateFromRow($row);
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_product WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }
        return $this->hydrateFromRow($row);
    }

    public function update(Product $product){

        $sql = "UPDATE gravure_product SET id_category = :id_category, product_id = :product_id, time = :time, alias = :alias WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'id_category' => (int)$product->getIdCategory(),
            'product_id' => (int)$product->getProductId(),
            'time' => (int)$product->getTime(),
            'alias' => (string)$product->getAlias(),
            "id" =>  (int) $product->getId(),
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure_product WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Product::fromArray($row);
    }
}