<?php

/**
 * Basket page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Http;
use Main\Utils;


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Basket');
$basketService = Configuration::getBasketService();
$post = Configuration::getHttpRequest()->getPost();
$messages = Configuration::getMessages();

// remove product from basket
if (!empty($post['remove'])) {
	$basketService->removeFromBasket((int) $post['remove']);
	$messages->addMessage('Product has been removed from basket.');
	Configuration::redirect('basket.php');
}

// update product quantity and redirect to checkout if checkout is submitted
if (!empty($post['recalculate']) || !empty($post['checkout'])) {
	try {
		foreach ($post as $key => $value) {
			if (strncmp($key, 'quantity', 8) === 0) {
				$basketService->addToBasket((int) substr($key, 8), (int) $value, true);
			}
		}

	} catch (InvalidArgumentException $e) {
		$messages->addMessage($e->getMessage(), $messages::TYPE_DANGER);
		Configuration::redirect('basket.php');
	}

	if (!empty($post['checkout'])) {
		Configuration::redirect('checkout.php');
	} else {
		$messages->addMessage('Basket has been recalculated.');
		Configuration::redirect('basket.php');
	}
}

// show message if basket is empty
$basketProductsCount = $basketService->getBasketProductsCount();
if (!$basketProductsCount) {
	$messages->addMessage('Basket is empty.', $messages::TYPE_WARNING);
}

siteHeader();

?>

<?php if ($basketProductsCount): ?>
	<h4 class="d-flex justify-content-between align-items-center mb-3">
		<span class="text-muted">Your Basket</span> <span class="badge badge-secondary badge-pill"><?= $basketProductsCount; ?></span>
	</h4>

	<form method="post" action="basket.php">
		<table class="table mb-3">
			<thead class="thead-light">
				<tr>
					<th scope="col">Product name</th>
					<th scope="col" class="text-center">Quantity</th>
					<th scope="col" class="text-right">Price</th>
					<th scope="col" class="text-right">Remove</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($basketService->getBasketProducts() as $productData): ?>
					<?php

					$product = $productData['product'];
					$quantity = (int) $productData['quantity'];
					$escapedProductId = escape($product['id']);
					$productUrl = (new Http\Url('product.php'))->setQueryParameter('id', $escapedProductId);

					?>

					<tr>
						<th scope="row">
							<a href="/<?= $productUrl; ?>"><?= escape($product['name']); ?></a>
						</th>

						<td class="text-right">
							<div class="btn-group plus-minus-number">
								<span class="px-2 d-none d-lg-block">
									<button type="button" disabled class="btn btn-danger btn-number" data-type="minus" data-field="quantity<?= $escapedProductId; ?>">
										<i class="fa fa-minus"></i>
									</button>
								</span>

								<label for="quantity<?= $escapedProductId; ?>"></label>
								<input type="number" id="quantity<?= $escapedProductId; ?>" name="quantity<?= $escapedProductId; ?>" class="form-control input-number" value="<?= $quantity; ?>" min="1" max="100" size="5">

								<span class="px-2 d-none d-lg-block">
									<button type="button" class="btn btn-success btn-number" data-type="plus" data-field="quantity<?= $escapedProductId; ?>">
										<i class="fa fa-plus"></i>
									</button>
								</span>
							</div>
						</td>

						<td class="text-right">
							<span class="text-danger"><?= Utils::formatPrice((float) $product['price'] * $quantity); ?></span>
						</td>

						<td class="text-right">
							<button type="submit" value="<?= escape($product['id']); ?>" name="remove" class="btn btn-sm">
								<i class="fa fa-times fa-2x text-danger"></i>
							</button>
						</td>
					</tr>
				<?php endforeach; ?>

				<tr>
					<th scope="row" colspan="2">
						<strong>Total</strong>
					</th>

					<td class="text-right">
						<span class="text-danger"><strong><?= Utils::formatPrice($basketService->getBasketProductsPrice()); ?></strong></span>
					</td>

					<td></td>
				</tr>
			</tbody>
		</table>

		<div class="row">
			<div class="col-lg-6"></div>

			<div class="col-lg-3 mb-2">
				<button type="submit" value="recalculate" name="recalculate" class="btn btn-lg btn-secondary text-white">
					Recalculate Basket
				</button>
			</div>

			<div class="col-lg-3">
				<button type="submit" value="checkout" name="checkout" class="btn btn-primary btn-lg text-white">
					Continue to Checkout
				</button>
			</div>
		</div>
	</form>
<?php endif; ?>

<?php siteFooter();
