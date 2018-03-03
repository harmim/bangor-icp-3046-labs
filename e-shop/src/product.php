<?php

/**
 * Product detail page/
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;
use Main\Service;
use Main\Utils;


require_once __DIR__ . '/includes/configuration.php';


/** @var Service\ProductService $productService */
$productService = Configuration::getService(Service\ProductService::class);

// fetch current product
$id = Configuration::getHttpRequest()->getQuery('id');
if ($id && ($product = $productService->getProductById((int) $id))) {
	Configuration::setTitleSection($product['name']);

} else {
	Renderable\Messages::addMessage('Product not found.', Renderable\Messages::TYPE_DANGER);
}

// TODO: Process buying of product.

siteHeader();

?>

<?php if (!empty($product)): ?>
	<div class="card card-body box-shadow">
		<div class="row">
			<div class="col-md-4">
				<img class="card-img-top" src="<?= $productService->getImageRelativePath($product['image']); ?>" alt="<?= escape($product['name']); ?>">
			</div>

			<div class="col-md-8 top-margin-md">
				<h2><?= escape($product['name']); ?></h2>

				<form method="post" action="product.php">
					<div class="d-flex justify-content-between align-items-center">
						<strong class="text-danger"><?= Utils::formatPrice($product['price']); ?></strong>

						<div class="btn-group">
							<div class="btn-group plus-minus-number">
								<span class="px-2">
									<button type="button" disabled class="btn btn-danger btn-number" data-type="minus" data-field="quantity">
										<i class="fa fa-minus"></i>
									</button>
								</span>

								<label for="quantity"></label>
								<input type="number" id="quantity" name="quantity" class="form-control input-number" value="1" min="1" max="100" size="5">

								<span class="px-2">
									<button type="button" class="btn btn-success btn-number" data-type="plus" data-field="quantity">
										<i class="fa fa-plus"></i>
									</button>
								</span>
							</div>

							<button type="submit" value="1" name="buy" class="btn btn-outline-success">
								<i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy
							</button>

							<input type="hidden" name="id" value="<?= escape($product['id']); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>

		<hr>

		<h4>Product description</h4>
		<p class="card-text">
			<?= nl2br(escape($product['description'])); ?>
		</p>
	</div>
<?php endif; ?>

<?php siteFooter(); ?>
