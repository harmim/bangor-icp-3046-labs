<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;


/**
 * HTTP response interface.
 *
 * @package Main\Http
 */
interface IResponse
{
	/**
	 * HTTP codes
	 */
	public const
		C200_OK = 200,
		C302_FOUND = 302,
		C303_POST_GET = 303,
		C404_NOT_FOUND = 404,
		C500_INTERNAL_SERVER_ERROR = 500;


	/**
	 * Sets HTTP response code.
	 *
	 * @param int $code HTTP code
	 * @return IResponse
	 */
	function setCode(int $code): IResponse;


	/**
	 * Sends a HTTP header and replaces a previous one.
	 *
	 * @param string $name name of HTTP header
	 * @param string|null $value value of HTTP header
	 * @return IResponse
	 */
	function setHeader(string $name, ?string $value): IResponse;


	/**
	 * Sends a Content-type HTTP header.
	 *
	 * @param string $type type of content
	 * @param string $charset character set
	 * @return IResponse
	 */
	function setContentType(string $type, string $charset = 'utf-8'): IResponse;


	/**
	 * Redirects to a new URL.
	 *
	 * @param string $url new URL
	 * @param int $code HTTP code
	 * @return void
	 */
	function redirect(string $url, int $code = self::C302_FOUND): void;
}
