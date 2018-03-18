<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Database;


/**
 * Database wrapper interface.
 *
 * @package Main\Database
 */
interface IDatabase
{
	/**
	 * Runs query with given parameters and returns first row.
	 *
	 * @param string $query query to run
	 * @param array $parameters query parameters (name in associative array or unnamed in simple array)
	 * @return array first row as associative array
	 */
	function queryOne(string $query, array $parameters = []): array;


	/**
	 * Runs query with given parameters and returns all resulting rows.
	 *
	 * @param string $query query to run
	 * @param array $parameters query parameters (name in associative array or unnamed in simple array)
	 * @return array array of resulting rows
	 */
	function queryAll(string $query, array $parameters = []): array;


	/**
	 * Insert row to table with given data.
	 *
	 * @param string $table table name to insert to
	 * @param array $data associative array with data to insert
	 * @return int number of affected rows
	 *
	 * @throws \Exception in case of invalid identifiers
	 */
	function insert(string $table, array $data): int;


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
	function update(string $table, array $data, string $condition = '', array $parameters = []): int;
}
