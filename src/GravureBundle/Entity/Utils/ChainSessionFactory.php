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

    public function sortGravure($gravures, $lockedGravures)
    {
        $idSession = $this->container->get('repositories.session')->findMaxId();//recherche de la dernière session créé

        $chainNumber = 0;
        $oldGravureSurname = '';
        $oldColor = "";
        $seriesNumber = 1;


        $array = [];
        $arrayIdLockedGravure = [];

        //ajoute toutes les gravures vérouillées au tableau réponse
        foreach ($lockedGravures as $lockedGravure){
            if($lockedGravure['series_number'] == 1){
                $chainNumber++;
            }
            $array[] = [
                'id_gravure' => $lockedGravure['id'],
                'id_session' => $idSession,
                'chain_number' => $chainNumber,
                'series_number' => $lockedGravure['series_number'],
                'locked_position' => 1
            ];
            $arrayIdLockedGravure[] = $lockedGravure['id'];
        }

        foreach ($gravures as $gravure){

            //si l'id de l'actuel gravure n'est pas dans la liste des gravures verrouillées , alors on ajoute cette gravure au tableau réponse
            if(!in_array($gravure['id'], $arrayIdLockedGravure)) { //empêche d'avoir des doublons

                $gravureSurname = $gravure['surname'];

                //si la gravure a la même catégorie et la même machine lié que la précédente alors on l'ajoute à la même série
                if (($oldGravureSurname == $gravureSurname) && ($gravure['color'] == $oldColor)) {
                    //on vérifie que le maximum par série ne soit pas atteint
                    if ($seriesNumber % $gravure['max_gabarit'] == 0) {
                        $seriesNumber = 0;
                        $chainNumber++;
                    }
                } else {
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
                    'locked_position' => 0
                ];
            }
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
            $categories = $this->container->get('repositories.chain_session')->findCategorySurnameAndGabaritByChainNumber($chain['chain_number']);
            //récupére de la même manière la couleur de la machine utilisée pour la chaîne
            $color = $this->container->get('repositories.chain_session')->findColorByChainNumber($chain['chain_number']);
            //récupére les gravures comprises dans cette chaîne
            $gravures = $this->container->get('repositories.chain_session')->findGravuresByChainNumber($chain['chain_number']);


//            $arrayIdGravure = [];
//            $arrayPathJpgGravure = [];
            $arrayGravureText = [];
            foreach ($gravures as $gravure){

                $texts = $this->container->get('repositories.gravure_text')->findTextByIdGravure($gravure['id']);

                foreach ($texts as $text){

                    $arrayGravureText[] = [
                      $gravure['id'] => [
                          'name_block' => $text->getNameBlock(),
                          'value' => $text->getValue()
                      ]
                    ];

                }

                $arrayIdGravure[] = $gravure['id'];
                $arrayPathJpgGravure[] = $gravure['path_jpg'];
            }

            $locked = $this->container->get('repositories.chain_session')->isLockedByMachineDefault($gravures[0]['id']);

            $array[] = [
                'surname' => $categories[0]['surname'],
                'path_gabarit' => $categories[0]['path_gabarit'],
                'name_gabarit' => $categories[0]['name_gabarit'],
                'color' => $color,
                'gravures' => $gravures,
                'texts' => $arrayGravureText,
                'locked' => $locked,
                'status' => $gravures[0]['id_status'],
                'machine' => $gravures[0]['type'],
                'chain_number' => $chain['chain_number']
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