<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;

use Main\Http;


/**
 * Messages renderable component.
 * Allows push messages of specific type and then their rendering.
 *
 * @package Main\Renderable
 */
class Messages implements IRenderable
{
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
	 * @var Http\SessionSection messages session section
	 */
	private $messagesSection;


	/**
	 * Creates Messages component.
	 *
	 * @param Http\SessionSection $messagesSection messages session section
	 */
	public function __construct(Http\SessionSection $messagesSection)
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
				$html = '
					<div class="alert alert-%s alert-dismissable">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>%s
					</div>
				';

				printf($html, $type, escape($message));
			}
		}

		$this->cleanMessages();
	}


	/**
	 * Add new message of specific type.
	 *
	 * @param string $message message to be rendered
	 * @param string $type one of TYPE_... constant
	 * @return Messages self
	 */
	public function addMessage(string $message, string $type = self::TYPE_INFO): Messages
	{
		if (!in_array($type, self::TYPES, true)) {
			trigger_error(sprintf('Unknown message type %s.', $type), E_USER_WARNING);
		}

		$this->messagesSection[$type][] = $message;

		return $this;
	}


	/**
	 * Clean all stored messages.
	 *
	 * @return Messages self
	 */
	public function cleanMessages(): Messages
	{
		$this->messagesSection->remove();

		return $this;
	}
}
