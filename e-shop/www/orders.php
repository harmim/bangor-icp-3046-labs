<?php

/**
 * User orders history.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Helpers;


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Orders');
$user = Configuration::getUser();
$messages = Configuration::getMessages();

// redirect user to login page if he is logged out
if (!$user->isLoggedIn()) {
	$messages->addMessage('You have to be logged in if you want display your orders list.');
	Configuration::getHttpResponse()->setCookie('loginBackLink', 'orders.php', '10 minutes');
	Configuration::redirect('login.php');
}

// get user's orders
$orders = Configuration::getOrderService()->getUsersOrders($user->getIdentity());

// show message if user has no orders
if (!count($orders)) {
	$messages->addMessage('You have no orders so far.', $messages::TYPE_WARNING);
}

siteHeader();

?>

<h4 class="mb-3">Your orders history</h4>

<?php foreach ($orders as $order): ?>
	<div class="card card-body box-shadow mb-3">
		<h5>
			<span class="text-muted">
				Date: <?= escape(Nette\Utils\DateTime::from($order['created'])->format('j. n. Y')); ?>
				| Order number: <strong><?= escape($order['id']); ?></strong>
				| Status: <span class="text-warning"><?= escape($order['status']); ?></span>
				| Paid: <strong><?= (bool) $order['is_paid'] ? 'Yes' : 'No'; ?></strong>
			</span>
		</h5>

		<table class="table">
			<thead>
				<tr>
					<th scope="col">Product name</th>
					<th scope="col" class="text-center">Quantity</th>
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
								<th scope="row"><a href="/<?= (new Nette\Http\Url('product.php'))->setQueryParameter('id', escape($item['product'])); ?>"><?= escape($item['name']); ?></a></th>
								<td class="text-center"><?= escape($item['quantity']); ?></td>
								<td class="text-danger text-right"><?= Helpers::formatPrice((float) $item['price'] * (int) $item['quantity']); ?></td>
							</tr>
							<?php break; ?>

						<?php case 'shipping': ?>
							<tr>
								<th scope="row" colspan="2"><strong>Shipping:</strong> <?= escape($item['name']); ?></th>
								<td class="text-danger text-right"><?= Helpers::formatPrice($item['price']); ?></td>
							</tr>
							<?php break; ?>

						<?php case 'payment': ?>
							<tr>
								<th scope="row" colspan="2"><strong>Payment:</strong> <?= escape($item['name']); ?></th>
								<td class="text-danger text-right"><?= Helpers::formatPrice($item['price']); ?></td>
							</tr>
							<?php break; ?>
					<?php endswitch; ?>
				<?php endforeach; ?>

				<tr>
					<th scope="row" colspan="2"><strong>Total</strong></th>
					<td class="text-danger text-right"><strong><?= Helpers::formatPrice($order['price']); ?></strong></td>
				</tr>
			</tbody>
		</table>

		<div class="row">
			<div class="col-md-6">
				<h5>Billing address</h5>

				<ul class="list-group list-group-flush">
					<li class="list-group-item">Email: <?= escape($order['email']); ?></li>
					<li class="list-group-item">Forename: <?= escape($order['forename']); ?></li>
					<li class="list-group-item">Surname: <?= escape($order['surname']); ?></li>
					<li class="list-group-item">Address: <?= escape($order['address']); ?></li>
					<li class="list-group-item">City: <?= escape($order['city']); ?></li>
					<li class="list-group-item">Zip: <?= escape($order['zip']); ?></li>
				</ul>
			</div>

			<?php if (!empty($order['shipping_forename'])): ?>
				<div class="col-md-6">
					<h5>Shipping address</h5>

					<ul class="list-group list-group-flush">
						<li class="list-group-item">Forename: <?= escape($order['shipping_forename']); ?></li>
						<li class="list-group-item">Surname: <?= escape($order['shipping_surname']); ?></li>
						<li class="list-group-item">Address: <?= escape($order['shipping_address']); ?></li>
						<li class="list-group-item">City: <?= escape($order['shipping_city']); ?></li>
						<li class="list-group-item">Zip: <?= escape($order['shipping_zip']); ?></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>

<?php siteFooter();
