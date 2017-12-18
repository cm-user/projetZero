<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CM\ServiceClientBundle\Entity\Phone;
use CM\ServiceClientBundle\Form\PhoneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Phone controller.
 *
 * @Route("phone")
 */

class PhoneController extends Controller
{
    /**
     * @Route("/", name="phone_index")
     */
    public function indexAction()
    {

        $ListePhone = $this->get('sc.repository.phone')->findAll();

        return $this->render('ServiceClientBundle:phone:index.html.twig', array(
            'phones' =>$ListePhone,
        ));

    }

    /**
     * @Route("/new", name="phone_new")
     */
    public function newAction(Request $request){
        $phone = new Phone;
        $form = $this->createForm('CM\ServiceClientBundle\Form\PhoneType', $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('sc.repository.phone')->save($phone);

            return $this->redirectToRoute('phone_index');
        }

        return $this->render('ServiceClientBundle:phone:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/json", name="phone_show_json", options={"expose"=true})
     * @param Phone $phone
     */
    public function showAction(){

        $ListePhone = $this->get('sc.repository.phone')->findAll();

        $formatted = [];
        foreach ($ListePhone as $phone) {
            $formatted[] = [
                'text' => $phone->getText(),
            ];
        }
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/{id}/edit", name="phone_edit")
     * @param Request $request
     * @param Phone $phone
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, Phone $phone)
    {
        $deleteForm = $this->createDeleteForm($phone);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\PhoneType', $phone);
        $editForm->handleRequest($request);        

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $date = new \DateTime('NOW');
            $phone->setUpdatedAt($date);
                $this->get('sc.repository.phone')->save($phone);
                return $this->redirectToRoute('phone_index');
            
        }

        return $this->render('ServiceClientBundle:phone:edit.html.twig', array(            
            'phone' => $phone,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="phone_delete")
     * @param Request $request
     * @param Phone $phone
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Phone $phone)
    {

        $this->get('sc.repository.phone')->delete($phone);

        return $this->redirectToRoute('phone_index');
    }

    private function createDeleteForm(Phone $phone)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('phone_delete', array('id' => $phone->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}