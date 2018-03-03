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
	 * @var array GET method variables
	 */
	private $queryParameters;

	/**
	 * @var array POST method variables
	 */
	private $post;

	/**
	 * @var array cookies variables
	 */
	private $cookies;

	/**
	 * @var string HTTP request method
	 */
	private $method;

	/**
	 * @var string|null IP address of the remote client
	 */
	private $remoteAddress;

	/**
	 * @var string running script name
	 */
	private $scriptName;


	public function __construct()
	{
		// GET, POST, COOKIE
		$requestUrl = $_SERVER['REQUEST_URI'] ?? '/';
		$requestUrl = preg_replace('~^\w++://[^/]++~', '', $requestUrl);
		$explodedRequestUrl = explode('?', $requestUrl, 2);
		$this->queryParameters = $explodedRequestUrl[1] ?? '';
		parse_str($this->queryParameters, $this->queryParameters);
		$this->queryParameters = (array) $this->queryParameters;

		$this->post = (array) filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
		$this->cookies = (array) filter_input_array(INPUT_COOKIE, FILTER_UNSAFE_RAW);

		// remove invalid characters
		$list = [&$this->queryParameters, &$this->post, &$this->cookies];
		$chars = '\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}';
		$reChars = '~^[' . $chars . ']*+\z~u';
		foreach ($list as $key => &$val) {
			foreach ($val as $k => $v) {
				if (is_string($k) && (!preg_match($reChars, $k) || preg_last_error())) {
					unset($list[$key][$k]);

				} elseif (is_array($v)) {
					$list[$key][$k] = $v;
					$list[] = &$list[$key][$k];

				} else {
					$list[$key][$k] = (string) preg_replace('~[^' . $chars . ']+~u', '', $v);
				}
			}
		}

		// method
		$method = $_SERVER['REQUEST_METHOD'] ?? null;
		if (
			$method === self::METHOD_POST && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])
			&& preg_match('~^[A-Z]+\z~', $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])
		) {
			$method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}
		$this->method = $method ?: self::METHOD_GET;

		// remote address
		$this->remoteAddress = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

		// script name
		$this->scriptName = $_SERVER['SCRIPT_NAME'];
	}


	/**
	 * @inheritdoc
	 */
	public function getQuery(string $key = null)
	{
		if ($key === null) {
			return $this->queryParameters;
		}

		return $this->queryParameters[$key] ?? null;
	}


	/**
	 * @inheritdoc
	 */
	public function getPost(string $key = null)
	{
		if ($key === null) {
			return $this->post;
		}

		return $this->post[$key] ?? null;
	}


	/**
	 * @inheritdoc
	 */
	public function getCookie(string $key = null)
	{
		if ($key === null) {
			return $this->cookies;
		}

		return $this->cookies[$key] ?? null;
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


	/**
	 * @inheritdoc
	 */
	public function getRemoteAddress(): ?string
	{
		return $this->remoteAddress;
	}


	/**
	 * @inheritdoc
	 */
	public function getScriptName(bool $withExtension = false, bool $fullPath = false): string
	{
		$scriptName = $this->scriptName;

		if (!$fullPath) {
			$explodedScriptName = explode(DIRECTORY_SEPARATOR, $scriptName);
			$scriptName = end($explodedScriptName);
		}

		if (!$withExtension) {
			$scriptName = pathinfo($scriptName, PATHINFO_FILENAME);
		}

		return $scriptName;
	}

}
