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

    public function findCategorySurnameByChainNumber($chainNumber){
        $sql = 'SELECT gravure_category.surname
FROM gravure_chain_session 
LEFT JOIN gravure ON gravure_chain_session.id_gravure = gravure.id
LEFT JOIN gravure_product on gravure.id_product = gravure_product.id
LEFT JOIN gravure_category on gravure_product.id_category = gravure_category.id
WHERE gravure_chain_session.chain_number = :chain_number';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("chain_number", $chainNumber);
        $stmt->execute();
        $surnames = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $surnames[0]['surname'];
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