<?php

namespace CM\ServiceClientBundle\Controller;

use CM\ServiceClientBundle\Entity\Bond;
use CM\ServiceClientBundle\Entity\UploadZip;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use CM\ServiceClientBundle\Form\UploadZipType;
use \ZipArchive;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;





/**
 * Bond controller.
 *
 * @Route("bond")
 */
class BondController extends Controller
{
    /**
     * Lists all bond entities.
     *
     * @Route("/", name="bond_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bonds = $em->getRepository('ServiceClientBundle:Bond')->findAll();

        return $this->render('ServiceClientBundle:bond:index.html.twig', array(
            'bonds' => $bonds,
        ));
    }

    /**
     * Creates a new bond entity.
     *
     * @Route("/new", name="bond_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $uploadZip = new UploadZip();
        $form = $this->createForm('CM\ServiceClientBundle\Form\UploadZipType',$uploadZip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $uploadZip->getZip();
            $fileName = "temp.pdf"; //nom du fichier zip dans notre repertoire

            $file->move(
                $this->getParameter('bond'),
                $fileName
            );

            $bond = new Bond();
            $number_random = rand (); //genere un nombre aléatoire
            $new_name = "BL" . $number_random . ".pdf"; //nouveau nom pour le fichier pdf
            $path = $this->getParameter('bond') . "/" . $new_name; //creation du chemin complet
            rename($this->getParameter('bond') . "/" . $fileName, $path); //on renomme le fichier qui se situe déjà dans le répertoire bond
            $bond->setName($new_name);
            $bond->setPath($path);
            $em->persist($bond);
            $em->flush();

            return $this->redirectToRoute('bond_index');
        }

        return $this->render('ServiceClientBundle:bond:new.html.twig', array(
//            'bond' => $bond,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bond entity.
     *
     * @Route("/{id}", name="bond_show")
     * @Method("GET")
     */
    public function showAction(Bond $bond)
    {
        $deleteForm = $this->createDeleteForm($bond);

        return $this->render('ServiceClientBundle:bond:show.html.twig', array(
            'bond' => $bond,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing bond entity.
     *
     * @Route("/{id}/edit", name="bond_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bond $bond)
    {
        $deleteForm = $this->createDeleteForm($bond);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\BondType', $bond);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $date = new \DateTime('NOW');
            $bond->setUpdatedAt($date);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bond_edit', array('id' => $bond->getId()));
        }

        return $this->render('ServiceClientBundle:bond:edit.html.twig', array(
            'bond' => $bond,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bond entity.
     *
     * @Route("/{id}", name="bond_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bond $bond)
    {
        $form = $this->createDeleteForm($bond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bond);
            $em->flush();
        }

        return $this->redirectToRoute('bond_index');
    }

    /**
     * Creates a form to delete a bond entity.
     *
     * @param Bond $bond The bond entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bond $bond)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bond_delete', array('id' => $bond->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Delete a bond entity.
     *
     * @Route("/{id}/delete", name="bond_delete_bybutton")
     * @return RedirectResponse
     */
    public function delete_bybuttonAction(Bond $bond)
    {
        $dir_path = "bond"; //chemin du dossier des images
        $file_name = $bond->getName();
        $path = $dir_path . "/" . $file_name; //forme le chemin complet de l'image

        if(file_exists($path)){
            unlink($path); //suppression du pdf
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($bond);
        $em->flush();

        return $this->redirectToRoute('bond_index');
    }
    
    /**
     * Creates a new bond entity.
     *
     * @Route("/new/upload-zip", name="bond_new_upload")
     * @Method({"GET", "POST"})
     */
    public function newUploadZipAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $uploadZip = new UploadZip();
        $form = $this->createForm('CM\ServiceClientBundle\Form\UploadZipType',$uploadZip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $uploadZip->getZip();
            $fileName = "folder_pdf.zip"; //nom du fichier zip dans notre repertoire
            
            $file->move(
                $this->getParameter('bond'),
                $fileName
            );

            //décompresse les nouveaux bons retour du fichier zip et les place dans le répertoire bond
            BondController::unzip_file('bond/folder_pdf.zip', 'bond/');

            if($zip = zip_open("bond/folder_pdf.zip"))
            {
                while($zipEntry = zip_read($zip)) { //boucle sur chaque pdf dans le fichier zip
                    $bond = new Bond();
                    $number_random = rand (); //genere un nombre aléatoire
                    $new_name = "BL-" . $number_random . ".pdf"; //nouveau nom pour le fichier pdf
                    $path = $this->getParameter('bond') . "/" . $new_name; //creation du chemin complet
                    $file_Name = zip_entry_name($zipEntry); //nom du fichier dans le zip
                    rename($this->getParameter('bond') . "/" . $file_Name, $path); //on renomme le fichier qui se situe déjà dans le répertoire bond
                    $bond->setName($new_name);
                    $bond->setPath($path);
                    $em->persist($bond);
                }
                zip_close($zip);
            }

            $em->flush();

            return $this->redirectToRoute('bond_index');
        }

        return $this->render('ServiceClientBundle:bond:new_upload_zip.html.twig', array(
//            'bond' => $bond,
            'form' => $form->createView(),
        ));
    }

    /**
     * Décompresse un fichier zip
     *
     */
    private function unzip_file($file, $destination){
        // Créer l'objet (PHP 5 >= 5.2)
        $zip = new ZipArchive() ;
        // Ouvrir l'archive
        if ($zip->open($file) !== true) {
            return 'Impossible d\'ouvrir l\'archive';
        }
        // Extraire le contenu dans le dossier de destination
        $zip->extractTo($destination);
        // Fermer l'archive
        $zip->close();
        // Afficher un message de fin
//                    echo 'Archive extrait';
    }


    /**
     * @Route("/json/all", name="bond_show_json", options={"expose"=true})
     *
     */
    public function GetAllAction(){

        $ListeBond = $this->get('sc.repository.bond')->findAll();

        $formatted = [];
        foreach ($ListeBond as $bond) {
            $formatted[] = [
                'id' => $bond->getId(),
                'name' => $bond->getName(),
                'path' => $bond->getPath(),
            ];
        }
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/download/{id}", name="bond_downlaod")
     *
     */
    public function downloadAction(Bond $bond){

        $em = $this->getDoctrine()->getManager();

        $bonds = $em->getRepository('ServiceClientBundle:Bond')->findAll();
        if(sizeof($bonds) < 10){ //s'il reste moins de 10 bons retour, on envoit une alerte par mail
            ///Procèdure d'envoie de mail
            $mailBonds = $em->getRepository('ServiceClientBundle:MailBond')->findAll();
            foreach ($mailBonds as $mailBond){
                sendMail($mailBond->getMail()); //appel de la fonction sendMail()
            }
        }


        $dir_path = "bond"; //chemin du dossier des images
        $file_name = $bond->getName();
        $date_name = (new \DateTime())->format('d-m-Y') . ".pdf"; //nom final du fichier lors du téléchargement
        $path = $dir_path . "/" . $file_name; //forme le chemin complet de l'image

        $response = new Response();
        $response->setContent(file_get_contents($path));
        $response->headers->set('Content-Type', 'application/force-download'); // modification du content-type pour forcer le téléchargement (sinon le navigateur internet essaie d'afficher le document)
        $response->headers->set('Content-Transfer-Encoding', 'Binary');
        $response->headers->set('Content-Length', filesize($path));
        $response->headers->set('Content-disposition', 'filename=' . $date_name );
        ob_end_clean();

        if(file_exists($path)){
            unlink($path); //suppression du pdf
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($bond);
        $em->flush();

        return $response;
    }
}
