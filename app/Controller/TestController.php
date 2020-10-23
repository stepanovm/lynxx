<?php


namespace app\Controller;


use Laminas\Diactoros\Response\HtmlResponse;
use Lynxx\AbstractController;
use Psr\Http\Message\ServerRequestInterface;

class TestController extends AbstractController
{

    private $request;

    /**
     * TestController constructor.
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function test()
    {
        $form = "
            <form method='post'>
                <input type='text' name='testFormNameInput' value='' />
                <input type='submit' value='ok'>
            </form>
        ";
        return new HtmlResponse(
            $form
            . '<br />hello from test controller with request:<br/> <pre>'
            . print_r($this->request->getParsedBody(), true)
            . '</pre>'
            . '<p>'.print_r($this->request->getQueryParams(), true) .'</p>'
        );
    }
}