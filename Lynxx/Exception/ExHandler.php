<?php

namespace Lynxx\Exception;


use Lynxx\Logger;
use Lynxx\Lynxx;
use PDOException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;


class ExHandler
{
    public static function handle(\Throwable $ex)
    {

        $container = Lynxx::getContainer();
        if(!$container->has(RequestInterface::class)) {
            $container->set(RequestInterface::class, $container->get('default_request'));
        }

        $logger = $container->get(LoggerInterface::class);

        if ($container->has('exceptionDependencies')) {
            $exceptionDependencies = $container->get('exceptionDependencies');
            $exClass = get_class($ex);
            if (array_key_exists($exClass, $exceptionDependencies) && $exceptionDependencies[$exClass] instanceof \Closure) {
                $exceptionDependencies[$exClass]($container, $ex);
                return;
            }
        }

        $logger->error($ex->getMessage(), ['throwable' => $ex]);
        echo "Неизвестная ошибка...\n" . $ex->getMessage();
        die();



        /**
        if ($ex instanceof PDOException) {
            $logger->error($ex->getMessage(), ['throwable' => $ex]);
            $errorpage = new ControllerSiteError('<p>Не удалось загрузить данные...</p>' . Utils::debugObj($ex));
            $errorpage->run();
        } else if ($ex instanceof NotFoundException) {
            if (stripos(Utils::getHttpReferer(), 'bot') === false) {
                $logger->warning($ex->getMessage(), ['throwable' => $ex]);
            }
            $page404 = new Controller404($ex->getMessage());
            $page404->run();
            return;
        } else if ($ex instanceof SiteErrorException) {
            $logger->error($ex->getMessage(), ['throwable' => $ex]);
            $errorpage = new ControllerSiteError($ex->getMessage());
            $errorpage->run();
        } else if ($ex instanceof AccessForbiddenException) {
            $logger->error($ex->getMessage(), ['throwable' => $ex]);
            $errorpage = new ControllerSiteError($ex->getMessage());
            $errorpage->run();
        } else {
            $logger->error($ex->getMessage(), ['throwable' => $ex]);
            echo "Неизвестная ошибка...\n" . $ex->getMessage();
            echo Utils::debugObj($ex);
        }
         */
    }
}