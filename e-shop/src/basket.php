<?php

/**
 * Basket page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


require_once __DIR__ . '/includes/configuration.php';
Configuration::setTitleSection('Basket');


// TODO: Recalculate, validate and process basket.
// TODO: Maybe redirect logged out user away if basket is only for logged users.

siteHeader();

?>

<h4 class="d-flex justify-content-between align-items-center mb-3">
	<!-- TODO: Display number of products in Basket from database. -->
	<span class="text-muted">Your Basket</span> <span class="badge badge-secondary badge-pill">3</span>
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
			<!-- TODO: Display products in Basket from database. -->
			<tr>
				<th scope="row">
					<h6 class="my-0"><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></h6>
				</th>

				<td class="text-center">
					<div class="btn-group plus-minus-number">
						<span class="px-2">
							<button type="button" disabled class="btn btn-danger btn-number" data-type="minus" data-field="quantity1">
								<i class="fa fa-minus"></i>
							</button>
						</span>

						<label for="quantity1"></label>
						<input type="number" id="quantity1" name="quantity1" class="form-control input-number" value="1" min="1" max="100" size="5">

						<span class="px-2">
							<button type="button" class="btn btn-success btn-number" data-type="plus" data-field="quantity1">
								<i class="fa fa-plus"></i>
							</button>
						</span>
					</div>
				</td>

				<td class="text-right">
					<span class="text-danger">£&nbsp;139.97</span>
				</td>

				<td class="text-right">
					<button type="submit" name="remove1" class="btn btn-sm">
						<i class="fa fa-times fa-2x text-danger"></i>
					</button>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<h6 class="my-0"><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></h6>
				</th>

				<td class="text-center">
					<div class="btn-group plus-minus-number">
						<span class="px-2">
							<button type="button" disabled class="btn btn-danger btn-number" data-type="minus" data-field="quantity2">
								<i class="fa fa-minus"></i>
							</button>
						</span>

						<label for="quantity2"></label>
						<input type="number" id="quantity2" name="quantity2" class="form-control input-number" value="1" min="1" max="100" size="5">

						<span class="px-2">
							<button type="button" class="btn btn-success btn-number" data-type="plus" data-field="quantity2">
								<i class="fa fa-plus"></i>
							</button>
						</span>
					</div>
				</td>

				<td class="text-right">
					<span class="text-danger">£&nbsp;139.97</span>
				</td>

				<td class="text-right">
					<button type="submit" name="remove1" class="btn btn-sm">
						<i class="fa fa-times fa-2x text-danger"></i>
					</button>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<h6 class="my-0"><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></h6>
				</th>

				<td class="text-center">
					<div class="btn-group plus-minus-number">
						<span class="px-2">
							<button type="button" disabled class="btn btn-danger btn-number" data-type="minus" data-field="quantity3">
								<i class="fa fa-minus"></i>
							</button>
						</span>
						<label for="quantity3"></label>
						<input type="number" id="quantity3" name="quantity3" class="form-control input-number" value="1" min="1" max="100" size="5">
						<span class="px-2">
							<button type="button" class="btn btn-success btn-number" data-type="plus" data-field="quantity3">
								<i class="fa fa-plus"></i>
							</button>
						</span>
					</div>
				</td>

				<td class="text-right">
					<span class="text-danger">£&nbsp;139.97</span>
				</td>

				<td class="text-right">
					<button type="submit" name="remove1" class="btn btn-sm">
						<i class="fa fa-times fa-2x text-danger"></i>
					</button>
				</td>
			</tr>

			<tr>
				<th scope="row" colspan="2">
					<h6 class="my-0"><strong>Total</strong></h6>
				</th>

				<td class="text-right">
					<!-- TODO: Calculate total price. -->
					<span class="text-danger"><strong>£&nbsp;419.91</strong></span>
				</td>

				<td></td>
			</tr>
		</tbody>
	</table>

	<div class="row">
		<div class="col-lg-6"></div>

		<div class="col-lg-3 mb-2">
			<button type="submit" name="recalculate" class="btn btn-lg btn-secondary text-white">
				Recalculate Basket
			</button>
		</div>

		<div class="col-lg-3">
			<button type="submit" name="checkout" class="btn btn-primary btn-lg text-white">
				Continue to Checkout
			</button>
		</div>
	</div>
</form>

<?php siteFooter(); ?>
