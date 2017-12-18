<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CM\ServiceClientBundle\Entity\Mail;
use CM\ServiceClientBundle\Form\MailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Mail controller.
 *
 * @Route("mail")
 */

class MailController extends Controller
{
    /**
     * @Route("/", name="mail_index")
     */
    public function indexAction()
    {
    
        $ListeMail = $this->get('sc.repository.mail')->findByAsc();

//        if(!$ListeMail){
//            throw new NotFoundHttpException($this->get('translator')->trans('Liste Mail nul !'));
//        }

        return $this->render('ServiceClientBundle:mail:index.html.twig', array(
            'mails' =>$ListeMail,
        ));

    }

    /**
     * @Route("/new", name="mail_new")
     */
    public function newAction(Request $request){
        $mail = new Mail;
        $form = $this->createForm('CM\ServiceClientBundle\Form\MailType', $mail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('sc.repository.mail')->save($mail);
            
            return $this->redirectToRoute('mail_index');
        }

        return $this->render('ServiceClientBundle:mail:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/show", name="mail_show")
     * @param Mail $mail
     */
    public function showAction(Mail $mail){
        
        $deleteForm = $this->createDeleteForm($mail);

        return $this->render('ServiceClientBundle:mail:show.html.twig', array(
            'mail' => $mail,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="mail_edit")
     * @param Request $request
     * @param Mail $mail
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, Mail $mail)
    {
        $deleteForm = $this->createDeleteForm($mail);
        $editForm = $this->createForm('CM\ServiceClientBundle\Form\MailType', $mail);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->get('sc.repository.mail')->save($mail);
            
            return $this->redirectToRoute('mail_index');
        }

        return $this->render('ServiceClientBundle:mail:edit.html.twig', array(
            'mail' => $mail,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="mail_delete")
     * @param Request $request
     * @param Mail $mail
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Mail $mail)
    {
        /*$form = $this->createDeleteForm($carouselProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('shop.repository.carousel.product')->delete($carouselProduct);
        }*/
        $this->get('sc.repository.mail')->delete($mail);

        return $this->redirectToRoute('mail_index');
    }
    
    private function createDeleteForm(Mail $mail)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mail_delete', array('id' => $mail->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
