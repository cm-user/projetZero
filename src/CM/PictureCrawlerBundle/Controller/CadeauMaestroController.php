<?php



namespace CM\PictureCrawlerBundle\Controller;





use PictureCrawlerBundle\Crawler\CrawlerCadeauMaestro;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;



class CadeauMaestroController extends Controller

{

    public static function indexAction($url){

        $crawlerAvantGardiste = CrawlerCadeauMaestro::init($url);



        return new JsonResponse(array('data' => $crawlerAvantGardiste));

    }

}