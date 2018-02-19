<?php

/**
 * Registration page.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable\PersonalInformationForm;


require_once __DIR__ . '/includes/configuration.php';
Configuration::setTitleSection('Registration');


// TODO: Redirect away if user is already logged in.
// TODO: Process, fill and validate registration form.

siteHeader();

?>

<div class="row">
	<div class="col-md-8">
		<h4 class="mb-3">Registration</h4>

		<?php

		$registrationForm = new PersonalInformationForm('registration.php', [
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
