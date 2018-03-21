<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Security;

use Main\Configuration;


/**
 * IAuthenticator implementation.
 * Performs an authentication against database.
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
		static $errorMessage = 'The credentials you entered are incorrect.';
		$userService = Configuration::getUserService();

		if (!($user = $userService->getUserByEmail($username))) {
			throw new AuthenticationException($errorMessage, self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $user['password'])) {
			throw new AuthenticationException($errorMessage, self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($user['password'])) {
			$userService->updateUser($user['id'], [
				'password' => $password,
			]);
		}

		unset($user['password']); // return user without password due to security reasons

		return new Identity($user['id'], $user);
	}
}