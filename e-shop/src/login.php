<?php

/**
 * Login page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;
use Main\Security;


require_once __DIR__ . '/includes/configuration.php';


Configuration::setTitleSection('Login');
$user = Configuration::getUser();

// redirect user to personal information page if he is already logged in
if ($user->isLoggedIn()) {
	Configuration::redirect('personal_information.php');
}

// process and validate login form
$form = Configuration::getHttpRequest()->getPost();
if (isset($form['submit'])) {
	if (!empty($form['email']) && !empty($form['password'])) {
		try {
			$user->login($form['email'], $form['password']);
			$user->setExpiration('7 days');
			Renderable\Messages::addMessage(
				'You have been successfully logged in.',
				Renderable\Messages::TYPE_SUCCESS
			);

			Configuration::redirect('index.php');

		} catch (Security\AuthenticationException $e) {
			Renderable\Messages::addMessage($e->getMessage(), Renderable\Messages::TYPE_DANGER);
		}
	} else {
		Renderable\Messages::addMessage(
			'Please enter all required fields.',
			Renderable\Messages::TYPE_DANGER
		);
	}
}

siteHeader();

?>

<div class="row">
	<div class="col-md-8">
		<h4 class="mb-3">Login</h4>

		<form class="needs-validation" method="post" action="login.php">
			<div class="form-row">
				<div class="col-md-6 form-group">
					<label for="email">Email</label>
					<input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
				</div>

				<div class="col-md-6 form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
				</div>
			</div>

			<hr class="mb-4">

			<button class="btn btn-primary btn-lg btn-block" type="submit" value="1" name="submit">Login</button>
		</form>
	</div>
</div>

<?php siteFooter(); ?>
