<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 22/03/2018
 * Time: 10:54
 */

namespace GravureBundle\Controller;


use GravureBundle\Entity\Domain\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

/**
 * Boxnumber controller.
 *
 * @Route("session")
 */
class SessionController extends Controller
{

    /**
     * @Route("/", name="gravure_session_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $sessions = $this->get('repositories.session')->findLastTen();

        return $this->render('GravureBundle:session:index.html.twig', array(
            'sessions' => $sessions,
        ));
    }

    /**
     * @Route("/all", name="gravure_session_index_all")
     * @Method("GET")
     */
    public function indexAllAction()
    {

        $sessions = $this->get('repositories.session')->findAll();

        return $this->render('GravureBundle:session:index_all.html.twig', array(
            'sessions' => $sessions,
        ));
    }

    /**
     * @Route("/show/{id}", name="gravure_session_show")
     * @Method("GET")
     */
    public function showAction($id)
    {

        $orders = $this->get('repositories.order')->findAllWithSessionAndEngrave($id); //recherche toutes les commandes liée à la session et dont la gravure est terminé

        foreach ($orders as $key => $order) {
            $gravures = $this->get('repositories.gravure')->findAllByIdOrder($order['id']); //récupére chaque gravure de chaque commande
            $orders[$key]['gravures'] = $gravures; //ajoute les gravures au tableau orders

            foreach ($gravures as $key2 => $gravure) {
                $texts = $this->get('repositories.gravure_text')->findTextByIdGravure($gravure['id']); //pour chaque gravure, récupére les textes à graver

                foreach ($texts as $key3 => $text) {
                    $orders[$key]['gravures'][$key2]['texts'][$key3]['id'] = $text['id_gravure']; // ajout au tableau order dans le bon ordre l'id de la gravure
                    $orders[$key]['gravures'][$key2]['texts'][$key3]['name_block'] = $text['name_block']; // ajout au tableau order dans le bon ordre le nom du block
                    $orders[$key]['gravures'][$key2]['texts'][$key3]['value'] = $text['value']; // ajout au tableau order dans le bon ordre la valeur entrée par le client
                }
            }
        }

        return $this->render('GravureBundle:session:show.html.twig', array(
            'orders' => $orders,
            'idSession' => $id
        ));
    }

    /**
     * @Route("/download-pdf/{id}", name="gravure_session_download_pdf")
     * @Method("GET")
     */
    public function DownloadPdfAction($id)
    {
        $gravure = $this->get('repositories.gravure')->findById($id);
        $category = $this->get('repositories.gravure')->findCategoryDetailsById($id);
        $fileName = $category['folder'] . " 1/" . $category['surname'] . " (1).pdf";
        $file = str_replace('http://tools.cadeau-maestro.com/gravure/pdf/', '', $gravure->getPathPdf()); //récupére le nom du fichier
        $zip = new ZipArchive();
        $ZIPFileName = $this->getParameter("gravure_zip_directory") . "GRAVURE.zip"; //nom du fichier ZIP reçu par l'utilisateur

        if ($zip->open($ZIPFileName) == TRUE)
            if ($zip->open($ZIPFileName, ZipArchive::CREATE) === true) {
                $zip->addFile($this->getParameter("gravure_pdf_directory") . $file, $fileName); //Ajout du fichier au ZIP
            }

        // Et on referme l'archive.
        $zip->close();

        //partie téléchargement
        $response = new Response();
        $response->setContent(file_get_contents($ZIPFileName));
        $response->headers->set('Content-Type', 'application/force-download'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
        $response->headers->set('Content-Transfer-Encoding', 'Binary');
        $response->headers->set('Content-Length', filesize($ZIPFileName));
        $response->headers->set('Content-disposition', 'filename=GRAVURE.zip');
        ob_end_clean();
        self::clearFolder($this->getParameter("gravure_zip_directory")); //efface tout le contenu du dossier

        return $response;
    }

    /**
     * @Route("/count", name="gravure_session_count_gravures", options={"expose"=true})
     */
    public function countGravureAction()
    {
        $number = $this->get('repositories.session')->countNumberEngraveInLastSession();

        $this->get('repositories.session')->updateNumberEngrave($number);

        return new JsonResponse("Ajout du total des gravures faîtes par la dernière session");
    }

    /**
     * @Route("/search/{debut}/{fin}", name="session_search_date_json", options={"expose"=true})
     */
    public function searchAction($debut, $fin)
    {
        $sessions = $this->get('repositories.session')->getSessionByDate($debut, $fin);

        return new JsonResponse($sessions);
    }

    /**
     * Supprime le contenu d'un dossier
     * sans supprimer le dossier lui-même
     */
    private function clearFolder($folder)
    {
        // 1 ouvrir le dossier
        $dossier = opendir($folder);
        //2)Tant que le dossier est pas vide
        while ($fichier = readdir($dossier)) {
            //3) Sans compter . et ..
            if ($fichier != "." && $fichier != "..") {
                //On selectionne le fichier et on le supprime
                $Vidage = $folder . $fichier;
                unlink($Vidage);
            }
        }
        //Fermer le dossier vide
        closedir($dossier);
    }
}