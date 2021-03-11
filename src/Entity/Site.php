<?php


namespace Herisson\Entity;


class Site
{

    public $name;
    public $email;
    public $publicKey;
    public $privateKey;
    public $siteurl;
    public $basePath;
    public $validFields = [
        'name', 'email', 'publicKey', 'privateKey', 'siteurl', 'basePath'
    ];

    public function __construct(array $options)
    {
        foreach ($this->validFields as $optionName) {
            if (! array_key_exists($optionName, $options)) {
                continue;
            }
            $this->$optionName = $options[$optionName];
        }
    }

    public function getSitePath()
    {
        return $this->siteurl."/".$this->basePath;

    }




}