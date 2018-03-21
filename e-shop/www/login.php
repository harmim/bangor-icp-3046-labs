<?php

/**
 * Login page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Security;


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Login');
$user = Configuration::getUser();
$messages = Configuration::getMessages();

// redirect user to personal information page if he is already logged in
if ($user->isLoggedIn()) {
	$messages->addMessage('You are already logged in.');
	Configuration::redirect('personal_information.php');
}

// process and validate login form
$post = Configuration::getHttpRequest()->getPost();
if (isset($post['submit'])) {
	if (!empty($post['email']) && !empty($post['password'])) {
		try {
			$user->login($post['email'], $post['password']);
			$user->setExpiration('7 days');
			$messages->addMessage('You have been successfully logged in.', $messages::TYPE_SUCCESS);

			if ($backLink = Configuration::getHttpRequest()->getCookie('loginBackLink')) {
				Configuration::getHttpResponse()->deleteCookie('loginBackLink');
				Configuration::redirect($backLink);

			} else {
				Configuration::redirect('index.php');
			}

		} catch (Security\AuthenticationException $e) {
			$messages->addMessage($e->getMessage(), $messages::TYPE_DANGER);
		}
	} else {
		$messages->addMessage('Please enter all required fields.', $messages::TYPE_DANGER);
	}
}

siteHeader();

?>

<div class="row">
	<div class="col-md-8">
		<h4 class="mb-3">Login</h4>

		<form class="needs-validation mb-3" method="post" action="login.php">
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

			<button class="btn btn-primary btn-lg btn-block" type="submit" value="submit" name="submit">Login</button>
		</form>

		<p>
			New customer? <a href="/registration.php">Create your account</a>
		</p>
	</div>
</div>

<?php siteFooter();
