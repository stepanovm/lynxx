<?php

namespace app\Controller\request;

use Laminas\Diactoros\Response\JsonResponse;
use Lynxx\AbstractController;
use Lynxx\Lynxx;
use Lynxx\View;

class DynamicLoadController extends AbstractController
{
    private View $view;

    /**
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }


    public function loadDynamicComponents()
    {
        $this->view->registerComponent('sign', 'components/signBlock.php', [
            'isLoggedIn' => Lynxx::Auth()->isLoggedIn(),
        ]);

        $dynamicComponents = [
            [
                'selector' => '.header-authorization',
                'html' => $this->view->showComponent('sign'),
            ]
        ];

        $response = new JsonResponse(json_encode($dynamicComponents['html']));
        echo $response->getBody();
    }

}