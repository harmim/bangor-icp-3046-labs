<?php

/**
 * Checkout process page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;


require_once __DIR__ . '/includes/configuration.php';


Configuration::setTitleSection('Checkout process');

// TODO: Redirect user away if his Basket is empty or if he can't checkout for any other reason.
// TODO: Fill, process and validate checkout form.

siteHeader();

?>

<h2 class="mb-3">Checkout</h2>

<div class="row">
	<div class="col-md-4 order-md-2 mb-4">
		<h4 class="d-flex justify-content-between align-items-center mb-3">
			<!-- TODO: Display number of products in Basket from database. -->
			<span class="text-muted">Your Basket</span> <span class="badge badge-secondary badge-pill">3</span>
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
			<!-- TODO: Display products in Basket from database. -->
			<tr>
				<th scope="row"><small><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></small></th>
				<td class="text-right"><small>2</small></td>
				<td class="text-danger text-right"><small>£&nbsp;139.97</small></td>
			</tr>

			<tr>
				<th scope="row"><small><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></th>
				<td class="text-right"><small>3</small></td>
				<td class="text-danger text-right"><small>£&nbsp;419.91</small></td>
			</tr>

			<tr>
				<th scope="row" colspan="2"><small><strong>Total</strong></small></th>
				<td class="text-danger text-right"><small><strong>£&nbsp;699.85</strong></small></td>
			</tr>
		</tbody>
	</table>
	</div>

	<div class="col-md-8 order-md-1">
		<form class="needs-validation" method="post" action="checkout.php">
			<!-- TODO: Fill this form with user data. -->
			<h4 class="mb-3">Billing address</h4>

			<div class="mb-3">
				<label for="email">Email</label>
				<input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
			</div>

			<?php

			$billingAddressForm = new Renderable\AddressForm('billing', [
				'forename' => [
					'value' => 'Dominik',
				],
				'surname' => [
					'value' => 'Harmim'
				],
			]);
			$billingAddressForm->render();

			?>

			<hr class="mb-4">

			<div class="custom-control custom-checkbox mb-3">
				<input type="checkbox" class="custom-control-input" id="shippingAddress" name="shippingAddress" data-toggle="collapse" data-target="#shippingAddressCollapse" aria-expanded="false" aria-controls="shippingAddressCollapse">
				<label class="custom-control-label" for="shippingAddress">Shipping address differs from my billing address</label>
			</div>

			<div class="collapse" id="shippingAddressCollapse">
				<h4 class="mb-3">Shipping address</h4>

				<?php

				$billingAddressForm = new Renderable\AddressForm('shipping', [
					'forename' => [
						'value' => 'Dominik',
					],
					'surname' => [
						'value' => 'Harmim'
					],
				]);
				$billingAddressForm->render();

				?>
			</div>

			<hr class="mb-4">

			<h4 class="mb-3">Shipping</h4>

			<div class="d-block my-3">
				<div class="custom-control custom-radio">
					<input id="homeDelivery" name="shipping" type="radio" class="custom-control-input" checked required>
					<label class="custom-control-label" for="homeDelivery">
						Home Delivery ( <span class="text-danger">Free</span> )
					</label>
				</div>

				<div class="custom-control custom-radio">
					<input id="storeDelivery" name="shipping" type="radio" class="custom-control-input" required>
					<label class="custom-control-label" for="storeDelivery">
						Store Delivery ( <span class="text-danger">£&nbsp;5</span> )
					</label>
				</div>
			</div>

			<hr class="mb-4">

			<h4 class="mb-3">Payment</h4>

			<div class="d-block my-3">
				<div class="custom-control custom-radio">
					<input id="cashPayment" name="payment" type="radio" class="custom-control-input" checked required>
					<label class="custom-control-label" for="cashPayment">
						Cash Payment ( <span class="text-danger">Free</span> )
					</label>
				</div>

				<div class="custom-control custom-radio">
					<input id="cardPayment" name="payment" type="radio" class="custom-control-input" required>
					<label class="custom-control-label" for="cardPayment">
						Pay with Card ( <span class="text-danger">Free</span> )
					</label>
				</div>
			</div>

			<hr class="mb-4">

			<div class="custom-control custom-checkbox mb-3">
				<input type="checkbox" class="custom-control-input" id="termsAgreement" name="termsAgreement" required>
				<label class="custom-control-label" for="termsAgreement">I agree to the terms and conditions</label>
			</div>

			<hr class="mb-4">

			<button class="btn btn-primary btn-lg btn-block" type="submit" value="1" name="submit">Confirm order</button>
		</form>
	</div>
</div>

<?php siteFooter(); ?>
