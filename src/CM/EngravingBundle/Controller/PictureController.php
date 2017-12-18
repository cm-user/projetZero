<?php

namespace CM\EngravingBundle\Controller;

use CM\EngravingBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



/**
 * Picture controller.
 *
 * @Route("picture")
 */
class PictureController extends Controller
{
    /**
     * Lists all picture entities.
     *
     * @Route("/", name="picture_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pictures = $em->getRepository('EngravingBundle:Picture')->findAll();

        return $this->render('EngravingBundle:picture:index.html.twig', array(
            'pictures' => $pictures,
        ));
    }

    /**
     * Creates a new picture entity.
     *
     * @Route("/new", name="picture_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $picture = new Picture();
        $form = $this->createForm('CM\EngravingBundle\Form\PictureType', $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($picture);
            $em->flush();

            return $this->redirectToRoute('picture_show', array('id' => $picture->getId()));
        }

        return $this->render('EngravingBundle:picture:new.html.twig', array(
            'picture' => $picture,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a picture entity.
     *
     * @Route("/{id}", name="picture_show")
     * @Method("GET")
     */
    public function showAction(Picture $picture)
    {
        $deleteForm = $this->createDeleteForm($picture);

        return $this->render('EngravingBundle:picture:show.html.twig', array(
            'picture' => $picture,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing picture entity.
     *
     * @Route("/{id}/edit", name="picture_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Picture $picture)
    {
        $deleteForm = $this->createDeleteForm($picture);
        $editForm = $this->createForm('CM\EngravingBundle\Form\PictureType', $picture);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('picture_edit', array('id' => $picture->getId()));
        }

        return $this->render('EngravingBundle:picture:edit.html.twig', array(
            'picture' => $picture,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a picture entity.
     *
     * @Route("/{id}", name="picture_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Picture $picture)
    {
        $form = $this->createDeleteForm($picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($picture);
            $em->flush();
        }

        return $this->redirectToRoute('picture_index');
    }

    /**
     * Creates a form to delete a picture entity.
     *
     * @param Picture $picture The picture entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Picture $picture)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('picture_delete', array('id' => $picture->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }    

    /**
     * Lists all picture entities.
     *
     * @Route("/search/by-id-order", name="picture_search")
     * @Method("GET")
     */
    public function SearchAction()
    {
        
        return $this->render('EngravingBundle:picture:search.html.twig', array(

        ));
    }

    /**
     *
     * @Route("/json/{idOrder}", name="picture_search_json", options={"expose"=true})
     *
     */
    public function JsonSearchAction($idOrder){

        $pictures = $this->get('engraving.repository.picture')->FindAllByName($idOrder);
        if(sizeof($pictures) == 0){
            return new JsonResponse("error");
        }
        
        $formatted = [];

        foreach ($pictures as $picture) {
            $formatted[] = [
                'id' => $picture->getId(),
                'name' => $picture->getName(),
                'surname' => $picture->getSurname(),
                'path-jpg' => $picture->getPathJpg(),
                'path-pdf' => $picture->getPathPdf(),
                'category' => ($picture->getCategory() == null) ? 'No_Category' : $picture->getCategory()->getSurname(),
                'id_product' => $picture->getIdProduct(),
                'session' => ($picture->getSession() == null) ? 'No_Session' : $picture->getSession()->getName(),
                'id_config' => $picture->getIdConfig(),
                'machine' => $picture->getMachine(),
                'date_created' => $picture->getCreatedAt(),
                'date_updated' => $picture->getUpdatedAt(),
            ];
        }

        return new JsonResponse($formatted);
    }
}
