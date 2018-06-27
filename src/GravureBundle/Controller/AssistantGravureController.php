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
        //récupére l'id de la session en cours ou donne la futur valeur de la session
        $idSession = $this->get('utils.session')->getRecentOrFutureIdSession();

//        $this->get('session')->set('number_session', $idSession); //on stock le numéro de session

        return $this->render('@Gravure/gravure/assistant_gravure.html.twig',['idSession' => $idSession]);
    }

    /**
     * @Route("/begin", name="assistant_gravure_chain", options={"expose"=true})
     */
    public function getTheChainSession()
    {
        $lockedGravures = $this->get('repositories.gravure')->findAllIsLockedByPositionAndNotFinish($this->getParameter('status_TERMINE'));
        //récupére toutes les gravures qui vont être gravées
        $gravures = $this->get('repositories.gravure')->FindAllWithHighSessionAndNotEngraveNotFinish($this->getParameter('status_TERMINE'));
        //construit la chaîne qui réunit les gravures par séries et par catégories
        $chainSession = $this->get('factory.chain_session')->sortGravure($gravures, $lockedGravures);
        $this->get('repositories.chain_session')->cleanTable(); //efface la table
        $this->get('repositories.chain_session')->save($chainSession); //remplit à nouveau la table

        //parse la chaîne de façon cohérente pour renvoyer un json
        $response = $this->get('factory.chain_session')->parse();

        return new JsonResponse($response);
    }

}