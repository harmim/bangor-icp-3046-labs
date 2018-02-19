<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Security;

use Main\Configuration;


/**
 * IAuthenticator implementation.
 * Performs an authentication against database and static credential.
 *
 * @package Main\Security
 */
class Authenticator implements IAuthenticator
{
	/**
	 * @inheritdoc
	 */
	public function authenticate(string $username, string $password): IIdentity
	{
		$errorMessage = 'The credentials you entered are incorrect.';

		if ($username === Configuration::STATIC_USERNAME) {
			// This static user should be in database as user with ID 1 or this static authentication should
			// be deleted later.
			$user = new Identity(1, [
				'email' => $username,
			]);
			$userPassword = Configuration::STATIC_PASSWORD;

		} elseif (false) {
			// TODO: Authentication against database.
			$user = new Identity(0, []);
			$userPassword = '';

		} else {
			throw new AuthenticationException($errorMessage, self::IDENTITY_NOT_FOUND);
		}

		if (!Passwords::verify($password, $userPassword)) {
			throw new AuthenticationException($errorMessage, self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($userPassword)) {
			// TODO: Set new password to user.
			$newPassword = Passwords::hash($password);
		}

		return $user;
	}
}
