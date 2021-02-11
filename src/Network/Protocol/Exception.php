<?php

namespace Herisson\Network\Protocol;

use Throwable;
use Herisson\Service\Network;

class Exception extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Network::reply($code);
    }

}