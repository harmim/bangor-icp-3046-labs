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
	 * Finds user by ID.
	 *
	 * @param int $id user's ID
	 * @return array associative array with user
	 */
	public function getUserById(int $id): array
	{
		$query = '
			SELECT *
			FROM `user`
			WHERE `id` = :id
		';

		return Database::queryOne($query, [
			':id' => $id,
		]);
	}


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
	 * @return UserService self
	 *
	 * @throws \Exception in case of invalid identifiers
	 * @throws \UnexpectedValueException in case of validation error, user message will be in exception message then
	 */
	public function updateUser(int $id, array $data): UserService
	{
		$this->checkUserData($data);
		Database::update('user', $data, 'WHERE id = :id', [
			':id' => $id,
		]);

		return $this;
	}


	/**
	 * Creates new user and performs validation.
	 *
	 * @param string $email user email
	 * @param string $forename user forename
	 * @param string $surname user surname
	 * @param string $password user password
	 * @param string $confirmPassword confirm password (optional)
	 * @return UserService self
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
	): UserService
	{
		$data = [
			'email' => $email,
			'forename' => $forename,
			'surname' => $surname,
			'password' => $password,
			'confirmPassword' => $confirmPassword,
		];
		$this->checkUserData($data);
		unset($data['confirmPassword']);
		Database::insert('user', $data);

		return $this;
	}


	/**
	 * Check and eventually modify given user data.
	 *
	 * @param array $data data to be checked
	 * @return UserService self
	 *
	 * @throws \UnexpectedValueException in case of validation error, user message will be in exception message then
	 */
	private function checkUserData(array &$data): UserService
	{
		foreach ($data as $key => &$value) {
			switch ($key) {
				case 'email':
					if (!Validators::isEmail($value)) {
						throw new \UnexpectedValueException('Please enter a valid email address.');
					}

					if ($this->getUserByEmail($value)) {
						throw new \UnexpectedValueException('Entered email is already registered.');
					}

					break;

				case 'password':
					if (!Validators::isPassword($value)) {
						throw new \UnexpectedValueException(
							'Your password must be at least 8 characters long, contain letters and numbers.'
						);
					}

					if (isset($data['confirmPassword'])) {
						if ($value !== $data['confirmPassword']) {
							throw new \UnexpectedValueException('Fields password and confirm password must matches.');
						}
						unset($data['confirmPassword']);
					}

					$value = Security\Passwords::hash($value);

					break;
			}
		}

		return $this;
	}
}
