<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 10:46
 */

namespace GravureBundle\Repository;

use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Session;

class DbalSessionRepository
{
    private $connection;

    /**
     * DbalSessionRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findLastTen(){
        $sessions = $this->connection->fetchAll('SELECT * FROM gravure_session ORDER BY id LIMIT 10');

        return $sessions;
    }

    public function findAll(){
        $sessions = $this->connection->fetchAll('SELECT * FROM gravure_session');

        return $sessions;
    }

    public function save(Session $session)
    {
        $query = <<<SQL
INSERT INTO gravure_session
    (user, gravure_total, created_at, updated_at)
VALUES
    (:user, :gravure_total, :created_at, :updated_at)
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'user' => (string)$session->getUser(),
            'gravure_total' => (int)$session->getGravureTotal(),
            'created_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
        ]);
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_session WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }
        return $this->hydrateFromRow($row);
    }

    public function findMaxId(){
        $session = $this->connection->fetchAll('SELECT MAX(id) FROM gravure_session');

        return $session[0]['MAX(id)'];
    }

    public function countNumberEngraveInLastSession(){
        $sql =('SELECT COUNT(gravure.id) FROM gravure WHERE gravure.id_session = (SELECT MAX(id) FROM gravure_session)');

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $number = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $number[0]['COUNT(gravure.id)'];
    }

    public function updateNumberEngrave($number){
        $sql = "UPDATE gravure_session SET 
        gravure_total = :gravure_total,
        updated_at  = :updated_at 
        WHERE id = (SELECT MAX(id) FROM (SELECT * FROM gravure_session) AS session_table)";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'gravure_total' => (int)$number,
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
        ]);
    }

    public function update(Session $session){

        $sql = "UPDATE gravure_session SET 
        user = :user,
        gravure_total = :gravure_total,
        updated_at  = :updated_at WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'user' => (string)$session->getUser(),
            'gravure_total' => (int)$session->getGravureTotal(),
            'updated_at' => (new \DateTime())->format('Y-m-d h:m:s'),
            "id" => (int) $session->getId(),
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure_session WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Session::fromArray($row);
    }
}