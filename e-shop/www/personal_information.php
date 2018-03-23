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


require_once __DIR__ . '/../src/configuration.php';


Configuration::setTitleSection('Personal information');
$user = Configuration::getUser();
$messages = Configuration::getMessages();

// redirect user to login page if he is logged out
if (!$user->isLoggedIn()) {
	$messages->addMessage('You have to be logged in if you want to display your personal information.');
	Configuration::getHttpResponse()->setCookie('loginBackLink', 'personal_information.php', '10 minutes');
	Configuration::redirect('login.php');
}

// process and validate personal information form
$identity = $user->getIdentity();
$post = Configuration::getHttpRequest()->getPost();
if (isset($post['submit'])) {
	if (
		!empty($post['forename'])
		&& !empty($post['surname'])
		&& (
			(empty($post['password']) && empty($post['confirmPassword']))
			|| (!empty($post['password']) && !empty($post['confirmPassword']))
		)
	) {
		$userService = Configuration::getUserService();
		try {
			$data = [
				'forename' => $post['forename'],
				'surname' => $post['surname'],
			];
			if (!empty($post['password'])) {
				$data += [
					'password' => $post['password'],
					'confirmPassword' => $post['confirmPassword'],
				];
			}
			$userService->updateUser($identity->getId(), $data);
			$messages->addMessage('Personal information have been successfully updated.', $messages::TYPE_SUCCESS);

			// update identity
			$updatedUser = $userService->getUserById($identity->getId());
			$identity = new Security\Identity($updatedUser['id'], $updatedUser);
			$user->setIdentity($identity);

		} catch (UnexpectedValueException $e) {
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
		<h4 class="mb-3">Personal information</h4>

		<?php

		$userData = $identity->getData();
		$personalInformationForm = new Renderable\PersonalInformationForm('personal_information.php', [
			'email' => [
				'value' => $userData['email'],
				'required' => true,
				'disabled' => true,
			],
			'forename' => [
				'value' => $userData['forename'],
				'required' => true,
			],
			'surname' => [
				'value' => $userData['surname'],
				'required' => true,
			],
			'submit' => [
				'value' => 'Save',
			],
		]);
		$personalInformationForm->render();

		?>
	</div>
</div>

<?php siteFooter();
