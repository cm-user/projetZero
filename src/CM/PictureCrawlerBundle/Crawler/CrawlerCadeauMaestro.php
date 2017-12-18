<?php

namespace PictureCrawlerBundle\Crawler;

/**
 * Class CrawlerAvantGardiste
 * Parsing de la page de l'avant gardiste
 * @package PictureCrawlerBundle\Crawler
 */
class CrawlerCadeauMaestro extends CrawlerPHP
{
    public static $BASE_URL = "https://www.cadeau-maestro.com/";
    public static $FOLDER = "img/p/";

    protected $imageIds = [];


    public static function init($url){
        $crawlerAvantGardiste = new self();
        $crawlerAvantGardiste->request($url);
        $crawlerAvantGardiste->parse();
        return $crawlerAvantGardiste->createLink();
    }


    public function request($url){
        parent::request($url);
    }

    public function parse()
    {
        $crawler = parent::parse();
        $thumbsListFrame = $crawler->filter('ul#thumbs_list_frame');

        if($thumbsListFrame->count() == 1) {
            $listThumbs = $thumbsListFrame->filter('li')->extract('id');
            foreach ($listThumbs as $listThumb) {
                preg_match("([0-9]+)", $listThumb, $matches);
                if(sizeof($matches)>0) {
                    $this->imageIds[] = $matches[0];
                }
            }
        }
    }

    public function createLink(){
        $links = [];
        foreach ($this->imageIds as $imageId) {
            $arrayId = str_split($imageId);

            $links[] = self::$BASE_URL.self::$FOLDER.implode('/', $arrayId)."/".$imageId.".jpg";
        }
        return $links;
    }
}