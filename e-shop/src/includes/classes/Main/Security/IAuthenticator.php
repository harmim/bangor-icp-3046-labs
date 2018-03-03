<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Security;


/**
 * Performs authentication.
 *
 * @package Main\Security
 */
interface IAuthenticator
{
	/**
	 * exception error codes
	 */
	public const
		IDENTITY_NOT_FOUND = 1,
		INVALID_CREDENTIAL = 2;


	/**
	 * Performs an authentication against e.g. database
	 * and returns IIdentity on success or throws AuthenticationException.
	 *
	 * @param string $username username
	 * @param string $password password
	 * @return IIdentity user identity
	 *
	 * @throws AuthenticationException if authentication failed
	 */
	function authenticate(string $username, string $password): IIdentity;
}
