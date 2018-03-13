<?php

namespace GravureBundle\Controller;

use GravureBundle\Entity\EndOfTheDayTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Endofthedaytime controller.
 *
 * @Route("endofthedaytime")
 */
class EndOfTheDayTimeController extends Controller
{
    /**
     * Lists all endOfTheDayTime entities.
     *
     * @Route("/", name="endofthedaytime_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $endOfTheDayTimes = $em->getRepository('GravureBundle:EndOfTheDayTime')->findAll();

        return $this->render('GravureBundle:endofthedaytime:index.html.twig', array(
            'endOfTheDayTimes' => $endOfTheDayTimes,
        ));
    }

    /**
     * Creates a new endOfTheDayTime entity.
     *
     * @Route("/new", name="endofthedaytime_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $endOfTheDayTime = new Endofthedaytime();
        $form = $this->createForm('GravureBundle\Form\EndOfTheDayTimeType', $endOfTheDayTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($endOfTheDayTime);
            $em->flush();

            return $this->redirectToRoute('endofthedaytime_show', array('id' => $endOfTheDayTime->getId()));
        }

        return $this->render('GravureBundle:endofthedaytime:new.html.twig', array(
            'endOfTheDayTime' => $endOfTheDayTime,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a endOfTheDayTime entity.
     *
     * @Route("/{id}", name="endofthedaytime_show")
     * @Method("GET")
     */
    public function showAction(EndOfTheDayTime $endOfTheDayTime)
    {
        $deleteForm = $this->createDeleteForm($endOfTheDayTime);

        return $this->render('GravureBundle:endofthedaytime:show.html.twig', array(
            'endOfTheDayTime' => $endOfTheDayTime,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing endOfTheDayTime entity.
     *
     * @Route("/{id}/edit", name="endofthedaytime_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EndOfTheDayTime $endOfTheDayTime)
    {
        $deleteForm = $this->createDeleteForm($endOfTheDayTime);
        $editForm = $this->createForm('GravureBundle\Form\EndOfTheDayTimeType', $endOfTheDayTime);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('endofthedaytime_edit', array('id' => $endOfTheDayTime->getId()));
        }

        return $this->render('GravureBundle:endofthedaytime:edit.html.twig', array(
            'endOfTheDayTime' => $endOfTheDayTime,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a endOfTheDayTime entity.
     *
     * @Route("/{id}", name="endofthedaytime_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EndOfTheDayTime $endOfTheDayTime)
    {
        $form = $this->createDeleteForm($endOfTheDayTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($endOfTheDayTime);
            $em->flush();
        }

        return $this->redirectToRoute('endofthedaytime_index');
    }

    /**
     * Creates a form to delete a endOfTheDayTime entity.
     *
     * @param EndOfTheDayTime $endOfTheDayTime The endOfTheDayTime entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EndOfTheDayTime $endOfTheDayTime)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('endofthedaytime_delete', array('id' => $endOfTheDayTime->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
