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

// TODO: Redirect user away if he is already login.

// Process and validate login form
$showLoginForm = true;
if (isset($_POST['submit'])) {
	if (isset($_POST['email'], $_POST['password'])) {
		try {
			Configuration::setUser((new Security\Authenticator())->authenticate($_POST['email'], $_POST['password']));
			Renderable\Messages::addMessage(
				'You have been successfully logged in.',
				Renderable\Messages::TYPE_SUCCESS
			);
			$showLoginForm = false;

		} catch (Security\AuthenticationException $e) {
			Renderable\Messages::addMessage($e->getMessage(), Renderable\Messages::TYPE_DANGER);
		}
	} else {
		Renderable\Messages::addMessage('Authentication error.', Renderable\Messages::TYPE_DANGER);
	}
}

siteHeader();

?>

<?php if ($showLoginForm): ?>
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

				<button class="btn btn-primary btn-lg btn-block" type="submit" name="submit">Login</button>
			</form>
		</div>
	</div>
<?php endif; ?>

<?php siteFooter(); ?>
