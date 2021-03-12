<?php


namespace Herisson\Service\Protocol;

use Herisson\Service\Network\Response;
use Herisson\Service\Protocol\ProtocolException as ProtocolException;

class HerissonProtocol
{

    /**
     * @var object
     */
    public $object;

    /**
     * @param Response $response
     * @param array $routing
     * @throws ProtocolException
     */
    public function dispatchResponse(Response $response, array $routing)
    {

        $code = $response->getCode();
        if ($this->checkResponseProtocolIsImplemented($code, $routing)) {
            throw new ProtocolException("Response $code not expected");
        }
        $method = $routing[$code];
        call_user_func([$method[0],$method[1]],$method[2]);
    }


    /**
     * @param int $code
     * @param array $routing
     * @return bool
     */
    public function checkResponseProtocolIsImplemented(int $code, array $routing) : bool
    {
        if (array_key_exists($code, array_keys($routing))) {
            return true;
        }
        return false;
    }

    public function setObject($object) : self
    {
        $this->object = $object;
        return $this;
    }

    public function getObject() : object
    {
        return $this->object;
    }

}