<?php

/**
 * Product detail page/
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


require_once __DIR__ . '/includes/configuration.php';
Configuration::setTitleSection('Product name');


// TODO: Process buying of product.

siteHeader();

?>

<!-- TODO: Display product information from database. -->
<div class="card card-body box-shadow">
	<div class="row">
		<div class="col-md-4">
			<img class="card-img-top" src="images/products/illust/product.jpg" alt="Product">
		</div>

		<div class="col-md-8 top-margin-md">
			<h2>Samsung 850 EVO 500GB 2.5inch SSD</h2>

			<form method="post" action="product.php">
				<div class="d-flex justify-content-between align-items-center">
					<strong class="text-danger">£&nbsp;139.97</strong>

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

						<button type="submit" name="buy" class="btn btn-outline-success">
							<i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<hr>

	<h4>Product description</h4>
	<p class="card-text">
		The Samsung SSD 850 EVO elevates the everyday computing experience to a higher level of performance and
		endurance than was ever imagined. Powered by Samsung's unmatched V-NAND technology, no wonder the 850 EVO
		is the best-selling SSD for everyday computing. Designed for mainstream desktop PCs and laptops, the 850 EVO
		comes in a wide range of capacities and form factors.
	</p>
</div>

<?php siteFooter(); ?>
