<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;

use Nette;


/**
 * Messages renderable component.
 * Allows push messages of specific type and then their rendering.
 *
 * @package Main\Renderable
 */
class Messages implements IRenderable
{
	use Nette\SmartObject;


	/**
	 * message types
	 */
	public const
		TYPE_SUCCESS = 'success',
		TYPE_INFO = 'info',
		TYPE_WARNING = 'warning',
		TYPE_DANGER = 'danger';

	/**
	 * array with all message types
	 */
	private const TYPES = [
		self::TYPE_SUCCESS,
		self::TYPE_INFO,
		self::TYPE_WARNING,
		self::TYPE_DANGER,
	];


	/**
	 * @var Nette\Http\SessionSection messages session section
	 */
	private $messagesSection;


	/**
	 * Creates Messages component.
	 *
	 * @param Nette\Http\SessionSection $messagesSection messages session section
	 */
	public function __construct(Nette\Http\SessionSection $messagesSection)
	{
		$this->messagesSection = $messagesSection;
	}


	/**
	 * @inheritdoc
	 */
	public function render(): void
	{
		foreach ($this->messagesSection as $type => $messages) {
			foreach ($messages as $message) {
				$alert = Nette\Utils\Html::el('div', [
					'class' => ['alert', 'alert-dismissable', "alert-$type"],
				]);
				$alert->create('a', [
					'href' => '#',
					'class' => 'close',
					'data-dismiss' => 'alert',
					'aria-label' => 'close',
				])->setHtml('&times;');
				$alert->addText($message);

				echo $alert;
			}
		}

		$this->cleanMessages();
	}


	/**
	 * Add new message of specific type.
	 *
	 * @param string $message message to be rendered
	 * @param string $type one of TYPE_... constant
	 * @return self
	 */
	public function addMessage(string $message, string $type = self::TYPE_INFO): self
	{
		if (!in_array($type, self::TYPES, true)) {
			trigger_error(sprintf('Unknown message type %s.', $type), E_USER_WARNING);
		}

		$this->messagesSection->$type[] = $message;

		return $this;
	}


	/**
	 * Clean all stored messages.
	 *
	 * @return self
	 */
	public function cleanMessages(): self
	{
		$this->messagesSection->remove();

		return $this;
	}
}
