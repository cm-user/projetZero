<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 23/03/2018
 * Time: 12:30
 */

namespace GravureBundle\Entity\Utils;


use Symfony\Component\DependencyInjection\Container;

class CreatorLinkFile
{

    private $container;

    public function __construct( Container $container = null)
    {
        $this->container = $container;
    }


    public function createJpg($config_id, $idProduct){

        $fileName = $config_id . '-' . $idProduct .'.jpg';
        $link = $this->container->getParameter('url_directory_engraving') . $config_id . '-' . $idProduct . '.jpg';
        $current = @file_get_contents($this->container->getParameter('url_directory_engraving') . $config_id . '-' . $idProduct . '.jpg'); //recupere contenu du fichier
        $folder_file = "C:\wamp64\www\projetZero2\web\gravure\jpg\ $fileName" ; //  choix du repertoire
        file_put_contents($folder_file, $current); //creation du fichier au bon repertoire avec son nom

        return $this->container->getParameter('gravure_jpg_url') . $fileName ;
    }

    public function createPdf($config_id, $idProduct){
        $fileName = $config_id . '-' . $idProduct .'.pdf';
        $current = @file_get_contents($this->container->getParameter('url_directory_engraving') . $config_id . '-' . $idProduct . '.pdf'); //recupere contenu du fichier
        $folder_file = $this->container->getParameter('gravure_pdf_directory') . $fileName ; //  choix du repertoire
        file_put_contents($folder_file, $current); //creation du fichier au bon repertoire avec son nom

        return $this->container->getParameter('gravure_pdf_url') . $fileName ;
    }



}