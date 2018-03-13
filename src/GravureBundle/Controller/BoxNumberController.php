<?php

namespace GravureBundle\Controller;

use GravureBundle\Entity\BoxNumber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Boxnumber controller.
 *
 * @Route("boxnumber")
 */
class BoxNumberController extends Controller
{
    /**
     * Lists all boxNumber entities.
     *
     * @Route("/", name="boxnumber_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $boxNumbers = $em->getRepository('GravureBundle:BoxNumber')->findAll();

        return $this->render('GravureBundle:boxnumber:index.html.twig', array(
            'boxNumbers' => $boxNumbers,
        ));
    }

    /**
     * Creates a new boxNumber entity.
     *
     * @Route("/new", name="boxnumber_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $boxNumber = new Boxnumber();
        $form = $this->createForm('GravureBundle\Form\BoxNumberType', $boxNumber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($boxNumber);
            $em->flush();

            return $this->redirectToRoute('boxnumber_show', array('id' => $boxNumber->getId()));
        }

        return $this->render('GravureBundle:boxnumber:new.html.twig', array(
            'boxNumber' => $boxNumber,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a boxNumber entity.
     *
     * @Route("/{id}", name="boxnumber_show")
     * @Method("GET")
     */
    public function showAction(BoxNumber $boxNumber)
    {
        $deleteForm = $this->createDeleteForm($boxNumber);

        return $this->render('GravureBundle:boxnumber:show.html.twig', array(
            'boxNumber' => $boxNumber,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing boxNumber entity.
     *
     * @Route("/{id}/edit", name="boxnumber_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, BoxNumber $boxNumber)
    {
        $deleteForm = $this->createDeleteForm($boxNumber);
        $editForm = $this->createForm('GravureBundle\Form\BoxNumberType', $boxNumber);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('boxnumber_edit', array('id' => $boxNumber->getId()));
        }

        return $this->render('GravureBundle:boxnumber:edit.html.twig', array(
            'boxNumber' => $boxNumber,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a boxNumber entity.
     *
     * @Route("/{id}", name="boxnumber_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, BoxNumber $boxNumber)
    {
        $form = $this->createDeleteForm($boxNumber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($boxNumber);
            $em->flush();
        }

        return $this->redirectToRoute('boxnumber_index');
    }

    /**
     * Creates a form to delete a boxNumber entity.
     *
     * @param BoxNumber $boxNumber The boxNumber entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(BoxNumber $boxNumber)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('boxnumber_delete', array('id' => $boxNumber->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
