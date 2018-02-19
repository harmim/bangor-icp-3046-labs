<?php

/**
 * User personal information.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable\PersonalInformationForm;


require_once __DIR__ . '/includes/configuration.php';
Configuration::setTitleSection('Personal information');


// TODO: Redirect away if user is not logged in.
// TODO: Fill, process and validate personal information form.

siteHeader();

?>

<div class="row">
	<div class="col-md-8">
		<h4 class="mb-3">Personal information</h4>

		<?php

		$personalInformationForm = new PersonalInformationForm('personalInformation.php', [
			'email' => [
				'value' => 'harmim6@gmail.com',
				'disabled' => true,
			],
			'forename' => [
				'value' => 'Dominik',
			],
			'surname' => [
				'value' => 'Harmim',
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
