<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 12/06/2018
 * Time: 17:03
 */

namespace GravureBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * @Route("assistant")
 */
class AssistantGravureController extends Controller
{

    /**
     * @Route("/", name="assistant_gravure_index")
     */
    public function indexAction()
    {

        $idSession = $this->get('repositories.session')->findMaxId();//recherche de la dernière session créé

        $this->get('session')->set('number_session', $idSession); //on stock le numéro de session


        return $this->render('@Gravure/gravure/assistant_gravure.html.twig',['idSession' => $idSession]);
    }

    /**
     * @Route("/begin", name="assistant_gravure_chain", options={"expose"=true})
     */
    public function getTheChainSession()
    {
        //TODO gérer le cas pour l'utilisateur clique sur modifier la session alors que des chaînes sont en cours de gravure
        //récupére toutes les gravures qui vont être gravées
        $gravures = $this->get('repositories.gravure')->FindAllWithHighSessionAndNotEngrave();
        //construit la chaîne qui réunit les gravures par séries et par catégories
        $chainSession = $this->get('factory.chain_session')->sortGravure($gravures);
        $this->get('repositories.chain_session')->cleanTable(); //efface la table
        $this->get('repositories.chain_session')->save($chainSession); //remplit à nouveau la table

        //parse la chaîne de façon cohérente pour renvoyer un json
        $response = $this->get('factory.chain_session')->parse();

        return new JsonResponse($response);
    }

}