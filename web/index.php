<?php

/**
 * @author StepanovM
 *
 * Main application entry point
 */

require __DIR__ . '/../vendor/autoload.php';

$app = new \Lynxx\Lynxx();
$app->run();