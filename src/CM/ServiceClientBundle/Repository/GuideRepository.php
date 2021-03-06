<?php

namespace CM\ServiceClientBundle\Repository;

use Doctrine\ORM\EntityManager;
use CM\ServiceClientBundle\Entity\Guide;

/**
 * GuideRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GuideRepository
{
    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|Guide[]
     */
    public function findAll(){
        return $this->entityManager->getRepository('ServiceClientBundle:Guide')->findAll();
    }

    public function save(Guide $guide){
        $this->entityManager->persist($guide);
        $this->entityManager->flush();
    }

    public function delete(Guide $guide){
        $this->entityManager->remove($guide);
        $this->entityManager->flush();
    }
}
