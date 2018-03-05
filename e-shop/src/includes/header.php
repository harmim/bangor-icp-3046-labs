<?php

/**
 * Page header.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;


$scriptName = Configuration::getHttpRequest()->getScriptName();
$isLoggedIn = Configuration::getUser()->isLoggedIn();

?>

<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
	<a class="navbar-brand" href="index.php"><img src="images/logo.png" alt="Inside" width="70"></a>

	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarCollapse">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item <?php if ($scriptName === 'index') echo 'active'; ?>">
				<a class="nav-link" href="index.php">Home</a>
			</li>

			<?php if (!$isLoggedIn): ?>
				<li class="nav-item <?php if ($scriptName === 'registration') echo 'active'; ?>">
					<a class="nav-link" href="registration.php">Registration</a>
				</li>
			<?php endif; ?>

			<?php if ($isLoggedIn): ?>
				<li class="nav-item <?php if ($scriptName === 'personal_information') echo 'active'; ?>">
					<a class="nav-link" href="personal_information.php">Personal information</a>
				</li>
			<?php endif; ?>

			<?php if ($isLoggedIn): ?>
				<li class="nav-item <?php if ($scriptName === 'orders') echo 'active'; ?>">
					<a class="nav-link" href="orders.php">Orders</a>
				</li>
			<?php endif; ?>
		</ul>

		<ul class="navbar-nav float-lg-right">
			<?php if (!$isLoggedIn): ?>
				<li class="nav-item <?php if ($scriptName === 'login') echo 'active'; ?>">
					<a class="nav-link" href="login.php">Login <i class="fa fa-sign-in" aria-hidden="true"></i></a>
				</li>
			<?php endif; ?>

			<?php if ($isLoggedIn): ?>
				<li class="nav-item">
					<a class="nav-link" href="logout.php">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a>
				</li>
			<?php endif; ?>

			<li class="nav-item <?php if ($scriptName === 'basket') echo 'active'; ?>">
				<a class="nav-link" href="basket.php">Basket <i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
			</li>
		</ul>
	</div>
</nav>
