<?php

/**
 * Product list (homepage).
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Service;
use Main\Strings;
use Main\Utils;


require_once __DIR__ . '/includes/configuration.php';


// get all products
/** @var Service\ProductService $productService */
$productService = Configuration::getService(Service\ProductService::class);
$products = $productService->getAllProducts();

siteHeader();

?>

<div class="row">
	<?php foreach ($products as $product): ?>
		<div class="col-lg-3">
			<div class="card mb-3 box-shadow product-box">
				<a href="product.php?id=<?= escape($product['id']) ?>">
					<img class="card-img-top" src="<?= $productService->getImageRelativePath($product['image']) ?>" alt="<?= escape($product['name']); ?>">
				</a>

				<div class="card-body">
					<p class="card-text product-name">
						<a href="product.php?id=<?= escape($product['id']) ?>"><?= escape(Strings::truncate($product['name'], 70)); ?></a>
					</p>
					<div class="d-flex justify-content-between align-items-center">
						<small class="text-danger"><?= Utils::formatPrice($product['price']) ?></small>
						<div class="btn-group">
							<a href="buy.php?id=<?= escape($product['id']) ?>" class="btn btn-sm btn-outline-success">
								<i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php siteFooter(); ?>
