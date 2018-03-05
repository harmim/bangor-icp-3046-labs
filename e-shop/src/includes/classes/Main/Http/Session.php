<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;


/**
 * Provides access to session sections as well as session settings and management methods.
 *
 * @package Main\Http
 */
class Session
{
	/**
	 * session variables names
	 */
	public const
		SESSION_NAME = 'MAIN',
		SESSION_DATA = 'DATA',
		SESSION_METADATA = 'META',
		SESSION_TIME = 'T';

	/**
	 * lifetime value 14 days
	 */
	private const LIFETIME = 60 * 60 * 24 * 14;

	/**
	 * configuration
	 */
	private const OPTIONS = [
		// security
		'referer_check' => '', // must be disabled because PHP implementation is invalid
		'use_cookies' => 1, // must be enabled to prevent Session Hijacking and Fixation
		'use_only_cookies' => 1, // must be enabled to prevent Session Fixation
		'use_trans_sid' => 0, // must be disabled to prevent Session Hijacking and Fixation

		// cookies
		'cookie_lifetime' => self::LIFETIME,
		'cookie_path' => '/', // cookie is available within the entire domain
		'cookie_domain' => '', // cookie is available on current sub-domain only
		'cookie_secure' => false, // cookie is available on HTTP & HTTPS
		'cookie_httponly' => true, // must be enabled to prevent Session Hijacking

		// other
		'gc_maxlifetime' => self::LIFETIME,
	];


	/**
	 * @var bool has been session started?
	 */
	private $started = false;

	/**
	 * @var bool has been session ID regenerated?
	 */
	private $regenerated = false;

	/**
	 * @var IRequest HTTP request
	 */
	private $httpRequest;

	/**
	 * @var IResponse HTTP response
	 */
	private $httpResponse;


	/**
	 * Creates session.
	 *
	 * @param IRequest $httpRequest HTTP request
	 * @param IResponse $httpResponse HTTP response
	 */
	public function __construct(IRequest $httpRequest, IResponse $httpResponse)
	{
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->started = session_status() === PHP_SESSION_ACTIVE;
	}


	/**
	 * Starts and initializes session data.
	 *
	 * @return void
	 *
	 * @throws \Exception if session_start failed
	 */
	public function start(): void
	{
		if ($this->started) {
			return;
		}

		$this->configure();

		$id = $this->httpRequest->getCookie(session_name());
		if (is_string($id) && preg_match('~^[0-9a-zA-Z,-]{22,256}\z~i', $id)) {
			session_id($id);

		} else {
			unset($_COOKIE[session_name()]);
		}

		try {
			session_start();
		} catch (\Exception $e) {
			@session_write_close();
			throw $e;
		}

		$this->started = true;

		$session = &$_SESSION[self::SESSION_NAME];
		if (!is_array($session)) {
			$session = [];
		}

		// regenerate empty session
		if (empty($session[self::SESSION_TIME])) {
			$session[self::SESSION_TIME] = time();
			$this->regenerated = true;
		}

		// resend cookie
		$this->sendCookie();

		// process meta metadata
		if (isset($session[self::SESSION_METADATA])) {
			$now = time();

			// expire section variables
			foreach ($session[self::SESSION_METADATA] as $section => $metadata) {
				if (is_array($metadata)) {
					foreach ($metadata as $variable => $value) {
						if (!empty($value[self::SESSION_TIME]) && $now > $value[self::SESSION_TIME]) {
							if ($variable === '') { // expire whole section
								unset(
									$session[self::SESSION_METADATA][$section],
									$session[self::SESSION_DATA][$section]
								);
								continue 2;
							}

							unset(
								$session[self::SESSION_METADATA][$section][$variable],
								$session[self::SESSION_DATA][$section][$variable]
							);
						}
					}
				}
			}
		}

		if ($this->regenerated) {
			$this->regenerated = false;
			$this->regenerateId();
		}

		register_shutdown_function([$this, 'clean']);
	}


	/**
	 * Does session exists for the current request?
	 *
	 * @return bool true if session exists, false otherwise
	 */
	public function exists(): bool
	{
		return $this->started || $this->httpRequest->getCookie(session_name()) !== null;
	}


	/**
	 * Regenerates the session ID.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException if HTTP headers have been sent
	 */
	public function regenerateId(): void
	{
		if ($this->started && !$this->regenerated) {
			if ($this->httpResponse->isSent()) {
				throw new \RuntimeException('Cannot regenerate session ID after HTTP headers have been sent.');
			}

			if (session_status() === PHP_SESSION_ACTIVE) {
				session_regenerate_id(true);
				session_write_close();
			}

			$backup = $_SESSION;
			session_start();
			$_SESSION = $backup;
		}

		$this->regenerated = true;
	}


	/**
	 * Returns specified session section.
	 *
	 * @param string $name section name
	 * @return SessionSection created session section
	 */
	public function getSection(string $name): SessionSection
	{
		return new SessionSection($this, $name);
	}


	/**
	 * Cleans and minimizes meta structures.
	 * This method is called automatically on shutdown, do not call it directly.
	 *
	 * @return void
	 *
	 * @internal
	 */
	public function clean(): void
	{
		if (!$this->started || empty($_SESSION)) {
			return;
		}

		$session = &$_SESSION[self::SESSION_NAME];
		if (isset($session[self::SESSION_METADATA]) && is_array($session[self::SESSION_METADATA])) {
			foreach ($session[self::SESSION_METADATA] as $name => $val) {
				if (empty($session[self::SESSION_METADATA][$name])) {
					unset($session[self::SESSION_METADATA][$name]);
				}
			}
		}

		if (empty($session[self::SESSION_METADATA])) {
			unset($session[self::SESSION_METADATA]);
		}

		if (empty($session[self::SESSION_DATA])) {
			unset($session[self::SESSION_DATA]);
		}
	}


	/**
	 * Configures session environment.
	 *
	 * @return void
	 *
	 * @throws \RuntimeException if session has been already started
	 */
	private function configure(): void
	{
		static $special = [
			'cache_expire' => 1,
			'cache_limiter' => 1,
			'save_path' => 1,
			'name' => 1,
		];

		foreach (self::OPTIONS as $key => $value) {
			if ($value === null || ini_get("session.$key") == $value) {
				continue;

			} elseif (strncmp($key, 'cookie_', 7) === 0) {
				if (!isset($cookie)) {
					$cookie = session_get_cookie_params();
				}
				$cookie[substr($key, 7)] = $value;

			} else {
				if (session_status() === PHP_SESSION_ACTIVE) {
					throw new \RuntimeException(
						"Unable to set 'session.$key' to value '$value' when session has been started."
					);
				}

				if (isset($special[$key])) {
					$key = "session_$key";
					$key($value);

				} else {
					@ini_set("session.$key", (string) $value);
				}
			}
		}

		if (isset($cookie)) {
			session_set_cookie_params(
				$cookie['lifetime'],
				$cookie['path'],
				$cookie['domain'],
				$cookie['secure'],
				$cookie['httponly']
			);

			if ($this->started) {
				$this->sendCookie();
			}
		}
	}


	/**
	 * Sends the session cookies.
	 *
	 * @return void
	 */
	private function sendCookie(): void
	{
		$cookie = session_get_cookie_params();
		$this->httpResponse->setCookie(
			session_name(),
			session_id(),
			$cookie['lifetime'] ? $cookie['lifetime'] + time() : 0,
			$cookie['path'],
			$cookie['domain'],
			$cookie['secure'],
			$cookie['httponly']
		);
	}
}
