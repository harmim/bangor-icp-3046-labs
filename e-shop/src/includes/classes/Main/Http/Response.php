<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;

use Main\Utils;


/**
 * HTTP response implementation.
 *
 * @package Main\Http
 */
class Response implements IResponse
{
	/**
	 * Cookie options.
	 */
	private const
		COOKIE_DOMAIN = '',
		COOKIE_PATH = '/',
		COOKIE_SECURE = false,
		COOKIE_HTTP_ONLY = true;


	/**
	 * @var int HTTP code
	 */
	private $code = self::C200_OK;


	public function __construct()
	{
		if (is_int($code = http_response_code())) {
			$this->code = $code;
		}
	}


	/**
	 * @inheritdoc
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setCode(int $code): IResponse
	{
		if ($code < 100 || $code > 599) {
			throw new \InvalidArgumentException("Bad HTTP response '$code'.");
		}

		self::checkHeaders();
		$this->code = $code;
		http_response_code($code);

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function setHeader(string $name, ?string $value): IResponse
	{
		self::checkHeaders();

		if ($value === null) {
			header_remove($name);

		} else {
			header($name . ': ' . $value, true, $this->code);
		}

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function setContentType(string $type, string $charset = 'utf-8'): IResponse
	{
		$this->setHeader('Content-Type', $type . ($charset ? '; charset=' . $charset : ''));

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function redirect(string $url, int $code = self::C302_FOUND): void
	{
		$this->setCode($code);
		$this->setHeader('Location', $url);
		exit(0);
	}


	/**
	 * @inheritdoc
	 */
	public function isSent(): bool
	{
		return headers_sent();
	}


	/**
	 * @inheritdoc
	 */
	public function setCookie(
		string $name,
		string $value,
		$expire,
		string $path = null,
		string $domain = null,
		bool $secure = null,
		bool $httpOnly = null
	): IResponse
	{
		self::checkHeaders();

		setcookie(
			$name,
			$value,
			$expire ? (int) Utils::datetime($expire)->format('U') : 0,
			$path === null ? self::COOKIE_PATH : $path,
			$domain === null ? self::COOKIE_DOMAIN : $domain,
			$secure === null ? self::COOKIE_SECURE : $secure,
			$httpOnly === null ? self::COOKIE_HTTP_ONLY : $httpOnly
		);

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function deleteCookie(string $name, string $path = null, string $domain = null, bool $secure = null): IResponse
	{
		$this->setCookie($name, '', 0, $path, $domain, $secure);

		return $this;
	}


	/**
	 * Check if headers have been sent.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException
	 */
	private function checkHeaders(): void
	{
		if (PHP_SAPI === 'cli') {
			return;

		} elseif (headers_sent($file, $line)) {
			throw new \RuntimeException('Cannot send header after HTTP headers have been sent'
				. ($file ? " (output started at $file:$line)." : '.'));
		}
	}
}
