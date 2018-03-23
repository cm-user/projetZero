<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/03/2018
 * Time: 14:02
 */

namespace GravureBundle\Repository;

use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Category;

class DbalCategoryRepository
{
    private $connection;

    /**
     * DbalCategoryRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(){
        $categories = $this->connection->fetchAll('SELECT * FROM gravure_category');

        return $categories;
    }

    public function save(Category $category)
    {

        $query = <<<SQL
INSERT INTO gravure_category
    (id_machine, surname, folder, name_gabarit, path_gabarit, max_gabarit, created_at, updated_at)
VALUES
    (:id_machine, :surname, :folder, :name_gabarit, :path_gabarit, :max_gabarit, :created_at, :updated_at)
 ON DUPLICATE KEY
    UPDATE
      id_machine = :id_machine,
      surname = :surname,
      folder = :folder,
      name_gabarit = :name_gabarit,
      path_gabarit = :path_gabarit,
      max_gabarit = :max_gabarit,
      updated_at  = :updated_at 
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'id_machine' => (int)$category->getIdMachine(),
            'surname' => (string)$category->getSurname(),
            'folder' => (string)$category->getFolder(),
            'name_gabarit' => (string)$category->getNameGabarit(),
            'path_gabarit' => (string)$category->getPathGabarit(),
            'max_gabarit' => (int)$category->getMaxGabarit(),
            'created_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
        ]);
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_category WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    public function update(Category $category){

        $sql = "UPDATE gravure_category SET 
        id_machine = :id_machine,
        surname = :surname,
        folder = :folder,
        name_gabarit = :name_gabarit,
        path_gabarit = :path_gabarit,
        max_gabarit = :max_gabarit,
        updated_at  = :updated_at WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'id_machine' => (int)$category->getIdMachine(),
            'surname' => (string)$category->getSurname(),
            'folder' => (string)$category->getFolder(),
            'name_gabarit' => (string)$category->getNameGabarit(),
            'path_gabarit' => (string)$category->getPathGabarit(),
            'max_gabarit' => (int)$category->getMaxGabarit(),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            "id" => (int) $category->getId(),
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure_category WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Category::fromArray($row);
    }
}