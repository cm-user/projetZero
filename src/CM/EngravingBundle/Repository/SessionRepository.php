<?php

namespace CM\EngravingBundle\Repository;

use Doctrine\ORM\EntityManager;
use CM\EngravingBundle\Entity\Session;

/**
 * SessionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SessionRepository extends \Doctrine\ORM\EntityRepository
{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|Session[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository('EngravingBundle:Session')->findAll();
    }

    public function findByMaxId()
    {
        $qb = $this->entityManager->createQueryBuilder('s');
        $qb->select('MAX(s.id)')
        ->from('EngravingBundle:Session', 's');


        $session = $qb->getQuery()->getSingleResult();

        return $session;
    }
    
    public function findOneById($id)
    {
        return $this->entityManager->getRepository('EngravingBundle:Session')->findOneBy(['id' => $id]);
    }

    public function findAllByDate($debut, $fin){
        
        $q = $this->entityManager->createQueryBuilder();

        $q->select('s')
            ->from('EngravingBundle:Session', 's')
            ->where('s.createdAt >= :date_min')
            ->andWhere('s.createdAt <= :date_max')
            ->setParameter(':date_min', $debut.' 00:00:00')
            ->setParameter(':date_max', $fin.' 23:59:59')
        ;

        $sessions = $q->getQuery()->getResult();

        return $sessions;

    }
    
    public function findLast(){

        $q = $this->entityManager->createQueryBuilder();

        $q->select('s')
            ->from('EngravingBundle:Session', 's')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults( 10 );
        ;

        $sessions = $q->getQuery()->getResult();

        return $sessions;

    }



    public function save(Session $session)
    {
        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }

    public function delete(Session $session)
    {
        $this->entityManager->remove($session);
        $this->entityManager->flush();
    }
}
