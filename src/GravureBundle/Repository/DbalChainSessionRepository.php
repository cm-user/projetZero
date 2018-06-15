<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/04/2018
 * Time: 12:31
 */

namespace GravureBundle\Repository;


use Doctrine\DBAL\Connection;

class DbalChainSessionRepository
{

    private $connection;

    /**
     * @param $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save($chainSession)
    {

        foreach ($chainSession as $chain){

            $query = <<<SQL
INSERT INTO gravure_chain_session
    (id_gravure, id_session, chain_number, series_number, engrave)
VALUES
    (:id_gravure, :id_session, :chain_number, :series_number, :engrave)
;
SQL;

            $stmt = $this->connection->prepare($query);
            $stmt->execute([
                'id_gravure' => $chain['id_gravure'],
                'id_session' => $chain['id_session'],
                'chain_number' => $chain['chain_number'],
                'series_number' => $chain['series_number'],
                'engrave' => $chain['engrave'],
            ]);

        }
    }

    public function getChainNumberCount(){
        $chainNumbers = $this->connection->fetchAll('SELECT chain_number, COUNT(chain_number) 
FROM gravure_chain_session 
GROUP BY chain_number');

        return $chainNumbers;
    }

    public function findCategorySurnameAndGabaritByChainNumber($chainNumber){
        $sql = 'SELECT gravure_category.surname, gravure_category.path_gabarit, gravure_category.name_gabarit
FROM gravure_chain_session 
LEFT JOIN gravure ON gravure_chain_session.id_gravure = gravure.id
LEFT JOIN gravure_product on gravure.id_product = gravure_product.id
LEFT JOIN gravure_category on gravure_product.id_category = gravure_category.id
WHERE gravure_chain_session.chain_number = :chain_number';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("chain_number", $chainNumber);
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $categories;
    }

    public function findColorByChainNumber($chainNumber){
        $sql = 'SELECT gravure_machine.color
FROM gravure_chain_session 
LEFT JOIN gravure ON gravure_chain_session.id_gravure = gravure.id
LEFT JOIN gravure_machine ON gravure_machine.id = gravure.id_machine
WHERE gravure_chain_session.chain_number = :chain_number';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("chain_number", $chainNumber);
        $stmt->execute();
        $colors = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $colors[0]['color'];
    }

    public function findGravuresByChainNumber($chainNumber){
        $sql = 'SELECT gravure.id, gravure.id_status, gravure.path_jpg, gravure_product.alias, o.box, o.gift, gravure_machine.type
FROM gravure_chain_session 
LEFT JOIN gravure ON gravure_chain_session.id_gravure = gravure.id
LEFT JOIN gravure_product  on gravure.id_product = gravure_product.id
LEFT JOIN gravure_order o on gravure.id_order = o.id
LEFT JOIN gravure_machine ON gravure_machine.id = gravure.id_machine
WHERE gravure_chain_session.chain_number = :chain_number';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("chain_number", $chainNumber);
        $stmt->execute();
        $gravures = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $gravures;

    }

    public function isLockedByMachineDefault($idGravure){
        $sql = 'SELECT gravure_category.id_machine
FROM gravure_chain_session 
LEFT JOIN gravure ON gravure_chain_session.id_gravure = gravure.id
LEFT JOIN gravure_product on gravure.id_product = gravure_product.id
LEFT JOIN gravure_category on gravure_product.id_category = gravure_category.id
WHERE gravure.id = :id ';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("id", $idGravure);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);


        if($row['id_machine'] == null){
            return 0;
        }
        else {
            return 1;
        }
    }


    public function cleanTable(){
        $query = <<<SQL
TRUNCATE gravure_chain_session
;
SQL;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
    }

}