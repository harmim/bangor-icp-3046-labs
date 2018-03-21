<?php

/**
 * Checkout process page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Http;
use Main\Renderable;
use Main\Security;
use Main\Utils;


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Checkout process');
$basketService = Configuration::getBasketService();
$user = Configuration::getUser();
$messages = Configuration::getMessages();

// redirect user to login page if he is not logged in
if (!$user->isLoggedIn()) {
	$messages->addMessage('You have to be logged in to checkout your order.');
	Configuration::getHttpResponse()->setCookie('loginBackLink', 'checkout.php', '10 minutes');
	Configuration::redirect('login.php');
}

// redirect user away if his Basket is empty
$basketProductsCount = $basketService->getBasketProductsCount();
if (!$basketProductsCount) {
	Configuration::redirect('basket.php');
}

// get shipping and payment methods
$orderService = Configuration::getOrderService();
$shippingMethods = $orderService->getAllShippingMethods();
$paymentMethods = $orderService->getAllPaymentMethods();

// process and validate checkout form
$identity = $user->getIdentity();
$post = Configuration::getHttpRequest()->getPost();
if (isset($post['submit'])) {
	if (
		!empty($post['email'])
		&& !empty($post['billingForename'])
		&& !empty($post['billingSurname'])
		&& !empty($post['billingAddress'])
		&& !empty($post['billingCity'])
		&& !empty($post['billingZip'])
		&& (
			empty($post['shippingAddressEnabled'])
			|| (
				!empty('shippingAddressEnabled')
				&& !empty($post['shippingForename'])
				&& !empty($post['shippingSurname'])
				&& !empty($post['shippingAddress'])
				&& !empty($post['shippingCity'])
				&& !empty($post['shippingZip'])
			)
		)
		&& !empty($post['shipping'])
		&& !empty($post['payment'])
		&& !empty($post['termsAgreement'])
	) {
		try {
			$userService = Configuration::getUserService();
			$userService->updateUser($identity->getId(), [
				'forename' => $post['billingForename'],
				'surname' => $post['billingSurname'],
			]);

			// update identity
			$updatedUser = $userService->getUserById($identity->getId());
			$identity = new Security\Identity($updatedUser['id'], $updatedUser);
			$user->setIdentity($identity);

			$orderService->processOrder($post, $identity);

			Configuration::redirect('resume.php');

		} catch (UnexpectedValueException $e) {
			$messages->addMessage($e->getMessage(), $messages::TYPE_DANGER);
		}

	} else {
		$messages->addMessage('Please enter all required fields.', $messages::TYPE_DANGER);
	}
}

siteHeader();

?>

<h2 class="mb-3">Checkout</h2>

<div class="row">
	<div class="col-md-4 order-md-2 mb-4">
		<h4 class="d-flex justify-content-between align-items-center mb-3">
			<span class="text-muted">Your Basket</span> <span class="badge badge-secondary badge-pill"><?= $basketProductsCount; ?></span>
		</h4>

		<table class="table">
		<thead>
			<tr>
				<th scope="col">Product name</th>
				<th scope="col" class="text-right">Quantity</th>
				<th scope="col" class="text-right">Price</th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($basketService->getBasketProducts() as $productData): ?>
				<?php

				$product = $productData['product'];
				$quantity = (int) $productData['quantity'];
				$productUrl = (new Http\Url('product.php'))->setQueryParameter('id', escape($product['id']));

				?>

				<tr>
					<th scope="row"><small><a href="/<?= $productUrl; ?>"><?= escape($product['name']); ?></a></small></th>
					<td class="text-right"><small><?= $quantity; ?></small></td>
					<td class="text-danger text-right"><small><?= Utils::formatPrice((float) $product['price'] * $quantity); ?></small></td>
				</tr>
			<?php endforeach; ?>

			<tr>
				<th scope="row" colspan="2"><small><strong>Total</strong></small></th>
				<td class="text-danger text-right"><small><strong><?= Utils::formatPrice($basketService->getBasketProductsPrice()); ?></strong></small></td>
			</tr>
		</tbody>
	</table>
	</div>

	<div class="col-md-8 order-md-1">
		<form class="needs-validation" method="post" action="checkout.php">
			<?php

			$userData = $identity->getData();

			?>
			<h4 class="mb-3">Billing address</h4>
			<div class="mb-3">
				<label for="email">Email</label>
				<input type="email" class="form-control" id="email" name="email" value="<?= $userData['email']; ?>" placeholder="Enter email" required readonly>
			</div>
			<?php

			$billingAddressForm = new Renderable\AddressForm('billing', [
				'forename' => [
					'value' => $userData['forename'],
				],
				'surname' => [
					'value' => $userData['surname'],
				],
			]);
			$billingAddressForm->render();

			?>

			<hr class="mb-4">
			<div class="custom-control custom-checkbox mb-3">
				<input type="checkbox" class="custom-control-input" id="shippingAddressEnabled" name="shippingAddressEnabled" value="shippingAddressEnabled" data-toggle="collapse" data-target="#shippingAddressCollapse" aria-expanded="false" aria-controls="shippingAddressCollapse">
				<label class="custom-control-label" for="shippingAddressEnabled">Shipping address differs from my billing address</label>
			</div>

			<div class="collapse" id="shippingAddressCollapse">
				<h4 class="mb-3">Shipping address</h4>
				<?php

				$billingAddressForm = new Renderable\AddressForm('shipping');
				$billingAddressForm->render();

				?>
			</div>

			<?php if (count($shippingMethods)): ?>
				<hr class="mb-4">
				<h4 class="mb-3">Shipping</h4>

				<div class="d-block my-3">
					<?php foreach ($shippingMethods as $key => $shippingMethod): ?>
						<?php

						$escapedShippingId = escape($shippingMethod['id']);

						?>

						<div class="custom-control custom-radio">
							<input id="shipping<?= $escapedShippingId; ?>" name="shipping" value="<?= $escapedShippingId; ?>" type="radio" class="custom-control-input" <?php if ($key === 0) echo 'checked'; ?> required>
							<label class="custom-control-label" for="shipping<?= $escapedShippingId; ?>">
								<?= $shippingMethod['name']; ?> ( <span class="text-danger"><?= Utils::formatPrice($shippingMethod['price']); ?></span> )
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if (count($paymentMethods)): ?>
				<hr class="mb-4">
				<h4 class="mb-3">Payment</h4>

				<div class="d-block my-3">
					<?php foreach ($paymentMethods as $key => $paymentMethod): ?>
						<?php

						$escapedPaymentId = escape($paymentMethod['id']);

						?>

						<div class="custom-control custom-radio">
							<input id="payment<?= $escapedPaymentId; ?>" name="payment" value="<?= $escapedPaymentId; ?>" type="radio" class="custom-control-input" <?php if ($key === 0) echo 'checked'; ?> required>
							<label class="custom-control-label" for="payment<?= $escapedPaymentId; ?>">
								<?= $paymentMethod['name']; ?> ( <span class="text-danger"><?= Utils::formatPrice($paymentMethod['price']); ?></span> )
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<hr class="mb-4">
			<div class="custom-control custom-checkbox mb-3">
				<input type="checkbox" class="custom-control-input" id="termsAgreement" name="termsAgreement" value="true" required>
				<label class="custom-control-label" for="termsAgreement">I agree to the terms and conditions</label>
			</div>

			<hr class="mb-4">
			<button class="btn btn-primary btn-lg btn-block" type="submit" value="submit" name="submit">Confirm order</button>
		</form>
	</div>
</div>

<?php siteFooter();
