<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Service;

use Main\Http;


/**
 * Basket service class.
 *
 * @package Main\Service
 */
class BasketService
{
	/**
	 * @var array list of products in basket
	 *
	 * each element has 'product' key with product from database and 'quantity' key with it's quantity
	 */
	private $basketProducts;

	/**
	 * @var int total products count in basket
	 */
	private $basketProductsCount = 0;

	/**
	 * @var float total price of products in basket
	 */
	private $basketProductsPrice = 0.0;

	/**
	 * @var Http\SessionSection basket session section
	 */
	private $basketSection;

	/**
	 * @var ProductService product service
	 */
	private $productService;


	/**
	 * Creates basket service.
	 *
	 * @param Http\SessionSection $basketSection basket session section
	 */
	public function __construct(Http\SessionSection $basketSection, ProductService $productService)
	{
		$this->basketSection = $basketSection;
		$this->productService = $productService;
	}


	/**
	 * Sets basket expiration.
	 *
	 * @param string|int|\DateTimeInterface|null $time expiration, 0 or null means when a user closes a browser
	 * @return self
	 */
	public function setExpiration($time): self
	{
		$this->basketSection->setExpiration($time);

		return $this;
	}


	/**
	 * Adds product to basket or update quantity of product already present in basket.
	 *
	 * @param int $productId product ID
	 * @param int $quantity quantity of products to add
	 * @param bool $updateQuantity update quantity of product already present in basket
	 * @return self
	 *
	 * @throws \InvalidArgumentException if product with given ID does not exists
	 */
	public function addToBasket(int $productId, int $quantity, bool $updateQuantity = false): self
	{
		if (!$this->productService->getProductById($productId)) {
			throw new \InvalidArgumentException('Product not found.');
		}

		if (
			!$updateQuantity
			&& isset($this->basketSection['products'][$productId]['quantity'])
			&& is_int($this->basketSection['products'][$productId]['quantity'])
		) {
			$this->basketSection['products'][$productId]['quantity'] += $quantity;

		} else {
			$this->basketSection['products'][$productId] = [
				'quantity' => $quantity,
			];
		}

		return $this;
	}


	/**
	 * Removes product from basket.
	 *
	 * @param int $productId ID of product to remove
	 * @return self
	 */
	public function removeFromBasket(int $productId): self
	{
		unset($this->basketSection['products'][$productId]);

		return $this;
	}


	/**
	 * Returns associative array of products and it's quantities in basket and computes total products count.
	 * [
	 *   1 => [ // product ID
	 *          'product' => [...], // product from database
	 *          'quantity' => 1, // product quantity
	 *        ],
	 *   ...
	 * ]
	 *
	 * @return array associative array of products and it's quantities in basket
	 */
	public function getBasketProducts(): array
	{
		if ($this->basketProducts === null) {
			$this->basketProducts = [];

			foreach ($this->basketSection['products'] as $productId => $data) {
				if (
					!isset($data['quantity'])
					|| !is_int($data['quantity'])
					|| !($product = $this->productService->getProductById($productId))
				) {
					$this->removeFromBasket($productId);
					continue;
				}

				$this->basketProducts[$productId] = [
					'product' => $product,
					'quantity' => $data['quantity'],
				];
				$this->basketProductsCount += $data['quantity'];
				$this->basketProductsPrice += (float) $product['price'] * (int) $data['quantity'];
			}
		}

		return $this->basketProducts;
	}


	/**
	 * Returns total number of products in basket.
	 *
	 * @return int total number of products in basket
	 */
	public function getBasketProductsCount(): int
	{
		if ($this->basketProducts === null) {
			$this->getBasketProducts();
		}

		return $this->basketProductsCount;
	}


	/**
	 * Returns total price of products in basket.
	 *
	 * @return float total price of products in basket
	 */
	public function getBasketProductsPrice(): float
	{
		if ($this->basketProducts === null) {
			$this->getBasketProducts();
		}

		return $this->basketProductsPrice;
	}
}
