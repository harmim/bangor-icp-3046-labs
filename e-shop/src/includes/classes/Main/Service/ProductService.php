<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Service;

use Main\Database;


/**
 * Product service class.
 *
 * @package Main\Service
 */
class ProductService
{
	/**
	 * Finds product by id.
	 *
	 * @param int $id product id
	 * @return array associative array with product
	 */
	public function getProductById(int $id): array
	{
		$query = '
			SELECT *
			FROM `product`
			WHERE `id` = :id
		';

		return Database::queryOne($query, [
			':id' => $id,
		]);
	}


	/**
	 * Returns all products.
	 *
	 * @return array array with rows of all products
	 */
	public function getAllProducts(): array
	{
		$query ='
			SELECT *
			FROM `product`
		';

		return Database::queryAll($query);
	}


	/**
	 * Returns image relative path based on it's name.
	 * If image does not exists, it returns path to noimg image.
	 *
	 * @param string $imageName image name
	 * @return string image path
	 */
	public function getImageRelativePath(string $imageName): string
	{
		$path = "images/products/$imageName";
		if (!is_readable($path)) {
			$path = "images/noimg.png";
		}

		return $path;
	}
}
