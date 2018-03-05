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
	 * @return IResponse self
	 */
	function setCode(int $code): IResponse;


	/**
	 * Sends a HTTP header and replaces a previous one.
	 *
	 * @param string $name name of HTTP header
	 * @param string|null $value value of HTTP header
	 * @return IResponse self
	 */
	function setHeader(string $name, ?string $value): IResponse;


	/**
	 * Sends a Content-type HTTP header.
	 *
	 * @param string $type type of content
	 * @param string $charset character set
	 * @return IResponse self
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


	/**
	 * Checks if headers have been sent.
	 *
	 * @return bool true if headers have been sent, false otherwise
	 */
	function isSent(): bool;


	/**
	 * Sends a cookie.
	 *
	 * @param string $name cookie name
	 * @param string $value cookie value
	 * @param string|int|\DateTimeInterface|null $expire time, value 0 means "until the browser is closed"
	 * @param string $path cookie path
	 * @param string $domain cookie domain
	 * @param bool $secure is cookie indicates that the cookie should only be transmitted over a secure HTTPS
	 * @param bool $httpOnly when TRUE the cookie will be made accessible only through the HTTP protocol
	 * @return IResponse self
	 */
	function setCookie(
		string $name,
		string $value,
		$expire,
		string $path = null,
		string $domain = null,
		bool $secure = null,
		bool $httpOnly = null
	): IResponse;


	/**
	 * Deletes a cookie.
	 *
	 * @param string $name cookie name
	 * @param string $path cookie path
	 * @param string $domain cookie domain
	 * @param bool $secure is cookie indicates that the cookie should only be transmitted over a secure HTTPS
	 * @return IResponse self
	 */
	function deleteCookie(string $name, string $path = null, string $domain = null, bool $secure = null): IResponse;
}
