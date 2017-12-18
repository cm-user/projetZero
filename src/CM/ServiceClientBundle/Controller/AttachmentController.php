<?php

namespace CM\ServiceClientBundle\Controller;

use CM\ServiceClientBundle\Entity\Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Attachment controller.
 *
 * @Route("attachment")
 */
class AttachmentController extends Controller
{
    /**
     * Lists all attachment entities.
     *
     * @Route("/", name="attachment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $attachments = $em->getRepository('ServiceClientBundle:Attachment')->findAll();

        return $this->render('ServiceClientBundle:attachment:index.html.twig', array(
            'attachments' => $attachments,
        ));
    }

    /**
     * Creates a new attachment entity.
     *
     * @Route("/new", name="attachment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $attachment = new Attachment();
        $form = $this->createForm('CM\ServiceClientBundle\Form\AttachmentType', $attachment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $attachment->getPath(); //récupére le chemin du fichier
            $fileName = $attachment->getName().".".$file->guessExtension(); //construit le fichier avec le nom donnée par l'utilisateur et son extension

            //déplace le fichier dans le repertoire attachment
            $file->move(
                $this->getParameter('attachment_directory'),
                $fileName
            );

            $attachment->setPath($this->getParameter('attachment_directory').$fileName); //renseigne le nouveau chemin sur le serveur

            $em = $this->getDoctrine()->getManager();
            $em->persist($attachment);
            $em->flush();

            return $this->redirectToRoute('attachment_show', array('id' => $attachment->getId()));
        }

        return $this->render('ServiceClientBundle:attachment:new.html.twig', array(
            'attachment' => $attachment,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/json", name="attachment_show_json", options={"expose"=true})
     * @param Attachment $attachment
     */
    public function showAction(){

        $ListeAttachment = $this->get('sc.repository.attachment')->findAll();

        $formatted = [];
        foreach ($ListeAttachment as $attachment) {
            $formatted[] = [
                'name' => $attachment->getName(),
                'path' => $attachment->getPath(),
            ];
        }
        return new JsonResponse($formatted);
    }

    /**
     * Displays a form to edit an existing attachment entity.
     *
     * @Route("/{id}/edit", name="attachment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Attachment $attachment)
    {
        $deleteForm = $this->createDeleteForm($attachment);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\AttachmentType', $attachment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('attachment_edit', array('id' => $attachment->getId()));
        }

        return $this->render('ServiceClientBundle:attachment:edit.html.twig', array(
            'attachment' => $attachment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a attachment entity.
     *
     * @Route("/{id}", name="attachment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Attachment $attachment)
    {
        $form = $this->createDeleteForm($attachment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($attachment);
            $em->flush();
        }

        return $this->redirectToRoute('attachment_index');
    }

    /**
     * Creates a form to delete a attachment entity.
     *
     * @param Attachment $attachment The attachment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Attachment $attachment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attachment_delete', array('id' => $attachment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
