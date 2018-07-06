<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 23/03/2018
 * Time: 09:47
 */

namespace GravureBundle\Repository;

use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Text;
use GravureBundle\Entity\Domain\Gravure;

class DbalGravureTextRepository
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

    public function save(Gravure $gravure, array $arrayTextObject)
    {
        foreach ($arrayTextObject as $text){

            $query = <<<SQL
INSERT INTO gravure_link_gravure_text
    (id_gravure, id_text, created_at, updated_at)
VALUES
    (:id_gravure, :id_text, :created_at, :updated_at)
;
SQL;

            $stmt = $this->connection->prepare($query);
            $stmt->execute([
                'id_gravure' => (int)$gravure->getId(),
                'id_text' => (int)$text->getId(),
                'created_at' => (new \DateTime())->format('Y-m-d H:m:s'),
                'updated_at' => (new \DateTime())->format('Y-m-d H:m:s'),
            ]);
    }


    }

    public function findTextByIdGravure($id){

        $sql = "SELECT gravure_link_gravure_text.id_gravure, gravure_text.name_block, gravure_text.value, gravure_text.updated_at, gravure_text.created_at
FROM `gravure_link_gravure_text` 
LEFT JOIN gravure_text ON gravure_link_gravure_text.id_text = gravure_text.id
WHERE gravure_link_gravure_text.id_gravure = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $texts = [];

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $texts[] = $this->hydrateFromRow($row);
        }

        return $texts;
    }

    private function hydrateFromRow(array $row)
    {
        return Text::fromArray($row);
    }
}