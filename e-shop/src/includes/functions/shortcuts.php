<?php

/**
 * Global shortcut functions.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);


/**
 * Include site header script.
 *
 * @return void
 */
function siteHeader(): void
{
	require_once __DIR__ . '/../site_header.php';
}


/**
 * Include site footer script.
 *
 * @return void
 */
function siteFooter(): void
{
	require_once __DIR__ . '/../site_footer.php';
}
