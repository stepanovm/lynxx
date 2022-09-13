<?php
/** @var \Lynxx\View $this */
$this->setLayout('main.php');

/** @var string $errorMsg - required string variable */
?>

<h1>Ошибка 404 - запрашиваемый ресурс не найден</h1>

<p>Пожалуйста, обратитесь к</p>

<p><?=$errorMsg?></p>