<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use \ZipArchive;
use CM\ServiceClientBundle\Entity\SearchCsv;
use CM\ServiceClientBundle\Form\SearchCsvType;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        self::clearFolder("picturecrawler/");

        return $this->render('ServiceClientBundle:Default:index.html.twig');
    }

    /**
     * @Route("/download", name="download_image")
     */
    public function download1Action()
    {
//        chmod("picturecrawler/". $date . '-Zip.zip', 0777);
        $fichier = 'Image_Cadeau_Maestro.zip' ;
        $chemin = "picturecrawler/"; // emplacement de votre fichier .pdf

        $response = new Response();
        $response->setContent(file_get_contents($chemin.$fichier));
        $response->headers->set('Content-Type', 'application/zip'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
        $response->headers->set('Content-Transfer-Encoding', 'Binary');
        $response->headers->set('Content-Length', filesize($chemin.$fichier));
        $response->headers->set('Content-disposition', 'filename='. $fichier);
        ob_end_clean();

        return $response;


}
    /**
     * Supprime le contenu d'un dossier
     * sans supprimer le dossier lui-même
     */
    private function clearFolder($folder)
    {
        // 1 ouvrir le dossier
        $dossier=opendir($folder);
        //2)Tant que le dossier est pas vide
        while ($fichier = readdir($dossier))
        {
            //3) Sans compter . et ..
            if ($fichier != "." && $fichier != "..")
            {
                //On selectionne le fichier et on le supprime
                $Vidage= $folder.$fichier;
                unlink($Vidage);
            }
        }
        //Fermer le dossier vide
        closedir($dossier);
    }

    /**
     * @Route("/form")
     */
    public function formAction(Request $request)
    {

        $searchCsv = new SearchCsv();

        $form = $this->createForm(SearchCsvType::class, $searchCsv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $searchCsv->getSearch();

            $fileName = "fichier_csv.csv";

            $file->move(
                $this->getParameter('picturecrawler'),
                $fileName
            );


            $array_lien = [];
            $array_nom = [];
            if (($handle = fopen("picturecrawler/fichier_csv.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        if($c == 0){
                            $array_nom[] = $data[$c];
                        }
                        if($c == 1){
                            $array_lien[] = $data[$c];
                        }
//                        $array_lien[] = $data[$c];
                    }



                }
                fclose($handle);
            }
            var_dump($array_nom);
            var_dump($array_lien);

            $zip = new ZipArchive();
            if($zip->open('picturecrawler/Image_Cadeau_Maestro.zip') == TRUE)
                if($zip->open('picturecrawler/Image_Cadeau_Maestro.zip', ZipArchive::CREATE) === true)
                {
//                    $zip->addEmptyDir('test'); //Creation dossier
                    for($i=0;$i<count($array_lien);$i++){
                        $current = file_get_contents($array_lien[$i]); //recupere contenu du fichier
                        $folder_file = "picturecrawler/" . $array_nom[$i] . $i .'.png'; // nommage du fichier + son extension et choix du repertoire
                        file_put_contents($folder_file, $current); //creation du fichier au bon repertoire
                        $file = $array_nom[$i] . $i .'.png';
                        $zip->addFile("picturecrawler/" .$file, $array_nom[$i]."/".$file); //Ajout du fichier au ZIP
                    }

                    // Et on referme l'archive.
                    $zip->close();
                }
                else
                {
                    echo 'Impossible d&#039;ouvrir &quot;Zip.zip&quot;';
                    // Traitement des erreurs avec un switch(), par exemple.
                }



        }

        return $this->render('ServiceClientBundle:Default:form.html.twig',array (
            'form' => $form->createView(),
        ));
    }


}
