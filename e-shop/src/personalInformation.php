<?php

/**
 * User personal information.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;
use Main\Security;
use Main\Service;


require_once __DIR__ . '/includes/configuration.php';


Configuration::setTitleSection('Personal information');
$user = Configuration::getUser();
$identity = $user->getIdentity();

// redirect user to login page if he is logged out
if (!$user->isLoggedIn()) {
	Configuration::redirect('login.php');
}

// process and validate personal information form
$form = Configuration::getHttpRequest()->getPost();
if (isset($form['submit'])) {
	if (
		!empty($form['forename'])
		&& !empty($form['surname'])
		&& (
			(empty($form['password']) && empty($form['confirmPassword']))
			|| (!empty($form['password']) && !empty($form['confirmPassword']))
		)
	) {
		/** @var Service\UserService $userService */
		$userService = Configuration::getService(Service\UserService::class);
		try {
			$data = [
				'forename' => $form['forename'],
				'surname' => $form['surname'],
			];
			if (!empty($form['password'])) {
				$data += [
					'password' => $form['password'],
					'confirmPassword' => $form['confirmPassword'],
				];
			}
			$userService->updateUser($identity->getId(), $data);

			Renderable\Messages::addMessage(
				'Personal information have been successfully updated.',
				Renderable\Messages::TYPE_SUCCESS
			);

			// update identity
			$updatedUser = $userService->getUserById($identity->getId());
			$identity = new Security\Identity($updatedUser['id'], $updatedUser);
			$user->setIdentity($identity);

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
		<h4 class="mb-3">Personal information</h4>

		<?php

		$userData = $identity->getData();
		$personalInformationForm = new Renderable\PersonalInformationForm('personalInformation.php', [
			'email' => [
				'value' => $userData['email'],
				'disabled' => true,
			],
			'forename' => [
				'value' => $userData['forename'],
			],
			'surname' => [
				'value' => $userData['surname'],
			],
			'submit' => [
				'value' => 'Save',
			],
		]);
		$personalInformationForm->render();

		?>
	</div>
</div>

<?php siteFooter(); ?>
