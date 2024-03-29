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
	require_once __SRC_DIR__ . '/templates/site_header.phtml';
}


/**
 * Include site footer script.
 *
 * @return void
 */
function siteFooter(): void
{
	require_once __SRC_DIR__ . '/templates/site_footer.phtml';
}


/**
 * Shortcut for Main\Helpers::escape with string casting.
 *
 * @param mixed $input input for Main\Helpers::escape
 * @return string Main\Helpers::escape result
 */
function escape($input): string
{
	return Main\Helpers::escape((string) $input);
}
