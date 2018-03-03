<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Service;

use Main\Database;
use Main\Security;
use Main\Validators;


/**
 * User service class.
 *
 * @package Main\Service
 */
class UserService
{
	/**
	 * Finds user by email.
	 *
	 * @param string $email user's email
	 * @return array associative array with user
	 */
	public function getUserByEmail(string $email): array
	{
		$query = '
			SELECT *
			FROM `user`
			WHERE `email` = :email
		';

		return Database::queryOne($query, [
			':email' => $email,
		]);
	}


	/**
	 * Update specific user with given new data.
	 *
	 * @param int $id ID of user to be updated
	 * @param array $data associative array with data to be updated
	 * @return void
	 *
	 * @throws \Exception in case of invalid identifiers
	 */
	public function updateUser(int $id, array $data): void
	{
		Database::update('user', $data, 'WHERE id = :id', [
			':id' => $id,
		]);
	}


	/**
	 * Creates new user and performs validation.
	 *
	 * @param string $email user email
	 * @param string $forename user forename
	 * @param string $surname user surname
	 * @param string $password user password
	 * @param string $confirmPassword confirm password (optional)
	 * @return void
	 *
	 * @throws \Exception in case of invalid identifiers
	 * @throws \UnexpectedValueException in case of validation error, user message will be in exception message then
	 */
	public function createUser(
		string $email,
		string $forename,
		string $surname,
		string $password,
		string $confirmPassword
	): void
	{
		if (!Validators::isEmail($email)) {
			throw new \UnexpectedValueException('Please enter a valid email address.');
		}
		if (!Validators::isPassword($password)) {
			throw new \UnexpectedValueException(
				'Your password must be at least 8 characters long, contain letters and numbers.'
			);
		}
		if ($password !== $confirmPassword) {
			throw new \UnexpectedValueException('Fields password and confirm password must matches.');
		}
		if ($this->getUserByEmail($email)) {
			throw new \UnexpectedValueException('Entered email is already registered.');
		}

		Database::insert('user', [
			'email' => $email,
			'forename' => $forename,
			'surname' => $surname,
			'password' => Security\Passwords::hash($password),
		]);
	}
}
