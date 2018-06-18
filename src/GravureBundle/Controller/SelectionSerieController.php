<?php

namespace GravureBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use ZipArchive;

/**
 * @Route("serie")
 */
class SelectionSerieController extends Controller
{

    /**
     * @Route("/", name="selection_serie_index")
     */
    public function indexAction()
    {
        //TODO Ajout d'un numéro de session aux gravures
        //TODO Changer leurs statut en EN CHAIN

        $idSession = $this->get('repositories.session')->findMaxId();//recherche de la dernière session créé
        $machines = $this->get('repositories.machine')->findAllWithoutNull();//sélection de toutes les machines

        //TODO pourquoi?
        $this->get('session')->set('number_session', $idSession); //on stock le numéro de session

        return $this->render('@Gravure/gravure/selection_serie.html.twig', ['idSession' => $idSession, 'machines' => $machines]);
    }


    /**
     * @Route("/begin", name="selection_serie_chain", options={"expose"=true})
     */
    public function getTheChainSession()
    {
        $lockedGravures = $this->get('repositories.gravure')->findAllIsLockedByPosition();
        //récupére toutes les gravures qui vont être gravées
        $gravures = $this->get('repositories.gravure')->FindAllWithHighSessionAndNotEngrave();
        //construit la chaîne qui réunit les gravures par séries et par catégories
        $chainSession = $this->get('factory.chain_session')->sortGravure($gravures, $lockedGravures);
        $this->get('repositories.chain_session')->cleanTable(); //efface la table
        $this->get('repositories.chain_session')->save($chainSession); //remplit à nouveau la table

        //parse la chaîne de façon cohérente pour renvoyer un json
        $response = $this->get('factory.chain_session')->parse();

        return new JsonResponse($response);
    }

    /**
     * @Route("/download-pdf/send-mail", name="download_gravure_pdf", options={"expose"=true})
     */
    public function DownloadAndSendMailAction()
    {

        $em = $this->getDoctrine()->getEntityManager();


        //TODO gérer le cas de la position du gabarit -> verouiller le gabarit au lancement de la gravure
//        $this->get('repositories.gravure')->updateStatusForGravureInChainSession($this->getParameter('status_EN_CHAIN'), $this->getParameter('status_EN_COURS')); //passe le statut de en chaîne à en cours pour les gravures liées à la session en cours
        //sélectionne les gravures ayant le statut EN_CHAIN et faites soit par une machine ayant besoin des mails soit par une machine ayant besoin des pdf
        $mailGravures = $this->get('repositories.gravure')->findAllWithStatusOnLoadAndMailMachine($this->getParameter('status_EN_CHAIN'));
        $PDFGravures = $this->get('repositories.gravure')->findAllWithStatusOnloadAndPDFMachine($this->getParameter('status_EN_CHAIN'));


        $mails = $em->getRepository('GravureBundle:Mail')->findAll();

//        foreach ($mails as $mail){
//        self::sendMail($mail->getEmail(), $mailGravures[0]['id_session'], $mailGravures);
//        }

        $zip = new ZipArchive();

        $ZIPFileName = $this->getParameter("gravure_zip_directory") . "GRAVURE.zip";


        if ($zip->open($ZIPFileName) == TRUE)
            if ($zip->open($ZIPFileName, ZipArchive::CREATE) === true) {


                if ($PDFGravures == []) {
                    $fileTxt = fopen($this->getParameter("gravure_zip_directory") . "NO_PDF.txt", "w");
                    fclose($fileTxt);
                    $zip->addFile($this->getParameter("gravure_zip_directory") . 'NO_PDF.txt', 'NO_PDF.txt'); //Ajout d'un fichier txt vide si il n'y a aucun pdf
                } else {
                    $counterSurname = 0; //compteur pour déterminer le nombre de gravure par dossier
                    $counterFolder = 1; //compteur pour spécifier le nom du dossier
                    $oldDirectoryName = ''; //nom de l'ancienne catégorie avant itération

                    foreach ($PDFGravures as $PDFGravure) {
                        $directoryName = $PDFGravure['folder'];

                        if ($oldDirectoryName == $directoryName) { //vérifie que les deux gravures soient de la même catégorie
                            if ($counterSurname % $PDFGravure['max_gabarit'] == 0) { //vérifie que le nombre de gravure par dossier ne soit pas dépassé
                                $counterSurname = 0;
                                $counterFolder++; //incrémente le compteur du dossier afin d'en créer un nouveau
                            }

                        }
                        else {
                            $counterSurname = 0;
                            $counterFolder = 1;

                        }

                        $counterSurname++;

                        $file = str_replace('http://tools.cadeau-maestro.com/gravure/pdf/', '', $PDFGravure['path_pdf']); //récupére le nom du fichier
                        $fileName = $PDFGravure['surname'] . '(' . $counterSurname . ').pdf';  //création du nom du fichier avec son numéro

                        $zip->addFile($this->getParameter("gravure_pdf_directory") . $file, "$directoryName($counterFolder)/$fileName"); //Ajout du fichier au ZIP

                        $oldDirectoryName = $directoryName;

                    }
                }
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

    private function sendMail($mail, $header, $mailGravures)
    {
        $message = (new \Swift_Message('mail de gravure pour la session N°' . $header))
            ->setFrom('contact@cadeau-maestro.com')
            ->setTo($mail)
            ->setBody(
                $this->renderView(
                    '@Gravure/mail/mail_gravures.html.twig',
                    array('gravures' => $mailGravures)
                ),
                'text/html'
            );

        $this->get('mailer')
            ->send($message);
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