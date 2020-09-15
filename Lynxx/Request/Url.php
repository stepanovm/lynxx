<?php


namespace Lynxx\Request;



class Url
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $url;

    public function __construct()
    {
        $this->url = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        if (!filter_var('http://' . filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL) . $this->url, FILTER_VALIDATE_URL)) {
            throw new \Exception('bad url requested');
        }
        $urlData = parse_url($this->url);
        $this->path = $urlData['path'] ?? null;
        $this->query = $urlData['query'] ?? null;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }




}