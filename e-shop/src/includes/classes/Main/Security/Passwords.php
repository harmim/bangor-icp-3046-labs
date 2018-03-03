<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Security;


/**
 * Passwords tools.
 *
 * @package Main\Security
 */
class Passwords
{
	/**
	 * Computes password hash.
	 *
	 * @param string $password the user's password
	 * @return string the hashed password
	 *
	 * @throws \UnexpectedValueException if computed hash is invalid
	 */
	public static function hash(string $password): string
	{
		$hash = password_hash($password, PASSWORD_BCRYPT);
		if ($hash === false) {
			throw new \UnexpectedValueException('Hash computed by password_hash is invalid.');
		}

		return $hash;
	}


	/**
	 * Verifies that a password matches a hash.
	 *
	 * @param string $password the user's password
	 * @param string $hash a hash created by Main\Security\Passwords::hash
	 * @return bool true if the password and hash match, or false otherwise
	 */
	public static function verify(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}


	/**
	 * Checks if the given hash matches the options.
	 *
	 * @param string $hash a hash created by Main\Security\Passwords::hash
	 * @return bool true if the hash should be rehashed to match the specific algorithm and options, or false otherwise.
	 */
	public static function needsRehash(string $hash): bool
	{
		return password_needs_rehash($hash, PASSWORD_BCRYPT);
	}
}
