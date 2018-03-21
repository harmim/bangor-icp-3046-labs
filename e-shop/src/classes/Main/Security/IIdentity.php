<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Security;


/**
 * Represents the user of application.
 *
 * @package Main\Security
 */
interface IIdentity
{
	/**
	 * Returns user ID.
	 *
	 * @return int user ID
	 */
	function getId(): int;

	/**
	 * Return user data.
	 *
	 * @return array user data
	 */
	function getData(): array;
}
