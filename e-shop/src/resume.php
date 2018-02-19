<?php

/**
 * Success page (order resume).
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


require_once __DIR__ . '/includes/configuration.php';
Configuration::setTitleSection('Order resume');


siteHeader();

?>

<div class="card card-body box-shadow mb-3">
	<h2 class="text-center">Thank you for your order.</h2>
	<!-- TODO: Display order number from database. -->
	<h4 class="text-center">Your order number <strong>4118630647</strong> is in processing.</h4>
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
			<!-- TODO: Display products from database. -->
			<tr>
				<th scope="row"><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></th>
				<td class="text-right">2</td>
				<td class="text-danger text-right">£&nbsp;139.97</td>
			</tr>

			<tr>
				<th scope="row"><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></th>
				<td class="text-right">3</td>
				<td class="text-danger text-right">£&nbsp;419.91</td>
			</tr>

			<tr>
				<th scope="row" colspan="2"><strong>Shipping:</strong> Store Delivery</th>
				<td class="text-danger text-right">Free</td>
			</tr>

			<tr>
				<th scope="row" colspan="2"><strong>Payment:</strong> Pay with Card</th>
				<td class="text-danger text-right">Free</td>
			</tr>

			<!-- TODO: Calculate total price. -->
			<tr>
				<th scope="row" colspan="2"><strong>Total</strong></th>
				<td class="text-danger text-right"><strong>£&nbsp;699.85</strong></td>
			</tr>
		</tbody>
	</table>
</div>

<?php siteFooter(); ?>
