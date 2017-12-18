<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CM\ServiceClientBundle\Entity\Solution;
use CM\ServiceClientBundle\Form\SolutionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Solution controller.
 *
 * @Route("solution")
 */

class SolutionController extends Controller
{
    /**
     * @Route("/", name="solution_index")
     */
    public function indexAction()
    {

        $ListeSolution = $this->get('sc.repository.solution')->findAll();


        return $this->render('ServiceClientBundle:solution:index.html.twig', array(
            'solutions' =>$ListeSolution,
        ));

    }

    /**
     * @Route("/new", name="solution_new")
     */
    public function newAction(Request $request){
        $solution = new Solution;
        $form = $this->createForm('CM\ServiceClientBundle\Form\SolutionType', $solution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('sc.repository.solution')->save($solution);

            return $this->redirectToRoute('solution_index');
        }

        return $this->render('ServiceClientBundle:solution:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/show", name="solution_show")
     * @param Solution $solution
     */
    public function showAction(Solution $solution){

        $deleteForm = $this->createDeleteForm($solution);

        return $this->render('ServiceClientBundle:solution:show.html.twig', array(
            'solution' => $solution,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="solution_edit")
     * @param Request $request
     * @param Solution $solution
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, Solution $solution)
    {
        $deleteForm = $this->createDeleteForm($solution);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\SolutionType', $solution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->get('sc.repository.solution')->save($solution);

            return $this->redirectToRoute('solution_index');
        }

        return $this->render('ServiceClientBundle:solution:edit.html.twig', array(
            'solution' => $solution,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="solution_delete")
     * @param Request $request
     * @param Solution $solution
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Solution $solution)
    {
        /*$form = $this->createDeleteForm($carouselProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('shop.repository.carousel.product')->delete($carouselProduct);
        }*/
        $this->get('sc.repository.solution')->delete($solution);

        return $this->redirectToRoute('solution_index');
    }

    private function createDeleteForm(Solution $solution)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('solution_delete', array('id' => $solution->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
