<?php

namespace GravureBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class GravureAssistantController extends Controller
{

    /**
     * @Route("/assistant", name="gravure_assistant_index")
     */
    public function indexAction()
    {
        //TODO Ajout d'un numéro de session aux gravures
        //TODO Changer leurs statut en EN COURS

        $idSession = $this->get('repositories.session')->findMaxId();//recherche de la dernière session créé
        $machines = $this->get('repositories.machine')->findAllWithoutNull();//sélection de toutes les machines


        $this->get('session')->set('number_session', $idSession); //on stock le numéro de session
//        var_dump($this->get('session')->get('number_session'));

        return $this->render('GravureBundle:gravure_assistant:index.html.twig', [ 'idSession' => $idSession, 'machines' => $machines]);
    }


    /**
     * @Route("/assistant/begin", name="gravure_assistant_begin", options={"expose"=true})
     */
    public function getTheChainSession()
    {

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