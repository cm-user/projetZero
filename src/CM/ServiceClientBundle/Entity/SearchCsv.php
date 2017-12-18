<?php
namespace CM\ServiceClientBundle\Entity;

class SearchCsv
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