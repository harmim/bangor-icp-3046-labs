<?php

/**
 * Success page (order resume).
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Http;
use Main\Utils;


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Order resume');
$orderService = Configuration::getOrderService();
$user = Configuration::getUser();

// redirect user away if his order session doesn't exists
if (!$orderService->getOrderId()) {
	Configuration::redirect('index.php');
}

// redirect user to login page if he is not logged in
if (!$user->isLoggedIn()) {
	Configuration::getMessages()->addMessage('You have to be logged in if you want display your order resume.');
	Configuration::getHttpResponse()->setCookie('loginBackLink', 'resume.php', '10 minutes');
	Configuration::redirect('login.php');
}

// get order
$order = $orderService->getUsersOrders($user->getIdentity(), $orderService->getOrderId());

siteHeader();

?>

<div class="card card-body box-shadow mb-3">
	<h2 class="text-center">Thank you for your order.</h2>
	<h4 class="text-center">Your order number <strong><?= $order['id']; ?></strong> is in processing.</h4>
	<p class="text-center">
		We will inform you by email about the exact date and time of shipping.
	</p>

	<table class="table mt-3">
		<thead>
			<tr>
				<th scope="col">Product name</th>
				<th scope="col" class="text-right">Quantity</th>
				<th scope="col" class="text-right">Price</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($order['items'] as $item): ?>
				<?php
				switch ($item['type']):
					case 'product':
				?>
						<tr>
							<th scope="row"><a href="/<?= (new Http\Url('product.php'))->setQueryParameter('id', $item['product']); ?>"><?= $item['name']; ?></a></th>
							<td class="text-right"><?= $item['quantity']; ?></td>
							<td class="text-danger text-right"><?= Utils::formatPrice((float) $item['price'] * (int) $item['quantity']); ?></td>
						</tr>
						<?php break; ?>

					<?php case 'shipping': ?>
						<tr>
							<th scope="row" colspan="2"><strong>Shipping:</strong> <?= $item['name']; ?></th>
							<td class="text-danger text-right"><?= Utils::formatPrice($item['price']); ?></td>
						</tr>
						<?php break; ?>

					<?php case 'payment': ?>
						<tr>
							<th scope="row" colspan="2"><strong>Payment:</strong> <?= $item['name']; ?></th>
							<td class="text-danger text-right"><?= Utils::formatPrice($item['price']); ?></td>
						</tr>
						<?php break; ?>
				<?php endswitch; ?>
			<?php endforeach; ?>

			<tr>
				<th scope="row" colspan="2"><strong>Total</strong></th>
				<td class="text-danger text-right"><strong><?= Utils::formatPrice($order['price']); ?></strong></td>
			</tr>
		</tbody>
	</table>
</div>

<?php siteFooter();
