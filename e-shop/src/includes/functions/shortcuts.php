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


/**
 * Shortcut for Main\Strings::escape with string casting.
 *
 * @param mixed $input input for Main\Strings::escape
 * @return string Main\Strings::escape result
 */
function escape($input): string
{
	return Main\Strings::escape((string) $input);
}
