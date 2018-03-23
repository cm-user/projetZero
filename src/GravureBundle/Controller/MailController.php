<?php

namespace GravureBundle\Controller;

use GravureBundle\Entity\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Mail controller.
 *
 * @Route("mail")
 */
class MailController extends Controller
{
    /**
     * Lists all mail entities.
     *
     * @Route("/", name="gravure_mail_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mails = $em->getRepository('GravureBundle:Mail')->findAll();

        return $this->render('GravureBundle:mail:index.html.twig', array(
            'mails' => $mails,
        ));
    }

    /**
     * Creates a new mail entity.
     *
     * @Route("/new", name="gravure_mail_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $mail = new Mail();
        $form = $this->createForm('GravureBundle\Form\MailType', $mail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mail);
            $em->flush();

            return $this->redirectToRoute('gravure_mail_index');
        }

        return $this->render('GravureBundle:mail:new.html.twig', array(
            'mail' => $mail,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a mail entity.
     *
     * @Route("/{id}", name="gravure_mail_show")
     * @Method("GET")
     */
    public function showAction(Mail $mail)
    {
        $deleteForm = $this->createDeleteForm($mail);

        return $this->render('GravureBundle:mail:show.html.twig', array(
            'mail' => $mail,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing mail entity.
     *
     * @Route("/{id}/edit", name="gravure_mail_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Mail $mail)
    {
        $deleteForm = $this->createDeleteForm($mail);
        $editForm = $this->createForm('GravureBundle\Form\MailType', $mail);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gravure_mail_edit', array('id' => $mail->getId()));
        }

        return $this->render('GravureBundle:mail:edit.html.twig', array(
            'mail' => $mail,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a mail entity.
     *
     * @Route("/{id}", name="gravure_mail_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Mail $mail)
    {
        $form = $this->createDeleteForm($mail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mail);
            $em->flush();
        }

        return $this->redirectToRoute('gravure_mail_index');
    }

    /**
     * Creates a form to delete a mail entity.
     *
     * @param Mail $mail The mail entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Mail $mail)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gravure_mail_delete', array('id' => $mail->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
