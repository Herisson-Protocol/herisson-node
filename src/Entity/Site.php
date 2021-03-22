<?php


namespace Herisson\Entity;


use Herisson\Repository\OptionRepositoryInterface;
use Herisson\Service\OptionLoader;

class Site
{

    public $sitename;
    public $email;
    public $publicKey;
    public $privateKey;
    public $siteurl;
    public $sitepath;

    public const PARAM_EMAIL = 'email';
    public const PARAM_PRIVATEKEY = 'privateKey';
    public const PARAM_PUBLICKEY = 'publicKey';
    public const PARAM_SITENAME = 'sitename';
    public const PARAM_SITEPATH = 'sitepath';
    public const PARAM_SITEURL = 'siteurl';
    public const PARAM_VERSION = 'version';

    public static $validFields = [
        Site::PARAM_EMAIL,
        Site::PARAM_PRIVATEKEY,
        Site::PARAM_PUBLICKEY,
        Site::PARAM_SITENAME,
        Site::PARAM_SITEPATH,
        Site::PARAM_SITEURL,
        Site::PARAM_VERSION,
    ];

    public static function createFromOptionLoader(OptionLoader $optionLoader)
    {
        $options = $optionLoader->load(static::$validFields);
        return new Site($options);

    }

    public static function createFromOptionRepository(OptionRepositoryInterface $optionRepository)
    {
        return Site::createFromOptionLoader(new OptionLoader($optionRepository));

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