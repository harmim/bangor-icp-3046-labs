<?php

/**
 * Registration page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;
use Main\Security;
use Main\ValidationException;


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Registration');
$user = Configuration::getUser();

// redirect user to personal information page if he is already logged in
if ($user->isLoggedIn()) {
	Configuration::redirect('personal_information.php');
}

$messages = Configuration::getMessages();
// process and validate registration form
$post = Configuration::getHttpRequest()->getPost();
if (isset($post['submit'])) {
	if (
		!empty ($post['email'])
		&& !empty ($post['forename'])
		&& !empty ($post['surname'])
		&& !empty ($post['password'])
		&& !empty ($post['confirmPassword'])
	) {
		try {
			Configuration::getUserService()->createUser(
				$post['email'],
				$post['forename'],
				$post['surname'],
				$post['password'],
				$post['confirmPassword']
			);

			$user->login($post['email'], $post['password']);
			$user->setExpiration(Configuration::getConfig('session', 'login_expiration'));
			$messages->addMessage('You have been successfully registered and logged in.', $messages::TYPE_SUCCESS);

			if ($backLink = Configuration::getHttpRequest()->getCookie('loginBackLink')) {
				Configuration::getHttpResponse()->deleteCookie('loginBackLink');
				Configuration::redirect($backLink);

			} else {
				Configuration::redirect('index.php');
			}

		} catch (ValidationException | Security\AuthenticationException $e) {
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
		<h4 class="mb-3">Registration</h4>

		<?php

		$registrationForm = new Renderable\PersonalInformationForm('registration.php', [
			'email' => [
				'value' => $post['email'] ?? null,
				'required' => true,
			],
			'forename' => [
				'value' => $post['forename'] ?? null,
				'required' => true,
			],
			'surname' => [
				'value' => $post['surname'] ?? null,
				'required' => true,
			],
			'password' => [
				'required' => true,
			],
			'confirmPassword' => [
				'required' => true,
			],
			'submit' => [
				'value' => 'Register',
			],
		]);
		$registrationForm->render();

		?>
	</div>
</div>

<?php siteFooter();
