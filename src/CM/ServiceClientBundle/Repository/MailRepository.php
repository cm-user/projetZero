<?php

namespace CM\ServiceClientBundle\Repository;

use Doctrine\ORM\EntityManager;
use CM\ServiceClientBundle\Entity\Mail;


class MailRepository
{

    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|Mail[]
     */
    public function findAll(){
        return $this->entityManager->getRepository('ServiceClientBundle:Mail')->findAll();
    }

    public function findByAsc(){
        $q = $this->entityManager->createQueryBuilder();

        $q->select('m')
            ->from('ServiceClientBundle:Mail', 'm')
            ->orderBy('m.nom')
        ;
       
        $mail = $q->getQuery()->getResult();

        return $mail;
    }
    
    public function save(Mail $mail){
        $this->entityManager->persist($mail);
        $this->entityManager->flush();
    }

    public function delete(Mail $mail){
        $this->entityManager->remove($mail);
        $this->entityManager->flush();
    }
}
