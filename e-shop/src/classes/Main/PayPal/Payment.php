<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\PayPal;

use Main\Configuration;
use Nette;
use PayPal;


/**
 * PayPal payment.
 *
 * @package Main\PayPal
 */
class Payment
{
	use Nette\StaticClass;

	/**
	 * Creates payment and returns payment object with approval link for redirect.
	 *
	 * @param array $products basket products
	 * @param float $productsPrice basket products price
	 * @param float $shippingPrice shipping price
	 * @return PayPal\Api\Payment  payment object with approval link for redirect
	 *
	 * @throws PayPalException in case of payment execution error, with user message
	 */
	public static function createPayment(
		array $products,
		float $productsPrice,
		float $shippingPrice
	): PayPal\Api\Payment {
		try {
			$currency = Configuration::getConfig('common', 'currency');
			$domain = Configuration::getConfig('common', 'domain');

			$itemList = new PayPal\Api\ItemList();
			foreach ($products as $productData) {
				$product = $productData['product'];
				$item = (new PayPal\Api\Item())
					->setSku($product['id'])
					->setName(escape($product['name']))
					->setDescription(escape($product['description']))
					->setQuantity($productData['quantity'])
					->setPrice($product['price'])
					->setCurrency($currency)
					->setUrl($domain . '/' . (new Nette\Http\Url('product.php'))->setQueryParameter('id', $product['id']));
				$itemList->addItem($item);
			}

			$details = (new PayPal\Api\Details())
				->setShipping($shippingPrice)
				->setSubtotal($productsPrice);

			$amount = (new PayPal\Api\Amount())
				->setCurrency($currency)
				->setDetails($details)
				->setTotal($productsPrice + $shippingPrice);

			$transaction = (new PayPal\Api\Transaction())
				->setAmount($amount)
				->setDescription(Configuration::getConfig('common', 'title') . ' - order payment.')
				->setItemList($itemList)
				->setInvoiceNumber(uniqid());

			$redirectUrls = (new PayPal\Api\RedirectUrls())
				->setReturnUrl($domain . '/' . (new Nette\Http\Url('checkout.php'))->setQueryParameter('paypalPayment', 'true'))
				->setCancelUrl($domain . '/' . (new Nette\Http\Url('checkout.php'))->setQueryParameter('paypalPayment', 'false'));

			$payment = (new PayPal\Api\Payment())
				->setIntent('sale')
				->setPayer((new PayPal\Api\Payer())->setPaymentMethod('paypal'))
				->setTransactions([$transaction])
				->setRedirectUrls($redirectUrls);

			$payment->create(self::createApiContext());

			return $payment;

		} catch (\Exception $e) {
			throw new PayPalException('PayPal create payment error.', $e->getCode(), $e);
		}
	}


	/**
	 * Executes payment.
	 *
	 * @param string $paymentId Payment ID
	 * @param string $payerId Payer ID
	 * @return PayPal\Api\Payment executed Payment object
	 *
	 * @throws PayPalException in case of payment execution error, with user message
	 */
	public static function executePayment(string $paymentId, string $payerId): PayPal\Api\Payment
	{
		try {
			$apiContext = self::createApiContext();

			$execution = (new PayPal\Api\PaymentExecution())->setPayerId($payerId);
			PayPal\Api\Payment::get($paymentId, $apiContext)->execute($execution, $apiContext);

			return PayPal\Api\Payment::get($paymentId, $apiContext);

		} catch (\Exception $e) {
			throw new PayPalException('PayPal execute payment error.', $e->getCode(), $e);
		}
	}


	/**
	 * Returns API context with appropriate credentials.
	 *
	 * @return PayPal\Rest\ApiContext API context with appropriate credentials
	 */
	private static function createApiContext(): PayPal\Rest\ApiContext
	{
		$context = new PayPal\Rest\ApiContext(new PayPal\Auth\OAuthTokenCredential(
			Configuration::getConfig('paypal', 'client_id'),
			Configuration::getConfig('paypal', 'secret')
		));

		// SSL peer handshake failed error, see https://github.com/paypal/PayPal-PHP-SDK/issues/897
		$context->setConfig([
			'http.CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1,
		]);

		return $context;
	}
}
