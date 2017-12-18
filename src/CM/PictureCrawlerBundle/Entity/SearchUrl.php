<?php
namespace PictureCrawlerBundle\Entity;

class SearchUrl
{
    protected $search;

    public function getSearch()
    {
        return $this->search;
    }

    public function setSearch($search)
    {
        $this->search = $search;
    }
}