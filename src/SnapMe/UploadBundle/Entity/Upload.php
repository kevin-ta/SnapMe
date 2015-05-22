<?php

namespace SnapMe\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Upload
 */
class Upload
{
    /**
     * @var string
     */
    private $url;


    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * @var integer
     */
    private $userid;


    /**
     * Set url
     *
     * @param string $url
     * @return Upload
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get userid
     *
     * @return integer 
     */
    public function getUserid()
    {
        return $this->userid;
    }
}