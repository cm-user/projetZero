<?php

namespace CM\ServiceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CM\ServiceClientBundle\Entity\Branch;
use CM\ServiceClientBundle\Form\MailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Mail controller.
 *
 * @Route("liste")
 */

class BranchController extends Controller
{
    /**
     * @Route("/", name="liste_index")
     */
    public function indexAction()
    {



        $ListeBranch = $this->get('sc.repository.branch')->findAll();

//        \Doctrine\Common\Util\Debug::dump($ListeBranch);
//            var_dump($ListeBranch);
        $html='<br>';

        
//        $array = array();
//
//        $array_solution = [];
//        foreach ($ListeBranch as $branch) {
//            $array[] = [
//                'id' => $branch['id'],
//                'nom' => $branch['nom'],
//                'parent_id' => $branch['parent_id'],
//
//            ];
//        }
//
////        $properties = get_object_vars($ListeBranch);
//
//        function afficher_menu($parent, $niveau, $array) {
//
//            $html = "";
//            $array = $this->get('sc.repository.branch')->findByEntityToArray();
//
//            foreach ($array AS $noeud) {
//
//                if ($parent == $noeud['parent_id']) {
//
//                    for ($i = 0; $i < $niveau; $i++) $html .= "-";
//
//                    $html .= " " . $noeud['nom'] . "<br />";
//
//                    $html .= afficher_menu($noeud['id'], ($niveau + 1), $array);
//
//                }
//
//            }
//
//            return $html;
//
//        }
//
//        $html=afficher_menu(null, 0, $ListeBranch);



//        if(!$ListeMail){
//            throw new NotFoundHttpException($this->get('translator')->trans('Liste Mail nul !'));
//        }

        return $this->render('ServiceClientBundle:liste:index.html.twig', array(
            'branches' =>$ListeBranch,
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
     * @Route("/{id}/delete", name="liste_delete")
     * @param Request $request
     * @param Branch $branch
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Branch $branch)
    {

        $this->get('sc.repository.branch')->delete($branch);

        return $this->redirectToRoute('liste_index');
    }

    private function createDeleteForm(Branch $branch)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mail_delete', array('id' => $mail->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
