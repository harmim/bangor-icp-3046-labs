<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;

use Main\Renderable;
use PDO;
use PDOException;
use PDOStatement;


/**
 * MySQL PDO wrapper.
 *
 * @package Main
 */
class Database
{
	/**
	 * PDO connection options
	 */
	private const OPTIONS = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
		PDO::ATTR_EMULATE_PREPARES => false,
	];

	/**
	 * error user message to be printed in case of error
	 */
	private const ERROR_MESSAGE = 'Database error.';


	/**
	 * @var string host name
	 */
	private static $host;

	/**
	 * @var string database name
	 */
	private static $database;

	/**
	 * @var string database username
	 */
	private static $user;

	/**
	 * @var string database password
	 */
	private static $password;

	/**
	 * @var PDO database connection
	 */
	private static $connection;


	/**
	 * Sets connection details for later connecting.
	 *
	 * @param string $host host name
	 * @param string $database database name
	 * @param string $user database username
	 * @param string $password database password
	 * @return void
	 */
	public static function initialize(string $host, string $database, string $user, string $password): void
	{
		self::$host = $host;
		self::$database = $database;
		self::$user = $user;
		self::$password = $password;
	}


	/**
	 * Runs query with given parameters and returns first row.
	 *
	 * @param string $query query to run
	 * @param array $parameters query parameters (name in associative array or unnamed in simple array)
	 * @return array first row as associative array
	 */
	public static function queryOne(string $query, array $parameters = []): array
	{
		if (!self::connect()) {
			return [];
		}

		return self::executeStatement($query, $parameters)->fetch(PDO::FETCH_ASSOC) ?: [];
	}


	/**
	 * Runs query with given parameters and returns all resulting rows.
	 *
	 * @param string $query query to run
	 * @param array $parameters query parameters (name in associative array or unnamed in simple array)
	 * @return array array of resulting rows
	 */
	public static function queryAll(string $query, array $parameters = []): array
	{
		if (!self::connect()) {
			return [];
		}

		return self::executeStatement($query, $parameters)->fetchAll(PDO::FETCH_ASSOC) ?: [];
	}


	/**
	 * Insert row to table with given data.
	 *
	 * @param string $table table name to insert to
	 * @param array $data associative array with data to insert
	 * @return int number of affected rows
	 *
	 * @throws \Exception in case of invalid identifiers
	 */
	public static function insert(string $table, array $data): int
	{
		if ( ! self::connect()) {
			return 0;
		}

		$keys = array_keys($data);
		self::checkIdentifiers($keys + [$table]);

		$query = "INSERT INTO `$table` (`" . implode('`, `', $keys) . '`) VALUES (';
		$i = 0;
		$parameters = [];
		foreach ($data as $key => $value) {
			$i++;
			$paramName = ":$key";

			$query .= $paramName;
			if ($i !== count($data)) {
				$query .= ', ';
			}

			$parameters[$paramName] = $value;
		}
		$query .= ')';

		return self::executeStatement($query, $parameters)->rowCount();
	}


	/**
	 * Update table with given data and condition.
	 *
	 * @param string $table table name to update
	 * @param array $data associative array with data to update
	 * @param string $condition update condition
	 * @param array $parameters extra condition named parameters in associative array
	 * @return int number of affected rows
	 *
	 * @throws \Exception in case of invalid identifiers
	 */
	public static function update(string $table, array $data, string $condition = '', array $parameters = []): int
	{
		if (!self::connect()) {
			return 0;
		}

		self::checkIdentifiers(array_keys($data) + [$table]);

		$query = "UPDATE `$table` SET ";
		$i = 0;
		foreach ($data as $key => $value) {
			$i++;
			$paramName = ":update$key";

			$query .= "`$key` = $paramName";
			if ($i !== count($data)) {
				$query .= ', ';
			}

			$parameters[$paramName] = $value;
		}
		$query .= " $condition";

		return self::executeStatement($query, $parameters)->rowCount();
	}


	/**
	 * Creates database connection with given connection details only if connection hasn't been already created.
	 *
	 * @return bool false in case of error, true otherwise
	 */
	private static function connect(): bool
	{
		if (!self::$connection) {
			try {
				$dns = 'mysql:host=' . self::$host . ';dbname=' . self::$database;
				self::$connection = new PDO($dns, self::$user, self::$password, self::OPTIONS);

			} catch (PDOException $e) {
				Renderable\Messages::addMessage(self::ERROR_MESSAGE, Renderable\Messages::TYPE_DANGER);
				return false;
			}
		}

		return true;
	}


	/**
	 * Runs query with given parameters and returns PDOStatement.
	 *
	 * @param string $query query to run
	 * @param array $parameters query parameters (name in associative array or unnamed in simple array)
	 * @return PDOStatement PDOStatement object
	 */
	private static function executeStatement(string $query, array $parameters = []): PDOStatement
	{
		$statement = self::$connection->prepare($query);
		$statement->execute($parameters);

		return $statement;
	}


	/**
	 * Check if database identifiers are valid.
	 *
	 * @param array $identifiers array of identifiers
	 * @return void
	 *
	 * @throws \Exception if there are some invalid identifiers
	 */
	private static function checkIdentifiers(array $identifiers): void
	{
		foreach ($identifiers as $identifier) {
			if (!preg_match('~^[\w\-]+$~ui', $identifier)) {
				throw new \Exception('Invalid identifier in SQL query.');
			}
		}
	}
}
