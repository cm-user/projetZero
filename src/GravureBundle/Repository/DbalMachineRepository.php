<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 15/03/2018
 * Time: 12:21
 */

namespace GravureBundle\Repository;


use Doctrine\DBAL\Connection;
use GravureBundle\Entity\Domain\Machine;

class DbalMachineRepository
{

    private $connection;

    /**
     * DbalMachineRepository constructor.
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Machine $machine)
    {
        $query = <<<SQL
INSERT INTO gravure_machine
    (name, color, type)
VALUES
    (:name, :color, :type)  
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'name' => (string)$machine->getName(),
            'color' => (string)$machine->getColor(),
            'type' => (string)$machine->getType()
        ]);
    }

    public function findAll(){
        $machines = $this->connection->fetchAll('SELECT * FROM gravure_machine');

        return $machines;
    }

    public function findAllWithoutNull(){
        $machines = $this->connection->fetchAll('SELECT * FROM `gravure_machine` WHERE name != \'null\'');

        return $machines;
    }

    public function findById($id){

        $sql = "SELECT * FROM gravure_machine WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }
        return $this->hydrateFromRow($row);
    }

    public function update(Machine $machine){

        $sql = "UPDATE gravure_machine SET name = :name, type = :type, color = :color, by_default = :by_default WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            "name" => (string) $machine->getName(),
            "color" => (string) $machine->getColor(),
            "type" => (string) $machine->getType(),
            "by_default" => (int) $machine->getDefault(),
            "id" =>  (int) $machine->getId(),
        ]);
    }

    public function getDefaultColor(){
        $sql = "SELECT * FROM gravure_machine WHERE by_default = :by_default";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("by_default", 1);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }

        $color = $row['color'];

        return $color;
    }

    public function updateDefault($idNewMachine){

        $sql = "SELECT * FROM gravure_machine WHERE by_default = :by_default";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("by_default", 1);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($row == null){
            return null;
        }

        $idOldMachine = $row['id'];

        $sql = "UPDATE gravure_machine SET by_default = :by_default WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            "by_default" => 0,
            "id" => $idOldMachine ,
        ]);

        $sql = "UPDATE gravure_machine SET by_default = :by_default WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            "by_default" => 1,
            "id" => $idNewMachine ,
        ]);
    }

    public function delete($id){
        $sql = "DELETE FROM gravure_machine WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    private function hydrateFromRow(array $row)
    {
        return Machine::fromArray($row);
    }

}