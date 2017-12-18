<?php
/**
 * Created by PhpStorm.
 * User: Rémy
 * Date: 24/10/2016
 * Time: 10:41
 */

namespace CM\PictureCrawlerBundle\Controller;


use PictureCrawlerBundle\Entity\SearchUrl;
use PictureCrawlerBundle\Entity\Tools;
use CM\PictureCrawlerBundle\Form\SearchUrlType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CM\PictureCrawlerBundle\Entity\SearchCsv;
use CM\PictureCrawlerBundle\Form\SearchCsvType;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Search controller.
 * @Route("cadeau-maestro")
 */
class SearchCadeauMaestroController extends Controller
{
    /**
     * Affichage du formulaire de téléchargement des images depuis les sites concurrents
     * @Route("/", name="picture_crawler_cm_index")
     * @Template("PictureCrawlerBundle:Search:search-cadeau-maestro.html.twig")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $searchUrl = new SearchUrl();

        $form = $this->createForm(SearchUrlType::class, $searchUrl);
        $form->handleRequest($request);

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Récupération des images sur le site concurrent
     * @Route("/submit", name="picture_crawler_cm_ajax")
     * @param Request $request
     * @return JsonResponse
     */
    public function routeAction(Request $request)
    {
        $url = $request->get('url');
        $urlInfo = Tools::parse_url($url);

        return CadeauMaestroController::indexAction($url);
    }

    /**
     * Affichage du formulaire qui prend en entré un csv avec nom du produit + son url(cadeau-maestro.com)
     * @Route("/csv", name="picture_crawler_cm_csv")
     * @Template("PictureCrawlerBundle:Search:search-cadeau-maestro-csv.html.twig")
     * @param Request $request
     * @return array
     */
    public function csvAction(Request $request)
    {
        $searchCsv = new SearchCsv();

        $form = $this->createForm(SearchCsvType::class, $searchCsv);
        $form->handleRequest($request);

        $dir = "/";
        if ($form->isSubmitted() && $form->isValid()) {
            $someNewFilename = "filename";

        $file = $form['attachment']->getData();
        $file->move($dir, $someNewFilename);

        // ...
    }


        return array(
            'form' => $form->createView(),
        );
    }
}
