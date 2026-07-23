<?php


namespace Lynxx;


use bin\Command\AppBuild\AssetsListManager;
use Laminas\Diactoros\Response\HtmlResponse;
use Lynxx\Exception\NotFoundException;
use Lynxx\Exception\ResourceNotFoundException;
use phpDocumentor\Reflection\Types\Mixed_;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class View implements ViewInterface
{
    /** @var string $title text for <title> tag */
    protected $title;
    /** @var array $heads headtags array for <head> block */
    protected $heads = array();
    /** @var array $css_paths css_paths array for <head> block */
    protected $css_paths = array();
    /** @var array $js_paths js_paths array for <head> block */
    protected $js_paths = array();
    /** @var string $layout absolute path to layout file */
    protected $layout;
    /** @var string $layout rendered view html code */
    protected $content;
    /** @var array $data some data for rendered view (usually transmitted from controller) */
    protected $data = array();
    /** @var array $components contains array of rendered components (html code) */
    protected $components = array();

    protected ?string $templatePath = null;

    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }



    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function registerHeadsTag(string $tag): void
    {
        if (in_array($tag, $this->heads)) {
            return;
        }
        $this->heads[] = $tag;
    }


    public function registerCss(string $css_file_path): void
    {
        /** if file not exist, write log and return */
        if (!file_exists(__DIR__ . '/../web' . $css_file_path)) {
            throw new ResourceNotFoundException('не удалось подключить css: '.$css_file_path.'. Файл не найден');
        }

        if ($this->container->get('config')['application_mode'] === 'DEV') {
            $this->registerHeadsTag('<link href="' . $css_file_path . '" rel="stylesheet" type="text/css" />');
        }
    }


    public function registerJs(string $js, array $params)
    {
        /** if file not exist, write log and return */
        if (!file_exists(__DIR__ . '/../web' . $js)) {
            /** writeLog('app_errors', 'не удалось подключить js: '.$js.'. Файл не найден'); */
            return;
        }

        $async = in_array('async', $params) ? 'async' : '';
        $jsTag = '<script ' . $async . ' type="text/javascript" src="' . $js . '"></script>';

        if (!in_array('async', $params)
            || in_array('nocompress', $params)
            || $this->container->get('config')['application_mode'] === 'DEV') {
            $this->registerHeadsTag($jsTag);
        }
    }


    public function render($view_file, $data = []): ResponseInterface
    {
        extract($data);

        $this->templatePath = $view_file;

        ob_start();
        $view_file = __DIR__ . '/../app/templates/' . $view_file;
        if (!file_exists($view_file)) {
            ob_end_clean();
            throw new NotFoundException('template not found');
        }
        include $view_file;
        $content = ob_get_contents();
        ob_end_clean();

        $layout = __DIR__ . '/../app/templates/layout/' . $this->layout;
        if (isset($this->layout) && file_exists($layout = __DIR__ . '/../app/templates/layout/' . $this->layout)) {
            ob_start();
            include $layout;
            $content = ob_get_contents();
            ob_end_clean();
        }

        return (new HtmlResponse($content))
            ->withAddedHeader('X-XSS-Protection', 1)
            ->withAddedHeader('X-Content-Type-Options', 'nosniff')
            ->withAddedHeader('Referrer-Policy', 'no-referrer-when-downgrade');
    }


    public function printResponse(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $k => $values) {
            foreach ($values as $v) {
                header(sprintf('%s: %s', $k, $v), false);
            }
        }
        echo $response->getBody();
    }


    public function registerComponent(string $name, string $component_file, array $data = [])
    {
        extract($data);

        ob_start();
        $component_file = __DIR__ . '/../app/templates/' . $component_file;

        if (!file_exists($component_file)) {
            echo 'Не удалось загрузить компонент ' . $name;
        } else {
            include $component_file;
        }

        $this->components[$name] = ob_get_contents();
        ob_end_clean();
    }



    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function getHeads(): string
    {
        $heads = $this->heads;
        $headsHtml = '';
        for ($i = 0; $i < count($heads); $i++) {
            $headsHtml .= $heads[$i];
        }

        if ($this->container->get('config')['application_mode'] === 'PROD') {
            $headsHtml .= $this->appendProdAssets();
        }

        return $headsHtml;
    }


    private function appendProdAssets(): string
    {
        $assetsHeadHtml = '';

        /** @var AssetsListManager $assetsManager */
        $assetsManager = $this->container->get(AssetsListManager::class);

        $jsFileInfo = $assetsManager->getCompressedAssetInfo($this->templatePath, AssetsListManager::RES_TYPE_JS);
        if (file_exists(__DIR__ . '/../web' . $jsFileInfo['filename'])) {
            $assetsHeadHtml .= '<script async type="text/javascript" src="' . $jsFileInfo['filename'] . '?'.$jsFileInfo['version'].'"></script>';
        }

        $cssFileInfo = $assetsManager->getCompressedAssetInfo($this->templatePath, AssetsListManager::RES_TYPE_CSS);
        if (file_exists(__DIR__ . '/../web' . $cssFileInfo['filename'])) {
            $assetsHeadHtml .= '<link href="' . $cssFileInfo['filename']  . '?'.$cssFileInfo['version'] . '" rel="stylesheet" type="text/css" />';
        }

        return $assetsHeadHtml;
    }



    public function showComponent(string $name): string
    {
        if (!array_key_exists($name, $this->components)) {
            return '';
        }
        return $this->components[$name];
    }


    public function hasComponent(string $name): bool
    {
        if (!array_key_exists($name, $this->components)) {
            return false;
        }
        return true;
    }


}