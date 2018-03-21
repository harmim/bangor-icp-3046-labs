<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;


/**
 * Validation utilities.
 *
 * @package Main
 */
class Validators
{
	/**
	 * Finds whether a string is a valid email address.
	 *
	 * @param string $value string to check if it is email
	 * @return bool true if it is valid email, false otherwise
	 */
	public static function isEmail(string $value): bool
	{
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$alpha = "a-z\x80-\xFF"; // superset of IDN

		return (bool) preg_match("(^
			(\"([ !#-[\\]-~]*|\\\\[ -~])+\"|$atom+(\\.$atom+)*)  # quoted or unquoted
			@
			([0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)+    # domain - RFC 1034
			[$alpha]([-0-9$alpha]{0,17}[$alpha])?                # top domain
		\\z)ix", $value);
	}


	/**
	 * Check if given string is valid password.
	 * Valid password must be at least 8 characters long, contain letters and numbers.
	 *
	 * @param string $value string to check if it is valid password
	 * @return bool true if it is valid password, false otherwise
	 */
	public static function isPassword(string $value): bool
	{
		if (Strings::length($value) < 8) {
			return false;
		}

		return (bool) preg_match('~(?=.*[a-z])(?=.*\d).*\z~i', $value);
	}
}
