<?php

/**
 * Registration page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;
use Main\Service;


require_once __DIR__ . '/includes/configuration.php';


Configuration::setTitleSection('Registration');
$user = Configuration::getUser();

// redirect user to personal information page if he is already logged in
if ($user->isLoggedIn()) {
	Configuration::redirect('personalInformation.php');
}

$form = Configuration::getHttpRequest()->getPost();
if (isset($form['submit'])) {
	// validation
	if (
		!empty ($form['email'])
		&& !empty ($form['forename'])
		&& !empty ($form['surname'])
		&& !empty ($form['password'])
		&& !empty ($form['confirmPassword'])
	) {
		/** @var Service\UserService $userService */
		$userService = Configuration::getService(Service\UserService::class);
		try {
			$userService->createUser(
				$form['email'],
				$form['forename'],
				$form['surname'],
				$form['password'],
				$form['confirmPassword']
			);

			$user->login($form['email'], $form['password']);
			$user->setExpiration('7 days');
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
				'value' => $form['forename'] ?? null,
			],
			'surname' => [
				'value' => $form['surname'] ?? null,
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
