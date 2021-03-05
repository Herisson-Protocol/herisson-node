<?php

namespace Herisson\Service\Protocol;

use Herisson\Service\Network\Grabber;
use Throwable;

class Exception extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Grabber::reply($code);
    }

}