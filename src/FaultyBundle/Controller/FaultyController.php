<?php

namespace FaultyBundle\Controller;

use FaultyBundle\Entity\Faulty;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Faulty controller.
 *
 * @Route("faulty")
 */
class FaultyController extends Controller
{
    /**
     * Lists all faulty entities.
     *
     * @Route("/", name="faulty_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $faulties = $em->getRepository('FaultyBundle:Faulty')->findAll();

        return $this->render('faulty/index.html.twig', array(
            'faulties' => $faulties,
        ));
    }

    /**
     * Creates a new faulty entity.
     *
     * @Route("/new", name="faulty_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $faulty = new Faulty();
        $form = $this->createForm('FaultyBundle\Form\FaultyType', $faulty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faulty);
            $em->flush();

            return $this->redirectToRoute('faulty_show', array('id' => $faulty->getId()));
        }

        return $this->render('faulty/new.html.twig', array(
            'faulty' => $faulty,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a faulty entity.
     *
     * @Route("/{id}", name="faulty_show")
     * @Method("GET")
     */
    public function showAction(Faulty $faulty)
    {
        $deleteForm = $this->createDeleteForm($faulty);

        return $this->render('faulty/show.html.twig', array(
            'faulty' => $faulty,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing faulty entity.
     *
     * @Route("/{id}/edit", name="faulty_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Faulty $faulty)
    {
        $deleteForm = $this->createDeleteForm($faulty);
        $editForm = $this->createForm('FaultyBundle\Form\FaultyType', $faulty);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $fault = $em->getRepository('FaultyBundle:Faulty')->findById(2);

        $faulty->getProduct()->setSupplier($fault[0]->getProduct()->getSupplier());

        $this->getDoctrine()->getManager()->flush();

        if ($editForm->isSubmitted() && $editForm->isValid()) {


            return $this->redirectToRoute('faulty_edit', array('id' => $faulty->getId()));
        }

        return $this->render('faulty/edit.html.twig', array(
            'faulty' => $faulty,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a faulty entity.
     *
     * @Route("/{id}", name="faulty_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Faulty $faulty)
    {
        $form = $this->createDeleteForm($faulty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faulty);
            $em->flush();
        }

        return $this->redirectToRoute('faulty_index');
    }

    /**
     * Creates a form to delete a faulty entity.
     *
     * @param Faulty $faulty The faulty entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Faulty $faulty)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('faulty_delete', array('id' => $faulty->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
