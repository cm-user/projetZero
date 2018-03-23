<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 10:03
 */

namespace GravureBundle\Repository;

use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Text;

class DbalTextRepository
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

    public function save(Text $text)
    {
        $query = <<<SQL
INSERT INTO gravure_text
    (name_block, value)
VALUES
    (:name_block, :value)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'name_block' => (string)$text->getNameBlock(),
            'value' => (string)$text->getValue(),
        ]);
    }

    public function findAll(){
        $texts = $this->connection->fetchAll('SELECT * FROM gravure_text');

        return $texts;
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_text WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->hydrateFromRow($row);
    }

    public function update(Text $text){

        $sql = "UPDATE gravure_text SET name_block = :name_block, value = :value WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'name_block' => (string)$text->getNameBlock(),
            'value' => (string)$text->getValue(),
            "id" =>  (int) $text->getId(),
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure_text WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Text::fromArray($row);
    }
}