<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/04/2018
 * Time: 11:28
 */

namespace GravureBundle\Entity\Utils;


use Symfony\Component\DependencyInjection\Container;

class ChainSessionFactory
{

    private $container;

    /**
     * GravureFactory constructor.
     *
     */
    public function __construct(Container $container = null )
    {
        $this->container = $container;
    }

    public function sortGravure($gravures)
    {
        $idSession = $this->container->get('repositories.session')->findMaxId();//recherche de la dernière session créé

        $chainNumber = 0;
        $oldGravureSurname = '';
        $oldColor = "";
        $seriesNumber = 1;


        $array = [];

        foreach ($gravures as $gravure){

            $gravureSurname = $gravure['surname'];

            //si la gravure a la même catégorie et la même machine lié que la précédente alors on l'ajoute à la même série
            if(($oldGravureSurname == $gravureSurname) && ($gravure['color'] == $oldColor)){
                //on vérifie avant que le maximum par série ne soit pas atteint
            if($seriesNumber % $gravure['max_gabarit'] == 0){
                $seriesNumber = 0;
                $chainNumber++;
            }
            }
            else {
                $oldGravureSurname = $gravureSurname;
                $oldColor = $gravure['color'];
                $seriesNumber = 0;
                $chainNumber++;
            }

            $seriesNumber++;

            $array[] = [
                'id_gravure' => $gravure['id'],
                'id_session' => $idSession,
                'chain_number' => $chainNumber,
                'series_number' => $seriesNumber,
//                'color' => $gravure['color'],
                'engrave' => 0
            ];

        }

        return $array;

    }

    public function parse(){
        $array = [];
//        $compteur = 0;
//        $oldChainNumber = 1;

        //récupére toutes les chaînes
        $chainNumbers = $this->container->get('repositories.chain_session')->getChainNumberCount();

        foreach ($chainNumbers as $chain){
            //récupére le nom de la catégorie en fonction du numéro de chain
            $surname = $this->container->get('repositories.chain_session')->findCategorySurnameByChainNumber($chain['chain_number']);
            //récupére de la même manière la couleur de la machine utilisée pour la chaîne
            $color = $this->container->get('repositories.chain_session')->findColorByChainNumber($chain['chain_number']);

            $gravures = $this->container->get('repositories.chain_session')->findGravuresIdByChainNumber($chain['chain_number']);

            $array[] = [
                'number' => $chain['COUNT(chain_number)'],
                'surname' => $surname,
                'color' => $color,
                'gravures' => $gravures
            ];
        }

//        foreach ($chainSession as $chain){
//            $chainNumber = $chain['chain_number'];
//
//            if($chainNumber != $oldChainNumber){
//
//                $nameCategory = $this->container->get('repositories.gravure')->findCategoryById($chain['id_gravure']);
//                    $array[] = [
//                      'number' => $compteur,
//                        'chain_number' => $oldChainNumber,
//                      'surname' => $nameCategory
//                    ];
//                        $compteur = 1;
//                $oldChainNumber = $chain['chain_number'];
//            }
//            else{
//                $compteur++;
//            }
//
//
//        }
        return $array; //retourne un tableau contenant le nombre de chain par série, la catégorie et la couleur lié à la machine utilisé
    }

}