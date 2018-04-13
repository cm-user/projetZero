<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 12/04/2018
 * Time: 15:37
 */

namespace GravureBundle\Entity\Utils;


use Doctrine\ORM\EntityManager;

class DateTimeLimit
{
    private $em;

    public function __construct( EntityManager $em = null)
    {
        $this->em = $em;
    }

    public function getDateTime(){
        $endOfTheDayTime = $this->em->getRepository('GravureBundle:EndOfTheDayTime')->find(1);
        $hours = $endOfTheDayTime->getHours()->format('H:i'); //récupére l'heure limite de la journée
        $date = new \DateTime();
        $hour = substr($hours, 0, 2); //heure
        $minute = substr($hours, 3, 2); // minute
        $datetime = $date->setTime($hour, $minute); //création de l'heure limite pour la journée en cours

        return $datetime;
    }

}