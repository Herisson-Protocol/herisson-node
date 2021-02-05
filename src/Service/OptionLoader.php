<?php

namespace App\Service;

use App\Repository\OptionRepository;

class OptionLoader
{
    public $options = [];
    public $optionRepository = [];

    public function __construct(OptionRepository $optionRepository)
    {
        $this->optionRepository = $optionRepository;
    }

    public function load(array $wantedOptions)
    {
        $objectOptions = $this->optionRepository->findAll();
        $options = [];
        foreach ($objectOptions as $objectOption) {
            if ($this->shouldBeSelected($wantedOptions, $objectOption)) {
                $options[$objectOption->getName()] = $objectOption->getValue();
            }
        }
        return $options;
    }

    public function shouldBeSelected($wantedOptions, $objectOption)
    {
        if (!count($wantedOptions) || in_array($objectOption->getName(), $wantedOptions)) {
            return true;
        }
        return false;

    }
}
