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

    public function saveTextAndLinkGravureText($idGravure, $blockValue, $blockName){

        //insere le nom du block et sa valeur dans la table Text
        $query = <<<SQL
INSERT INTO gravure_text
    (name_block, value)
VALUES
    (:name_block, :value)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'name_block' => (string)$blockName,
            'value' => (string)$blockValue,
        ]);



        //selectionne l'id de la table Text
        $idText = self::findLast();
        var_dump($idText['MAX(id)']);


        //insere l'id de la table gravure et l'id de la table text dans la table gravure_link qui servira de liaison
        $query = <<<SQL
INSERT INTO gravure_link_gravure_text
    (id_gravure, id_text)
VALUES
    (:id_gravure, :id_text)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'id_gravure' => $idGravure,
            'id_text' => $idText['MAX(id)'],
        ]);
    }

    public function findAll(){
        $texts = $this->connection->fetchAll('SELECT * FROM gravure_text');

        return $texts;
    }

    public function findLast(){

        $sql = "SELECT MAX(id) FROM `gravure_text` ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row;
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