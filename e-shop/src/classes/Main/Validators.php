<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;

use Nette;


/**
 * Validation utilities.
 *
 * @package Main
 */
class Validators extends Nette\Utils\Validators
{
	/**
	 * Check if given string is valid password.
	 * Valid password must be at least 8 characters long, contain letters and numbers.
	 *
	 * @param string $value string to check if it is valid password
	 * @return bool true if it is valid password, false otherwise
	 */
	public static function isPassword(string $value): bool
	{
		if (Nette\Utils\Strings::length($value) < 8) {
			return false;
		}

		return (bool) preg_match('~(?=.*[a-z])(?=.*\d).*\z~i', $value);
	}
}
