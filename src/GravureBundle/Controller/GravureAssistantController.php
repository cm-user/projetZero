<?php

namespace GravureBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("assistant")
 */
class GravureAssistantController extends Controller
{

    /**
     * @Route("/", name="gravure_assistant_index")
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
     * @Route("/begin", name="gravure_assistant_begin", options={"expose"=true})
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

    /**
     * @Route("/download-pdf/send-mail", name="download_gravure_pdf")
     */
    public function DownloadAndSendMailAction()
    {

        //TODO Changer le statut des gravures
        //TODO récupérer les gravures avec ce statut, récupérer celles fait par une machine pdf et les autres par machine mail


        //partie téléchargement
        $response = new Response();
        $response->setContent(file_get_contents($this->getParameter("gravure_pdf_directory") . "20-10.pdf"));
        $response->headers->set('Content-Type', 'application/force-download'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
        $response->headers->set('Content-Transfer-Encoding', 'Binary');
        $response->headers->set('Content-Length', filesize($this->getParameter("gravure_pdf_directory") . "20-10.pdf"));
        $response->headers->set('Content-disposition', 'filename=6-10.pdf' );
        ob_end_clean();
//        self::clearFolder($chemin);//

        return $response;
    }

}