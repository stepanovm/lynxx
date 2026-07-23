<?php

namespace app\Controller\Errors;

use app\Controller\AbstractViewController;
use Lynxx\AbstractController;
use Lynxx\View;

class Controller404 extends AbstractController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }


    public function run(\Throwable $ex)
    {
        http_response_code(404);

        $response = $this->view->render('error_pages/404.php', [
            'errorMsg' => $ex->getMessage()
        ]);

        $this->view->printResponse($response);
    }
}