<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Database;


/**
 * MySQL PDO database wrapper.
 *
 * @package Main
 */
class MySqlDatabase implements IDatabase
{
	/**
	 * PDO connection options
	 */
	private const OPTIONS = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
		\PDO::ATTR_EMULATE_PREPARES => false,
	];


	/**
	 * @var \PDO database connection
	 */
	private $connection;


	/**
	 * Creates database connection with given connection details.
	 *
	 * @param string $host host name
	 * @param string $database database name
	 * @param string $user database username
	 * @param string $password database password
	 *
	 * @throws \PDOException in case of connection error
	 */
	public function __construct(string $host, string $database, string $user, string $password)
	{
		$this->connection = new \PDO("mysql:host=$host;dbname=$database", $user, $password, self::OPTIONS);
	}


	/**
	 * @inheritdoc
	 */
	public function queryOne(string $query, array $parameters = []): array
	{
		return $this->executeStatement($query, $parameters)->fetch(\PDO::FETCH_ASSOC) ?: [];
	}


	/**
	 * @inheritdoc
	 */
	public function queryAll(string $query, array $parameters = []): array
	{
		return $this->executeStatement($query, $parameters)->fetchAll(\PDO::FETCH_ASSOC) ?: [];
	}


	/**
	 * @inheritdoc
	 */
	public function insert(string $table, array $data): int
	{
		$keys = array_keys($data);
		$this->checkIdentifiers($keys + [$table]);

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

		return $this->executeStatement($query, $parameters)->rowCount();
	}


	/**
	 * @inheritdoc
	 */
	public function update(string $table, array $data, string $condition = '', array $parameters = []): int
	{
		$this->checkIdentifiers(array_keys($data) + [$table]);

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

		return $this->executeStatement($query, $parameters)->rowCount();
	}


	/**
	 * Runs query with given parameters and returns PDOStatement.
	 *
	 * @param string $query query to run
	 * @param array $parameters query parameters (name in associative array or unnamed in simple array)
	 * @return \PDOStatement PDOStatement object
	 */
	private function executeStatement(string $query, array $parameters = []): \PDOStatement
	{
		$statement = $this->connection->prepare($query);
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
	private function checkIdentifiers(array $identifiers): void
	{
		foreach ($identifiers as $identifier) {
			if (!preg_match('~^[\w\-]+$~ui', $identifier)) {
				throw new \Exception('Invalid identifier in SQL query.');
			}
		}
	}
}
