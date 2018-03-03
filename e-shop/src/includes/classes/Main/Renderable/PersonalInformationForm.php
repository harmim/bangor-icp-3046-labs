<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;


/**
 * Personal information form component.
 *
 * @package Main\Renderable
 */
class PersonalInformationForm implements IRenderable
{
	/**
	 * @var string form action
	 */
	private $action;

	/**
	 * @var array form options
	 */
	private $options = [];


	/**
	 * Creates Personal information form component.
	 *
	 * @param string $action form action
	 * @param array $options form options
	 */
	public function __construct(string $action, array $options = [])
	{
		$this->action = $action;
		$this->options = $options;
	}


	/**
	 * @inheritdoc
	 */
	public function render(): void
	{
		printf(
			'
			<form id="registration" method="post" action="%s">
				<div class="form-row">
					<div class="col-md-6 form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" id="email" name="email" value="%s" placeholder="Enter email" required %s>
					</div>
				</div>
	
				<div class="form-row">
					<div class="col-md-6 form-group">
						<label for="forename">Forename</label>
						<input type="text" class="form-control" id="forename" name="forename" value="%s" placeholder="Enter forename" required>
					</div>
	
					<div class="col-md-6 form-group">
						<label for="surname">Surname</label>
						<input type="text" class="form-control" id="surname" name="surname" value="%s" placeholder="Enter surname" required>
					</div>
				</div>
	
				<div class="form-row">
					<div class="col-md-6 form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" id="password" name="password" placeholder="Enter password" minlength="8" aria-describedby="passwordHelpBlock" %s>
						<small id="passwordHelpBlock" class="form-text text-muted">
							Your password must be at least 8 characters long, contain letters and numbers.
						</small>
					</div>
	
					<div class="col-md-6 form-group">
						<label for="confirmPassword">Confirm password</label>
						<input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Enter password again" %s>
					</div>
				</div>
	
				<hr class="mb-4">
	
				<button class="btn btn-primary btn-lg btn-block" type="submit" value="1" name="submit">%s</button>
			</form>
			
			',
			$this->action,
			isset($this->options['email']['value']) ? escape($this->options['email']['value']) : '',
			!empty($this->options['email']['disabled']) ? 'disabled' : '',
			isset($this->options['forename']['value']) ? escape($this->options['forename']['value']) : '',
			isset($this->options['surname']['value']) ? escape($this->options['surname']['value']) : '',
			!empty($this->options['password']['required']) ? 'required' : '',
			!empty($this->options['confirmPassword']['required']) ? 'required' : '',
			isset($this->options['submit']['value']) ? escape($this->options['submit']['value']) : ''
		);
	}
}
