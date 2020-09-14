<?php

/**
 * @author StepanovM
 *
 * Main application entry point
 * init global application settings
 */

/** autoload */
require __DIR__ . '/../app/Lynxx/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

/** System configuration */
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');
set_exception_handler('\app\core\Utils::handleException');
if (version_compare(phpversion(), '7.2', '<') == true) { die ('PHP7.2 Only'); }

session_start();