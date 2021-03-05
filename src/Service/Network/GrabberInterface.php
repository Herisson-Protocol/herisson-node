<?php


namespace Herisson\Service\Network;


interface GrabberInterface
{
    public function getResponse(string $url, $post = []) : Response;
    public function getContent(string $url, $post = []) : string;
    public function check(string $url) : Response;

}