<?php

/**
 * Product list (homepage).
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Helpers;


require_once __DIR__ . '/../src/configuration.php';


$productService = Configuration::getProductService();

siteHeader();

?>

<div class="row">
	<?php foreach ($productService->getAllProducts() as $product): ?>
		<div class="col-lg-3">
			<div class="card mb-3 box-shadow product-box">
				<?php

				$productUrl = (new Nette\Http\Url('product.php'))->setQueryParameter('id', $product['id']);
				$buyUrl = (new Nette\Http\Url('buy.php'))->setQuery([
					'productId' => $product['id'],
					'backLink' => 'index.php',
				]);

				?>

				<a href="/<?= $productUrl; ?>">
					<img class="card-img-top" src="<?= $productService->getImageRelativePath($product['image']); ?>" alt="<?= escape($product['name']); ?>">
				</a>

				<div class="card-body">
					<p class="card-text product-name">
						<a href="/<?= $productUrl; ?>"><?= escape(Nette\Utils\Strings::truncate($product['name'], 70)); ?></a>
					</p>
					<div class="d-flex justify-content-between align-items-center">
						<small class="text-danger"><?= Helpers::formatPrice($product['price']); ?></small>
						<div class="btn-group">
							<a href="/<?= $buyUrl; ?>" class="btn btn-sm btn-outline-success">
								<i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php siteFooter();
