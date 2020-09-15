<?php


namespace Lynxx\Request;


use app\config\Config;
use app\Controller\HomeController;

class Request
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var string
     */
    private $referer;

    /**
     * @var mixed
     */
    private $ip;

    /**
     * @var mixed
     */
    private $userAgent;

    public function __construct(Url $url)
    {
        $this->url = $url;
        $this->referer = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING);
        $this->ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
        $this->userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
    }

}