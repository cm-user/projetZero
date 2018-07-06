<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 16/03/2018
 * Time: 12:08
 */

namespace GravureBundle\Controller;

use GravureBundle\Entity\Domain\Category;
use GravureBundle\Form\CategorySubmission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 * @Route("category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="category_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $categories = $this->get('repositories.category')->findAll();

        return $this->render('GravureBundle:category:index.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/new", name="category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $categorySubmission = new CategorySubmission();
        $form = $this->createForm('GravureBundle\Form\Types\CategoryType',
            $categorySubmission,
            array('machineRepository' => $this->get('repositories.machine')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = Category::addCategory($categorySubmission);

            $file = $category->getPathGabarit(); //récupére le chemin du fichier
            $fileName = $category->getSurname().".".$file->guessExtension(); //construit le fichier avec le nom donnée par l'utilisateur et son extension
            //déplace le fichier dans le bon repertoire
            $file->move(
                $this->getParameter('gravure_gabarit_directory'),
                $fileName
            );
            $category->setPathGabarit($this->getParameter('gravure_gabarit_url').$fileName); //renseigne le nouveau chemin sur le serveur

            $this->get('repositories.category')->save($category);

            return $this->redirectToRoute('category_index');
        }

        return $this->render('GravureBundle:category:new.html.twig', array(
            'category' => $categorySubmission,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        $category = $this->get('repositories.category')->findById($id);
        $category->setId($id);

        $deleteForm = $this->createDeleteForm($category);
//        $editForm = $this->createForm('GravureBundle\Form\Types\CategoryType', $category);
        $editForm = $this->createForm('GravureBundle\Form\Types\CategoryType',
            $category,
            array('machineRepository' => $this->get('repositories.machine')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $file = $category->getPathGabarit(); //récupére le chemin du fichier
            $fileName = $category->getSurname().".".$file->guessExtension(); //construit le fichier avec le nom donnée par l'utilisateur et son extension
            //déplace le fichier dans le bon repertoire
            $file->move(
                $this->getParameter('gravure_gabarit_directory'),
                $fileName
            );
            $category->setPathGabarit($this->getParameter('gravure_gabarit_url').$fileName); //renseigne le nouveau chemin sur le serveur

            $this->get('repositories.category')->update($category);

            return $this->redirectToRoute('category_index');
        }

        return $this->render('GravureBundle:category:edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $category = $this->get('repositories.category')->findById($id);
        $category->setId($id);

        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $path = $category->getPathGabarit();
            if(file_exists($path)){
                unlink($path); //suppression du pdf
            }
            $this->get('repositories.category')->delete($id);
        }

        return $this->redirectToRoute('category_index');
    }

    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $category->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }


    /**
     * @Route("/import/category", name="category_import_category")
     */
    public function importCategory()
    {
        $fileName = $this->getParameter('gravure_gabarit_directory') . 'gravure.csv';

            $row = 1;
if (($handle = fopen($fileName, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
        $category = new Category($data[4], $data[0], $data[1], $data[2], "path", $data[3]);
        $this->get('repositories.category')->save($category);
    }
    fclose($handle);
}


            return new JsonResponse("Importation csv réussi ");
      }
}