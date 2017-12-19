<?php

namespace CM\ServiceClientBundle\Controller;

use CM\ServiceClientBundle\Entity\Bond;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
        $bond = new Bond();
        $form = $this->createForm('CM\ServiceClientBundle\Form\BondType', $bond);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bond);
            $em->flush();

            return $this->redirectToRoute('bond_show', array('id' => $bond->getId()));
        }

        return $this->render('ServiceClientBundle:bond:new.html.twig', array(
            'bond' => $bond,
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
}
