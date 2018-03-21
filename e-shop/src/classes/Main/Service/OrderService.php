<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Service;

use Main\Database;
use Main\Http;
use Main\Security;


/**
 * Order service class.
 *
 * @package Main\Service
 */
class OrderService
{
	/**
	 * @var Database\IDatabase database wrapper
	 */
	private $database;

	/**
	 * @var Http\SessionSection order session section
	 */
	private $orderSection;

	/**
	 * @var BasketService basketService
	 */
	private $basketService;

	/**
	 * @var Http\IRequest HTTP request
	 */
	private $httpRequest;


	/**
	 * Creates order service.
	 *
	 * @param Database\IDatabase $database database wrapper
	 * @param Http\SessionSection $orderSection order session section
	 * @param BasketService $basketService basket service
	 * @param Http\IRequest $httpRequest HTTP request
	 */
	public function __construct(
		Database\IDatabase $database,
		Http\SessionSection $orderSection,
		BasketService $basketService,
		Http\IRequest $httpRequest
	) {
		$this->database = $database;
		$this->orderSection = $orderSection;
		$this->basketService = $basketService;
		$this->httpRequest = $httpRequest;
	}


	/**
	 * Returns all shipping methods.
	 *
	 * @return array array with rows of all shipping methods
	 */
	public function getAllShippingMethods(): array
	{
		$query = '
			SELECT *
			FROM `shipping_method`
		';

		return $this->database->queryAll($query);
	}


	/**
	 * Finds shipping method by ID.
	 *
	 * @param int $id shipping method ID
	 * @return array associative array with shipping method
	 */
	public function getShippingMethodById(int $id): array
	{
		$query = '
			SELECT *
			FROM `shipping_method`
			WHERE `id` = :id
		';

		return $this->database->queryOne($query, [
			':id' => $id,
		]);
	}


	/**
	 * Returns all payment methods.
	 *
	 * @return array array with rows of all payment methods
	 */
	public function getAllPaymentMethods(): array
	{
		$query = '
			SELECT *
			FROM `payment_method`
		';

		return $this->database->queryAll($query);
	}


	/**
	 * Finds payment method by ID.
	 *
	 * @param int $id payment method ID
	 * @return array associative array with payment method
	 */
	public function getPaymentMethodById(int $id): array
	{
		$query = '
			SELECT *
			FROM `payment_method`
			WHERE `id` = :id
		';

		return $this->database->queryOne($query, [
			':id' => $id,
		]);
	}


	/**
	 * Processes order.
	 *
	 * @param array $data order form data
	 * @param Security\IIdentity $user user who created order
	 * @return self
	 *
	 * @throws \UnexpectedValueException in case of supplied invalid data
	 */
	public function processOrder(array $data, Security\IIdentity $user): self
	{
		// fetch shipping and payment
		if (!($shipping = $this->getShippingMethodById((int) $data['shipping']))) {
			throw new \UnexpectedValueException('Invalid shipping method entered.');
		}
		if (!($payment = $this->getPaymentMethodById((int) $data['payment']))) {
			throw new \UnexpectedValueException('Invalid payment method entered.');
		}

		// save order
		$orderData = [
			'user' => $user->getId(),
			'ip' => $this->httpRequest->getRemoteAddress(),
			'email' => $data['email'],
			'forename' => $data['billingForename'],
			'surname' => $data['billingSurname'],
			'address' => $data['billingAddress'],
			'city' => $data['billingCity'],
			'zip' => $data['billingZip'],
		];
		if (!empty($data['shippingAddressEnabled'])) {
			$orderData += [
				'shipping_forename' => $data['shippingForename'],
				'shipping_surname' => $data['shippingSurname'],
				'shipping_address' => $data['shippingAddress'],
				'shipping_city' => $data['shippingCity'],
				'shipping_zip' => $data['shippingZip'],
			];
		}
		$this->database->insert('order', $orderData);
		$orderId = $this->database->getLastInsertedId();

		// save shipping
		$this->database->insert('order_item', [
			'order' => $orderId,
			'type' => 'shipping',
			'shipping' => (int) $shipping['id'],
			'price' => (float) $shipping['price'],
			'name' => $shipping['name'],
		]);

		// save payment
		$this->database->insert('order_item', [
			'order' => $orderId,
			'type' => 'payment',
			'payment' => (int) $payment['id'],
			'price' => (float) $payment['price'],
			'name' => $payment['name'],
		]);

		// save basket items
		foreach ($this->basketService->getBasketProducts() as $productData) {
			$this->database->insert('order_item', [
				'order' => $orderId,
				'product' => (int) $productData['product']['id'],
				'quantity' => (int) $productData['quantity'],
				'price' => (float) $productData['product']['price'],
				'name' => $productData['product']['name'],
			]);
			$this->basketService->removeFromBasket((int) $productData['product']['id']);
		}

		// store order ID to session
		$this->orderSection->id = $orderId;
		$this->orderSection->setExpiration('10 minutes');

		return $this;
	}


	/**
	 * Returns associative array with user orders, it's items and total price.
	 * [
	 *   [
	 *     'items' => [...], // order products, shipping method and payment method
	 *     'price' => [...], // total order price
	 *     ... // other order details
	 *   ],
	 *   ..
	 * ]
	 *
	 * @param Security\IIdentity $user logged in user
	 * @param int|null $id if specified, return particular user's order
	 * @return array associative array with user orders, it's items and total price
	 */
	public function getUsersOrders(Security\IIdentity $user, int $id = null): array
	{
		$sql = '
			SELECT *
			FROM `order`
			WHERE `user` = :user
		';
		$parameters = [
			':user' => $user->getId(),
		];
		if ($id !== null) {
			$sql .= "AND `id` = :id\n";
			$parameters['id'] = $id;
		}
		$sql .= "ORDER BY `id` DESC\n";
		$orders = $this->database->queryAll($sql, $parameters);
		if (!$orders) {
			return [];
		}

		$result = [];
		foreach ($orders as $order) {
			$result[$order['id']] = $order + [
				'items' => [],
				'price' => 0.0,
			];
		}

		$sql = '
			SELECT *
			FROM `order_item`
			WHERE `order` IN (' . implode(',', array_keys($result)) . ')
			ORDER BY `type`
		';
		foreach ($this->database->queryAll($sql) as $item) {
			$result[$item['order']]['items'][] = $item;
			$result[$item['order']]['price'] += (float) $item['price'] * (int) $item['quantity'];
		}

		return $id === null ? $result : reset($result);
	}


	/**
	 * Returns order ID stored in session.
	 *
	 * @return int|null ID of order stored in session or null if doesn't exists
	 */
	public function getOrderId(): ?int
	{
		return $this->orderSection->id ? (int) $this->orderSection->id : null;
	}
}
