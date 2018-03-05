<?php

/**
 * Initialize configuration.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


require_once __DIR__ . '/classes/Main/Configuration.php';
require_once __DIR__ . '/functions/shortcuts.php';


Configuration::initialize();
