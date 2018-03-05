<?php

/**
 * User orders history.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


require_once __DIR__ . '/includes/configuration.php';


Configuration::setTitleSection('Orders');
$user = Configuration::getUser();

// redirect user to login page if he is logged out
if (!$user->isLoggedIn()) {
	Configuration::redirect('login.php');
}

siteHeader();

?>

<h4 class="mb-3">Your orders history</h4>

<!-- TODO: Display user orders from database. -->

<div class="card card-body box-shadow mb-3">
	<h5>
		<span class="text-muted">
			Date: 24. 11. 2017
			❘ Order number: <strong>4118630647</strong>
			❘ Status: <span class="text-warning">processing</span>
			❘ Price: <span class="text-danger">£&nbsp;699.85</span>
		</span>
	</h5>

	<table class="table">
		<thead>
			<tr>
				<th scope="col">Product name</th>
				<th scope="col" class="text-right">Quantity</th>
				<th scope="col" class="text-right">Price</th>
			</tr>
		</thead>

		<tbody>
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
		</tbody>
	</table>
</div>

<div class="card card-body box-shadow mb-3">
	<h5>
		<span class="text-muted">
			Date: 12. 12. 2017
			❘ Order number: <strong>5118630647</strong>
			❘ Status: <span class="text-success">complete</span>
			❘ Price: <span class="text-danger">£&nbsp;419.91</span>
		</span>
	</h5>

	<table class="table">
		<thead>
			<tr>
				<th scope="col">Product name</th>
				<th scope="col" class="text-right">Quantity</th>
				<th scope="col" class="text-right">Price</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<th scope="row"><a href="product.php">Samsung 850 EVO 500GB 2.5inch SSD</a></th>
				<td class="text-right">3</td>
				<td class="text-danger text-right">£&nbsp;139.97</td>
			</tr>
		</tbody>
	</table>
</div>

<?php siteFooter(); ?>
