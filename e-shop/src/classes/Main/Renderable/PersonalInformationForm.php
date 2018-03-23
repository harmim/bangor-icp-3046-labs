<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;

use Nette;


/**
 * Personal information form component.
 *
 * @package Main\Renderable
 */
class PersonalInformationForm implements IRenderable
{
	use Nette\SmartObject;


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
		$form = Nette\Utils\Html::el('form', [
			'id' => 'registration',
			'method' => 'post',
			'action' => escape($this->action),
		]);
		$row = Nette\Utils\Html::el('div', [
			'class' => 'form-row',
		]);
		$col = Nette\Utils\Html::el('div', [
			'class' => ['col-md-6', 'form-group'],
		]);

		$form->addHtml((clone $row)->addHtml((clone $col)->addHtml($this->createInput('email', 'Email', 'email'))));

		$form->addHtml(
			(clone $row)
				->addHtml((clone $col)->addHtml($this->createInput('forename', 'Forename', 'text')))
				->addHtml((clone $col)->addHtml($this->createInput('surname', 'Surname', 'text')))
		);

		$password = Nette\Utils\Html::el();
		$password->create('label', [
			'for' => 'password',
		])->setText('Password');
		$attrs = [
			'type' => 'password',
			'class' => 'form-control',
			'id' => 'password',
			'name' => 'password',
			'placeholder' => 'Enter password',
			'minlength' => 8,
			'aria-describedby' => 'passwordHelpBlock',
		];
		if (!empty($this->options['password']['required'])) {
			$attrs['required'] = true;
		}
		$password->create('input', $attrs);
		$password->create('small', [
			'id' => 'passwordHelpBlock',
			'class' => ['form-text', 'text-muted'],
		])->setText('Your password must be at least 8 characters long, contain letters and numbers.');
		$form->addHtml(
			(clone $row)
				->addHtml((clone $col)->addHtml($password))
				->addHtml((clone $col)->addHtml($this->createInput('confirmPassword', 'Confirm password', 'password')))
		);

		$form->create('h4', [
			'class' => 'mb-4',
		]);
		$form->create('button', [
			'class' => ['btn', 'btn-primary', 'btn-lg', 'btn-block'],
			'type' => 'submit',
			'value' => 'submit',
			'name' => 'submit',
		])->setText($this->options['submit']['value'] ?? '');

		echo $form;
	}


	/**
	 * Creates HTML input with it's label.
	 *
	 * @param string $name input name
	 * @param string $label input label
	 * @param string $type input type
	 * @return Nette\Utils\Html HTML input with it's label
	 */
	private function createInput(string $name, string $label, string $type): Nette\Utils\Html
	{
		$input = Nette\Utils\Html::el();
		$id = escape($name);

		$input->create('label', [
			'for' => $id,
		])->setText($label);

		$attrs = [
			'type' => escape($type),
			'class' => 'form-control',
			'id' => $id,
			'name' => $id,
			'placeholder' => escape('Enter ' . lcfirst($label)),
		];
		if (isset($this->options[$name]['value'])) {
			$attrs['value'] = escape($this->options[$name]['value']);
		}
		if (!empty($this->options[$name]['required'])) {
			$attrs['required'] = true;
		}
		if (!empty($this->options[$name]['disabled'])) {
			$attrs['disabled'] = true;
		}
		$input->create('input', $attrs);

		return $input;
	}
}
