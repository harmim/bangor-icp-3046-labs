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
	 * Returns variable provided to the script via URL query ($_GET).
	 * If no key is passed, returns the entire array.
	 *
	 * @param string $key key of $_GET variable
	 * @param bool $trim trim $_POST fields
	 * @return mixed $_GET variable
	 */
	function getQuery(string $key = null, bool $trim = true);


	/**
	 * Returns variable provided to the script via POST method ($_POST).
	 * If no key is passed, returns the entire array.
	 *
	 * @param string $key key of $_POST variable
	 * @param bool $trim trim $_POST fields
	 * @return mixed $_POST variable
	 */
	function getPost(string $key = null, bool $trim = true);


	/**
	 * Returns variable provided to the script via HTTP cookies ($_COOKIE).
	 * If no key is passed, returns the entire array.
	 *
	 * @param string $key key of $_COOKIE variable
	 * @return mixed HTTP $_COOKIE variable
	 */
	function getCookie(string $key = null);


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


	/**
	 * Returns the IP address of the remote client.
	 *
	 * @return string|null IP address of the remote client
	 */
	function getRemoteAddress(): ?string;


	/**
	 * Returns running script name.
	 *
	 * @param bool $withExtension get file with extension
	 * @param bool $fullPath get full path of script
	 * @return string running script name
	 */
	function getScriptName(bool $withExtension = false, bool $fullPath = false): string;
}
