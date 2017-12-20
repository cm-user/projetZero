<?php

namespace CM\EngravingBundle\Controller;

use CM\EngravingBundle\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use \ZipArchive;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Session controller.
 *
 * @Route("session")
 */
class SessionController extends Controller
{
    /**
     * Lists all session entities.
     *
     * @Route("/", name="session_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sessions = $em->getRepository('EngravingBundle:Session')->findAll();

        return $this->render('EngravingBundle:session:index.html.twig', array(
            'sessions' => $sessions,
        ));
    }

    /**
     * Finds and displays a session entity.
     *
     * @Route("/{id}", name="session_show")
     * @Method("GET")
     */
    public function showAction(Session $session)
    {
        $IdSession = $session->getId();
        $Images = $this->get('engraving.repository.picture')->findByIdSession($IdSession);

        return $this->render('EngravingBundle:session:show.html.twig', array(
            'session' => $session,
            'images' => $Images,
        ));
    }


    /**
     * Finds and displays a session entity.
     *
     * @Route("/download/{id}", name="session_download_picture")
     * @Method("GET")
     */
    public function DownloadPictureAction(Session $session)
    {
        $images = $session->getPictures();

        $fichier = 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.zip';
        $chemin = "gravure/"; // emplacement de votre fichier .pdf

        $fichier_txt = fopen($chemin . 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt', 'a');


        $zip = new ZipArchive();
        if ($zip->open($chemin . $fichier) == TRUE)
            if ($zip->open($chemin . $fichier, ZipArchive::CREATE) === true) {

                $zip->addFile($chemin . 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt', 'gravure_' . $session->getCreatedAt()->format('Y-m-d_H-i') . '.txt'); //Ajout du fichier au ZIP

//                chmod( $fichier, 0777);

                foreach ($images as $image) {
                    $category = $image->getCategory();
                    if ($category != null) {
                        $directory = $category->getFolder(); //nom du dossier associé à la catégorie
                        if(get_headers($image->getPathPdf())[0] == "HTTP/1.1 200 OK"){ //vérifie que l'url existe bien
                            $current = file_get_contents($image->getPathPdf()); //recupere contenu du fichier
                            $folder_file = $chemin . $image->getSurname() . '.pdf'; // nommage du fichier + son extension et choix du repertoire
                            file_put_contents($folder_file, $current); //creation du fichier au bon repertoire
                            $file = $image->getSurname() . '.pdf';
                            $zip->addFile($chemin . $file, $directory . '/' . $file); //Ajout du fichier au ZIP
                        }
                        else {
                            fputs($fichier_txt, "le produit " . $image->getName() . " n'a pas de pdf" . "\r\n"); //indique le produit qui n'a pas de pdf
                        }
                    }
                }

                // Et on referme l'archive.
                $zip->close();
            } else {
                echo 'Impossible d&#039;ouvrir &quot;Zip.zip&quot;';
            }

        //partie téléchargement
        $response = new Response();
        $response->setContent(file_get_contents($chemin . $fichier));
        $response->headers->set('Content-Type', 'application/zip'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
        $response->headers->set('Content-Transfer-Encoding', 'Binary');
        $response->headers->set('Content-Length', filesize($chemin . $fichier));
        $response->headers->set('Content-disposition', 'filename=GRAVURE.zip' );
        ob_end_clean();
        fclose($fichier_txt);
        self::clearFolder($chemin);


        return $response;
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

    /**
     * @Route("/find-date/{debut}/{fin}", name="session_date_json", options={"expose"=true})
     */
    public function FindSessionByDateAction($debut, $fin)
    {

        $sessions = $this->get('engraving.repository.session')->findAllByDate($debut, $fin);

        $formatted = [];
        foreach ($sessions as $session) {
            $formatted[] = [
                'id' => $session->getId(),
                'name' => $session->getName(),
            ];
        }

        return new JsonResponse($formatted);
    }


    /**
     * @Route("/last/10", name="session_last_json", options={"expose"=true})
     */
    public function FindSessionLastAction()
    {

        $sessions = $this->get('engraving.repository.session')->findLast();

        $formatted = [];
        foreach ($sessions as $session) {
            $formatted[] = [
                'id' => $session->getId(),
                'name' => $session->getName(),
            ];
        }

        return new JsonResponse($formatted);
    }
}
