<?php

/**
 * Buy product script.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


require_once __DIR__ . '/../src/configuration.php';


$messages = Configuration::getMessages();
$query = Configuration::getHttpRequest()->getQuery();

// redirect away if product ID or back link missing
if (empty($query['productId']) || empty($query['backLink'])) {
	$messages->addMessage('Invalid link.', $messages::TYPE_DANGER);
	Configuration::redirect('index.php');
}

// add product to basket
try {
	Configuration::getBasketService()->addToBasket((int) $query['productId'], (int) ($query['quantity'] ?? 1));
	$messages->addMessage('Product has been added to your basket.', $messages::TYPE_SUCCESS);

} catch (InvalidArgumentException $e) {
	$messages->addMessage($e->getMessage(), $messages::TYPE_DANGER);
}

// redirect back
Configuration::redirect($query['backLink']);
