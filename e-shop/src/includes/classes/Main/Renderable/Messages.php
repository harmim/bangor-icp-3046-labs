<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;


/**
 * Messages renderable component.
 * Allows push messages of specific type and then their rendering.
 *
 * @package Main\Renderable
 */
class Messages implements IRenderableStatic
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
	 * @var array messages to be render
	 */
	private static $messages = [];


	/**
	 * @inheritdoc
	 */
	public static function render(): void
	{
		foreach (self::$messages as $type => $messages) {
			foreach ($messages as $message) {
				$html = '
					<div class="alert alert-%s alert-dismissable">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>%s
					</div>
				';

				printf($html, $type, escape($message));
			}
		}

		self::cleanMessages();
	}


	/**
	 * Add new message of specific type.
	 *
	 * @param string $message message to be rendered
	 * @param string $type one of TYPE_... constant
	 * @return void
	 */
	public static function addMessage(string $message, string $type = self::TYPE_INFO): void
	{
		if (!in_array($type, self::TYPES, true)) {
			trigger_error(sprintf('Unknown message type %s.', $type), E_USER_WARNING);
		}

		// TODO: add message to session
		self::$messages[$type][] = $message;
	}


	/**
	 * Clean all stored messages.
	 *
	 * @return void
	 */
	public static function cleanMessages(): void
	{
		// TODO: remove messages from session
		self::$messages = [];
	}
}
