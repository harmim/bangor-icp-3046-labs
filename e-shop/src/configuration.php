<?php

/**
 * Initialize configuration.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


define('__ROOT_DIR__', realpath(__DIR__ . '/..'));
define('__SRC_DIR__', __ROOT_DIR__ . '/src');
define('__LOG_DIR__', __ROOT_DIR__ . '/log');


require_once __ROOT_DIR__ . '/vendor/autoload.php';
require_once __SRC_DIR__ . '/classes/Main/Configuration.php';
require_once __SRC_DIR__ . '/functions/shortcuts.php';


Configuration::initialize();
