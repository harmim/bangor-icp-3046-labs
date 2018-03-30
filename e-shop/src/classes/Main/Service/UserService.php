<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Service;

use Main\Database;
use Main\Helpers;
use Main\Security;
use Main\ValidationException;
use Main\Validators;
use Nette;


/**
 * User service class.
 *
 * @package Main\Service
 */
class UserService
{
	use Nette\SmartObject;


	/**
	 * @var Database\IDatabase database wrapper
	 */
	private $database;


	/**
	 * Creates user service.
	 *
	 * @param Database\IDatabase $database database wrapper
	 */
	public function __construct(Database\IDatabase $database)
	{
		$this->database = $database;
	}


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

		return $this->database->queryOne($query, [
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

		return $this->database->queryOne($query, [
			':email' => $email,
		]);
	}


	/**
	 * Update specific user with given new data.
	 *
	 * @param int $id ID of user to be updated
	 * @param array $data associative array with data to be updated
	 * @return self
	 *
	 * @throws ValidationException in case of validation error, user message will be in exception message then
	 */
	public function updateUser(int $id, array $data): self
	{
		Helpers::trimArray($data);
		$this->checkUserData($data);
		$this->database->update('user', $data, 'WHERE id = :id', [
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
	 * @return self
	 *
	 * @throws ValidationException in case of validation error, user message will be in exception message then
	 */
	public function createUser(
		string $email,
		string $forename,
		string $surname,
		string $password,
		string $confirmPassword
	): self {
		$data = [
			'email' => $email,
			'forename' => $forename,
			'surname' => $surname,
			'password' => $password,
			'confirmPassword' => $confirmPassword,
		];
		Helpers::trimArray($data);
		$this->checkUserData($data);
		$this->database->insert('user', $data);

		return $this;
	}


	/**
	 * Check and eventually modify given user data.
	 *
	 * @param array $data data to be checked
	 * @return self
	 *
	 * @throws ValidationException in case of validation error, user message will be in exception message then
	 */
	private function checkUserData(array &$data): self
	{
		foreach ($data as $key => &$value) {
			switch ($key) {
				case 'email':
					if (!Validators::isEmail($value)) {
						throw new ValidationException('Please enter a valid email address.');
					}

					if ($this->getUserByEmail($value)) {
						throw new ValidationException('Entered email is already registered.');
					}

					break;

				case 'password':
					if (!Validators::isPassword($value)) {
						throw new ValidationException(
							'Your password must be at least 8 characters long, contain letters and numbers.'
						);
					}

					if (isset($data['confirmPassword'])) {
						if ($value !== $data['confirmPassword']) {
							throw new ValidationException('Fields password and confirm password must matches.');
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
