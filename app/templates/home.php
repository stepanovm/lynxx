<?php
/** @var \Lynxx\View $this */
/** @var string $name user name */

$this->setLayout('main.php');

$this->registerComponent('exampleComponent', 'example_component.php');

// $this->regi sterJs('/js/jquery-3.5.1.min.js', ['async', 'nocompress']);
// $this->regi sterJs('/js/home_noasync.js', []);
$this->registerJs('/js/main.js', ['async']);

$this->registerCss('/css/main.css');
?>

<h1>Welcome to Lynx Framework</h1>