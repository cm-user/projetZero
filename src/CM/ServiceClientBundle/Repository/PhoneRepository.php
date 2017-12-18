<?php

namespace CM\ServiceClientBundle\Repository;

use Doctrine\ORM\EntityManager;
use CM\ServiceClientBundle\Entity\Phone;


/**
 * PhoneRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PhoneRepository extends \Doctrine\ORM\EntityRepository
{
    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|Phone[]
     */
    public function findAll(){
        return $this->entityManager->getRepository('ServiceClientBundle:Phone')->findAll();
    }

    public function save(Phone $phone){
        $this->entityManager->persist($phone);
        $this->entityManager->flush();
    }

    public function delete(Phone $phone){
        $this->entityManager->remove($phone);
        $this->entityManager->flush();
    }
}
