<?php

declare(strict_types=1);

require_once __DIR__ . '/configure.php';

if (!DEBUG) {
	@ini_set('display_errors', 0);
	error_reporting(0);
}
