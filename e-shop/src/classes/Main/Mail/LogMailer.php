<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Mail;

use Nette;


/**
 * Log mailer.
 *
 * @package Main\Mail
 */
class LogMailer implements Nette\Mail\IMailer
{
	use Nette\SmartObject;

	/**
	 * @inheritdoc
	 */
	public function send(Nette\Mail\Message $mail)
	{
		$fileName = __LOG_DIR__ . '/mails/mail-'
			. date('Y-m-d-H-i-s') . '_'
			. Nette\Utils\Random::generate(4)
			. '.eml';

		Nette\Utils\FileSystem::write($fileName, $mail->generateMessage());
	}
}
