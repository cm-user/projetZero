<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CM\ServiceClientBundle\Entity\Guide;
use CM\ServiceClientBundle\Form\GuideType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Guide controller.
 *
 * @Route("guide")
 */

class GuideController extends Controller
{
    /**
     * @Route("/", name="guide_index")
     */
    public function indexAction()
    {

        $ListeGuide = $this->get('sc.repository.guide')->findAll();

        return $this->render('ServiceClientBundle:guide:index.html.twig', array(
            'guides' =>$ListeGuide,
        ));

    }

    /**
     * @Route("/new", name="guide_new")
     */
    public function newAction(Request $request){
        $guide = new Guide;
        $form = $this->createForm('CM\ServiceClientBundle\Form\GuideType', $guide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('sc.repository.guide')->save($guide);

            return $this->redirectToRoute('guide_index');
        }

        return $this->render('ServiceClientBundle:guide:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/json", name="guide_show_json", options={"expose"=true})
     * @param Guide $guide
     */
    public function showAction(){

        $ListeGuide = $this->get('sc.repository.guide')->findAll();

        $formatted = [];
        foreach ($ListeGuide as $guide) {
            $formatted[] = [
                'text' => $guide->getText(),
            ];
        }
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/{id}/edit", name="guide_edit")
     * @param Request $request
     * @param Guide $guide
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, Guide $guide)
    {
        $deleteForm = $this->createDeleteForm($guide);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\GuideType', $guide);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $date = new \DateTime('NOW');
            $guide->setUpdatedAt($date);
                $this->get('sc.repository.guide')->save($guide);
                return $this->redirectToRoute('guide_index');

        }

        return $this->render('ServiceClientBundle:guide:edit.html.twig', array(
            'guide' => $guide,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="guide_delete")
     * @param Request $request
     * @param Guide $guide
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Guide $guide)
    {

        $this->get('sc.repository.guide')->delete($guide);

        return $this->redirectToRoute('guide_index');
    }

    private function createDeleteForm(Guide $guide)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('guide_delete', array('id' => $guide->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
