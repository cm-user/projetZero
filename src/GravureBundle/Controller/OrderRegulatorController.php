<?php

namespace GravureBundle\Controller;

use GravureBundle\Entity\OrderRegulator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Orderregulator controller.
 *
 * @Route("orderregulator")
 */
class OrderRegulatorController extends Controller
{
    /**
     * Lists all orderRegulator entities.
     *
     * @Route("/", name="gravure_orderregulator_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orderRegulators = $em->getRepository('GravureBundle:OrderRegulator')->findAll();

        return $this->render('GravureBundle:orderregulator:index.html.twig', array(
            'orderRegulators' => $orderRegulators,
        ));
    }

    /**
     * Creates a new orderRegulator entity.
     *
     * @Route("/new", name="gravure_orderregulator_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $orderRegulator = new Orderregulator();
        $form = $this->createForm('GravureBundle\Form\OrderRegulatorType', $orderRegulator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderRegulator);
            $em->flush();

            return $this->redirectToRoute('gravure_orderregulator_show', array('id' => $orderRegulator->getId()));
        }

        return $this->render('GravureBundle:orderregulator:new.html.twig', array(
            'orderRegulator' => $orderRegulator,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a orderRegulator entity.
     *
     * @Route("/{id}", name="gravure_orderregulator_show")
     * @Method("GET")
     */
    public function showAction(OrderRegulator $orderRegulator)
    {
        $deleteForm = $this->createDeleteForm($orderRegulator);

        return $this->render('GravureBundle:orderregulator:show.html.twig', array(
            'orderRegulator' => $orderRegulator,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing orderRegulator entity.
     *
     * @Route("/{id}/edit", name="gravure_orderregulator_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OrderRegulator $orderRegulator)
    {
        $deleteForm = $this->createDeleteForm($orderRegulator);
        $editForm = $this->createForm('GravureBundle\Form\OrderRegulatorType', $orderRegulator);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gravure_orderregulator_edit', array('id' => $orderRegulator->getId()));
        }

        return $this->render('GravureBundle:orderregulator:edit.html.twig', array(
            'orderRegulator' => $orderRegulator,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a orderRegulator entity.
     *
     * @Route("/{id}", name="gravure_orderregulator_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderRegulator $orderRegulator)
    {
        $form = $this->createDeleteForm($orderRegulator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderRegulator);
            $em->flush();
        }

        return $this->redirectToRoute('gravure_orderregulator_index');
    }

    /**
     * Creates a form to delete a orderRegulator entity.
     *
     * @param OrderRegulator $orderRegulator The orderRegulator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderRegulator $orderRegulator)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gravure_orderregulator_delete', array('id' => $orderRegulator->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
