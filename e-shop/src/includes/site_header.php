<?php

/**
 * Header each of page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Renderable\Messages;


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once __DIR__ . '/head.php'; ?>
</head>
<body>
	<header>
		<?php require __DIR__ . '/header.php'; ?>
	</header>

	<main role="main" class="container">
		<?php Messages::render(); ?>
