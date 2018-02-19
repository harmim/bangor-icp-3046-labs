<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;


/**
 * HTTP request interface.
 *
 * @package Main\Http
 */
interface IRequest
{
	/**
	 * HTTP request methods
	 */
	public const
		METHOD_GET = 'GET',
		METHOD_POST = 'POST';


	/**
	 * Returns HTTP request method.
	 *
	 * @return string HTTP request method.
	 */
	function getMethod(): string;


	/**
	 * Checks HTTP request method.
	 *
	 * @param string $method method to compare
	 * @return bool true if methods are equal, false otherwise
	 */
	function isMethod(string $method): bool;
}
