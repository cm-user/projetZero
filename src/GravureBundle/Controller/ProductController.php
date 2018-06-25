<?php
/**
 * Created by PhpStorm.
 * User: Okaou
 * Date: 20/03/2018
 * Time: 17:47
 */

namespace GravureBundle\Controller;

use GravureBundle\Entity\Domain\Product;
use GravureBundle\Form\ProductSubmission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 * @Route("product")
 */
class ProductController extends Controller
{

    /**
     * @Route("/", name="gravure_product_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $products = $this->get('repositories.product')->findAll();

        return $this->render('GravureBundle:product:index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * @Route("/new", name="gravure_product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $productSubmission = new ProductSubmission();
        $form = $this->createForm('GravureBundle\Form\Types\ProductType',
            $productSubmission,
            array('categoryRepository' => $this->get('repositories.category')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $persta = $this->get('iq2i_prestashop_web_service')->getInstance(); //instance prestashop web service

            //Mise à jour de toutes les gravures sans produit type lié
            $gravures = $this->get('repositories.gravure')->findAllWithoutProduct();

            foreach ($gravures as $gravure) {

                //recherche de l'id du produit prestashop en fonction de l'id config
                $result_config_cart = $persta->get(array(
                    "resource" => "config_carts",
                    "filter[id]" => '[' . $gravure['config_id'] . ']',
                    "display" => '[id_product]',
                ));

                $result_config_cart = json_decode(json_encode((array)$result_config_cart), TRUE);
                $productId = $result_config_cart['config_carts']['config_cart']['id_product'];


                //////Methode pour récupérer l'id produit de chaque gravure pour trouver son produit lié///////
                $Product = $this->get('repositories.product')->findByProductId($productId);
                if ($Product != null) {
//                    dump($Product);die;

                    $idProduct = $Product['id'];
                    //Ajout de l'id du produit type à la gravure
                    $this->get('repositories.gravure')->updateIdProduct($gravure['id'], $idProduct);
                }

            }

            $product = Product::addProduct($productSubmission);

            $this->get('repositories.product')->save($product);

            return $this->redirectToRoute('gravure_product_index');
        }

        return $this->render('GravureBundle:product:new.html.twig', array(
            'product' => $productSubmission,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="gravure_product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        $product = $this->get('repositories.product')->findById($id);
        $product->setId($id);

        $deleteForm = $this->createDeleteForm($product);
//        $editForm = $this->createForm('GravureBundle\Form\Types\ProductType', $product);
        $editForm = $this->createForm('GravureBundle\Form\Types\ProductType',
            $product,
            array('categoryRepository' => $this->get('repositories.category')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->get('repositories.product')->update($product);

            return $this->redirectToRoute('gravure_product_index');
        }

        return $this->render('GravureBundle:product:edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="gravure_product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $product = $this->get('repositories.product')->findById($id);
        $product->setId($id);

        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('repositories.product')->delete($id);
        }

        return $this->redirectToRoute('gravure_product_index');
    }

    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gravure_product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}