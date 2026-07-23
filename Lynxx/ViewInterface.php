<?php


namespace Lynxx;


use Lynxx\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;

interface ViewInterface
{
    /**
     * add any data to $this->data
     * @param string $key
     * @param mixed $value
     */
    public function addData(string $key, $value): void;

    /**
     * @param string $layout path to layout file <br /><br />Note: layout file must be placed here <b>/app/templates/layout/</b>
     */
    public function setLayout(string $layout): void;


    /**
     * @param string $title just title text
     */
    public function setTitle(string $title): void;


    /**
     * add new head tag to $this->heads array.
     * if tag already in array, just return;
     * @param string $tag full tag for <head> block
     */
    public function registerHeadsTag(string $tag): void;


    /**
     * register css tag.
     * @param string $css_file_path
     */
    public function registerCss(string $css_file_path): void;


    /**
     * @param string $js
     * @param array $params ('nocompress', 'async')
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function registerJs(string $js, array $params);


    /**
     * @param $view_file
     * @param array $data
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function render($view_file, array $data = []): ResponseInterface;


    /**
     * echo response body
     * @param ResponseInterface $response
     * @return void
     */
    public function printResponse(ResponseInterface $response);



    /**
     * Method add component html content in $this->components array.
     *
     * @param string $name component_name it will use as key in components array
     * @param string $component_file path to component file
     * @param array $data some data, can be used at component as $data['key']
     */
    public function registerComponent(string $name, string $component_file, array $data = []);


    /** @return string $title title text */
    public function getTitle(): ?string;


    /**
     * @return string $headshtml all registered head tags as string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getHeads(): string;


    /**
     * return component html code if exist
     * @param string $name
     * @return string component html code or ''
     */
    public function showComponent(string $name): string;


    /**
     * @param string $name
     * @return boolean
     */
    public function hasComponent(string $name): bool;
}