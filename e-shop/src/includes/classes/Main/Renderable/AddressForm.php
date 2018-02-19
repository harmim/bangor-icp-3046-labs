<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;


/**
 * Address form component.
 *
 * @package Main\Renderable
 */
class AddressForm implements IRenderable
{
	/**
	 * @var string form name
	 */
	private $formName;

	/**
	 * @var array form options
	 */
	private $options = [];


	/**
	 * Creates Address form component.
	 *
	 * @param string form name
	 * @param array $options form options
	 */
	public function __construct(string $formName, array $options = [])
	{
		$this->options = $options;
	}


	/**
	 * @inheritdoc
	 */
	public function render(): void
	{
		$html = sprintf(
			'
			<div class="form-row">
				<div class="col-md-6 form-group">
					<label for="formNameForename">Forename</label>
					<input type="text" class="form-control" id="formNameForename" name="formNameForename" value="%s" placeholder="Enter forename" required>
				</div>

				<div class="col-md-6 form-group">
					<label for="formNameSurname">Surname</label>
					<input type="text" class="form-control" id="formNameSurname" name="formNameSurname" value="%s" placeholder="Enter surname" required>
				</div>
			</div>

			<div class="mb-3">
				<label for="formNameAddress">Address</label>
				<input type="text" class="form-control" id="formNameAddress" name="formNameAddress" placeholder="Enter address" required>
			</div>

			<div class="form-row">
				<div class="col-md-6 mb-3">
					<label for="formNameCity">City</label>
					<input type="text" class="form-control" id="formNameCity" name="formNameCity" placeholder="Enter city" required>
				</div>

				<div class="col-md-6 mb-3">
					<label for="formNameZip">Zip</label>
					<input type="text" class="form-control" id="formNameZip" name="formNameZip" placeholder="Enter zip" required>
				</div>
			</div>
			',
			$this->options['forename']['value'],
			$this->options['surname']['value']
		);
		$html = str_replace('formName', $this->formName, $html);

		echo $html;
	}
}
