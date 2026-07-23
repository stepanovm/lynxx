<?php

namespace app\Controller\Errors;

use app\Controller\AbstractViewController;
use Lynxx\AbstractController;
use Lynxx\View;

class SiteErrorController extends AbstractController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function run(\Throwable $ex)
    {
        $response = $this->view->render('error_pages/site_error_page.php', [
            'errorMsg' => $ex->getMessage()
        ]);

        $this->view->printResponse($response);
    }
}