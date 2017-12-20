<?php

namespace CM\ServiceClientBundle\Entity;


class UploadZip
{
    protected $zip;

    public function getZip()
    {
        return $this->zip;
    }

    public function setZip($zip)
    {
        $this->zip = $zip;
    }
}

