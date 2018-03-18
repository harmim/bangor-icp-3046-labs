<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Database;


/**
 * Dummy database wrapper.
 *
 * @package Main\Database
 */
class DummyDatabase implements IDatabase
{
	/**
	 * @inheritdoc
	 */
	public function queryOne(string $query, array $parameters = []): array
	{
		return [];
	}


	/**
	 * @inheritdoc
	 */
	public function queryAll(string $query, array $parameters = []): array
	{
		return [];
	}


	/**
	 * @inheritdoc
	 */
	public function insert(string $table, array $data): int
	{
		return 0;
	}


	/**
	 * @inheritdoc
	 */
	public function update(string $table, array $data, string $condition = '', array $parameters = []): int
	{
		return 0;
	}

}
