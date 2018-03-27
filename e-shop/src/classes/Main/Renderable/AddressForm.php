<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;

use Nette;


/**
 * Address form component.
 *
 * @package Main\Renderable
 */
class AddressForm implements IRenderable
{
	use Nette\SmartObject;


	/**
	 * @var string form name
	 */
	private $formName;

	/**
	 * @var array form options
	 */
	private $options;


	/**
	 * Creates Address form component.
	 *
	 * @param string form name
	 * @param array $options form options
	 */
	public function __construct(string $formName, array $options = [])
	{
		$this->formName = $formName;
		$this->options = $options;
	}


	/**
	 * @inheritdoc
	 */
	public function render(): void
	{
		$html = Nette\Utils\Html::el();
		$row = Nette\Utils\Html::el('div', [
			'class' => 'form-row',
		]);
		$col = Nette\Utils\Html::el('div', [
			'class' => ['col-md-6', 'form-group'],
		]);

		$html->addHtml(
			(clone $row)
				->addHtml((clone $col)->addHtml($this->createInput('forename', 'Forename')))
				->addHtml((clone $col)->addHtml($this->createInput('surname', 'Surname')))
		);

		$html->create('div', [
			'class' => 'mb-3',
		])->addHtml($this->createInput('address', 'Address'));

		$html->addHtml(
			(clone $row)
				->addHtml((clone $col)->addHtml($this->createInput('city', 'City')))
				->addHtml((clone $col)->addHtml($this->createInput('zip', 'Zip')))
		);

		echo $html;
	}


	/**
	 * Creates HTML input with it's label.
	 *
	 * @param string $name input name
	 * @param string $label input label
	 * @return Nette\Utils\Html HTML input with it's label
	 */
	private function createInput(string $name, string $label): Nette\Utils\Html
	{
		$input = Nette\Utils\Html::el();
		$id = escape($this->formName . ucfirst($name));

		$input->create('label', [
			'for' => $id,
		])->setText($label);

		$attrs = [
			'type' => 'text',
			'class' => 'form-control',
			'id' => $id,
			'name' => $id,
			'placeholder' => escape('Enter ' . lcfirst($label)),
			'required' => true,
		];
		if (isset($this->options[$name]['value'])) {
			$attrs['value'] = escape($this->options[$name]['value']);
		}
		$input->create('input', $attrs);

		return $input;
	}
}
