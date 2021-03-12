<?php


namespace Herisson\Entity;


use Herisson\Service\OptionLoader;

class Site
{

    public $sitename;
    public $email;
    public $publicKey;
    public $privateKey;
    public $siteurl;
    public $sitepath;
    public static $validFields = [
        'sitename', 'email', 'publicKey', 'privateKey', 'siteurl', 'sitepath'
    ];

    public static function createFromOptionLoader(OptionLoader $optionLoader)
    {
        $options = $optionLoader->load(static::$validFields);
        return new Site($options);

    }

    public function __construct(array $options)
    {
        foreach (static::$validFields as $optionName) {
            if (! array_key_exists($optionName, $options)) {
                continue;
            }
            $this->$optionName = $options[$optionName];
        }
    }

    public function getFullSitepath()
    {
        return $this->siteurl."/".$this->sitepath;

    }




}