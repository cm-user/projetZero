<?php

namespace CM\ServiceClientBundle\Controller;

use CM\ServiceClientBundle\Entity\MailBond;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Mailbond controller.
 *
 * @Route("mailbond")
 */
class MailBondController extends Controller
{
    /**
     * Lists all mailBond entities.
     *
     * @Route("/", name="mailbond_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mailBonds = $em->getRepository('ServiceClientBundle:MailBond')->findAll();

        return $this->render('ServiceClientBundle:mailbond:index.html.twig', array(
            'mailBonds' => $mailBonds,
        ));
    }

    /**
     * Creates a new mailBond entity.
     *
     * @Route("/new", name="mailbond_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $mailBond = new Mailbond();
        $form = $this->createForm('CM\ServiceClientBundle\Form\MailBondType', $mailBond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mailBond);
            $em->flush();

            return $this->redirectToRoute('ServiceClientBundle:mailbond:show', array('id' => $mailBond->getId()));
        }

        return $this->render('ServiceClientBundle:mailbond:new.html.twig', array(
            'mailBond' => $mailBond,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a mailBond entity.
     *
     * @Route("/{id}", name="mailbond_show")
     * @Method("GET")
     */
    public function showAction(MailBond $mailBond)
    {
        $deleteForm = $this->createDeleteForm($mailBond);

        return $this->render('ServiceClientBundle:mailbond:show.html.twig', array(
            'mailBond' => $mailBond,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing mailBond entity.
     *
     * @Route("/{id}/edit", name="mailbond_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MailBond $mailBond)
    {
        $deleteForm = $this->createDeleteForm($mailBond);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\MailBondType', $mailBond);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mailbond_edit', array('id' => $mailBond->getId()));
        }

        return $this->render('ServiceClientBundle:mailbond:edit.html.twig', array(
            'mailBond' => $mailBond,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a mailBond entity.
     *
     * @Route("/delete/{id}", name="mailbond_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MailBond $mailBond)
    {
        $form = $this->createDeleteForm($mailBond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mailBond);
            $em->flush();
        }

        return $this->redirectToRoute('ServiceClientBundle:mailbond:index');
    }

    /**
     * Creates a form to delete a mailBond entity.
     *
     * @param MailBond $mailBond The mailBond entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MailBond $mailBond)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mailbond_delete', array('id' => $mailBond->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
