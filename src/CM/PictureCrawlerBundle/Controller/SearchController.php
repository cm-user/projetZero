<?php
///**
// * Created by PhpStorm.
// * User: Rémy
// * Date: 24/10/2016
// * Time: 10:41
// */
//
//namespace PictureCrawlerBundle\Controller;
//
//
//use PictureCrawlerBundle\Entity\SearchUrl;
//use PictureCrawlerBundle\Entity\Tools;
//use PictureCrawlerBundle\Form\SearchUrlType;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Request;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//
///**
// * Search controller.
// * @Route("search")
// */
//class SearchController extends Controller
//{
//    /**
//     * Affichage du formulaire de téléchargement des images depuis les sites concurrents
//     * @Route("/avant-gardiste", name="picture_crawler_index")
//     * @Template("PictureCrawlerBundle:Search:search-avant-gardiste.html.twig")
//     * @param Request $request
//     * @return array
//     */
//    public function indexAction(Request $request)
//    {
//        $searchUrl = new SearchUrl();
//
//        $form = $this->createForm(SearchUrlType::class, $searchUrl);
//        $form->handleRequest($request);
//
//        return array(
//            'form' => $form->createView(),
//        );
//    }
//
//    /**
//     * Récupération des images sur le site concurrent
//     * @Route("/submit", name="picture_crawler_ajax")
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function routeAction(Request $request){
//        $url = $request->get('url');
//        $urlInfo = Tools::parse_url($url);
//        switch ($urlInfo["domain"]){
//            case 'lavantgardiste.com': return AvantGardisteController::indexAction($url);
//            default: return new JsonResponse(array('error' => 'le site web n\'est pas supporté'));
//        }
//    }
//
//
//    /**
//     * Affichage du formulaire de téléchargement des images depuis les sites concurrents
//     * @Route("/cadeau-maestro", name="picture_crawler_cm_index")
//     * @Template("PictureCrawlerBundle:Search:search-cadeau-maestro.html.twig")
//     * @param Request $request
//     * @return array
//     */
//    public function indexCmAction(Request $request)
//    {
//        $searchUrl = new SearchUrl();
//
//        $form = $this->createForm(SearchUrlType::class, $searchUrl);
//        $form->handleRequest($request);
//
//        return array(
//            'form' => $form->createView(),
//        );
//    }
//
//    /**
//     * Récupération des images sur le site concurrent
//     * @Route("/submit-cm", name="picture_crawler_ajax")
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function routeCmAction(Request $request){
//        $url = $request->get('url');
//        $urlInfo = Tools::parse_url($url);
//        switch ($urlInfo["domain"]){
//            case 'lavantgardiste.com': return AvantGardisteController::indexAction($url);
//            default: return new JsonResponse(array('error' => 'le site web n\'est pas supporté'));
//        }
//    }
//}