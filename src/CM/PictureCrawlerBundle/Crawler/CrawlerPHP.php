<?php

namespace PictureCrawlerBundle\Crawler;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerPHP
{
    protected $content;
    protected $url;

    protected function request($url){
        $this->url = $url;
        $client = new Client();
        $client->setDefaultOption('verify', false);
        $response = $client->get($url);
        $this->content = $response->getBody()->getContents();
    }
    
    

    protected function parse(){
        $crawler = new Crawler();
        $crawler->addHtmlContent($this->content);
        return $crawler;
    }
}