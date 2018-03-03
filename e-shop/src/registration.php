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
use Main\Service;


require_once __DIR__ . '/includes/configuration.php';


Configuration::setTitleSection('Registration');

// redirect user to personal information page if he is already logged in
if (Configuration::getLoggedUser()) {
	Configuration::redirect('personalInformation.php');
}

/** @var Service\UserService $userService */
$userService = Configuration::getService(Service\UserService::class);
$values = Configuration::getHttpRequest()->getPost();
if (isset($values['submit'])) {
	// validation
	if (isset($values['email'], $values['forename'], $values['surname'], $values['password'], $values['confirmPassword'])) {
		try {
			$userService->createUser(
				$values['email'],
				$values['forename'],
				$values['surname'],
				$values['password'],
				$values['confirmPassword']
			);

			Configuration::setLoggedUser((new Security\Authenticator())->authenticate($values['email'], $values['password']));
			Renderable\Messages::addMessage(
				'You have been successfully registered and logged in.',
				Renderable\Messages::TYPE_SUCCESS
			);

			Configuration::redirect('index.php');

		} catch (UnexpectedValueException $e) {
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
		<h4 class="mb-3">Registration</h4>

		<?php

		$registrationForm = new Renderable\PersonalInformationForm('registration.php', [
			'forename' => [
				'value' => $values['forename'] ?? null,
			],
			'surname' => [
				'value' => $values['surname'] ?? null,
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

<?php siteFooter(); ?>
