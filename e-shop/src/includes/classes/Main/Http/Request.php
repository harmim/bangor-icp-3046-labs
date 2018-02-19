<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;


/**
 * HTTP request implementation.
 *
 * @package Main\Http
 */
class Request implements IRequest
{
	/**
	 * @var string HTTP request method
	 */
	private $method;


	public function __construct()
	{
		$method = $_SERVER['REQUEST_METHOD'] ?? null;
		if (
			$method === self::METHOD_POST && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])
			&& preg_match('~^[A-Z]+\z~', $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])
		) {
			$method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}
		$this->method = $method ?: self::METHOD_GET;
	}


	/**
	 * @inheritdoc
	 */
	public function getMethod(): string
	{
		return $this->method;
	}


	/**
	 * @inheritdoc
	 */
	public function isMethod(string $method): bool
	{
		return strcasecmp($this->method, $method) === 0;
	}
}
