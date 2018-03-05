<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;

use Main\Http;
use Main\Security;
use Main\Service;


/**
 * Static configuration class.
 *
 * @package Main
 */
class Configuration
{
	/**
	 * default HTML title tag content
	 */
	public const DEFAULT_HTML_TITLE = 'Inside';

	/**
	 * database details
	 */
	private const
		DATABASE_HOST = 'localhost',
		DATABASE_NAME = 'icp3046_eshop',
		DATABASE_USER = 'root',
		DATABASE_PASSWORD = '';

	/**
	 * time zone
	 */
	private const TIME_ZONE = 'Europe/London';


	/**
	 * @var string HTML title tag content
	 */
	private static $title = self::DEFAULT_HTML_TITLE;

	/**
	 * @var bool enable debug mode
	 */
	private static $debugMode = true;

	/**
	 * @var User user authentication
	 */
	private static $user;

	/**
	 * @var Http\IRequest|null HTTP request
	 */
	private static $httpRequest;

	/**
	 * @var Http\IResponse HTTP|null HTTP response
	 */
	private static $httpResponse;

	/**
	 * @var Http\Session|null session
	 */
	private static $session;

	/**
	 * @var array array of instances of services classes
	 */
	private static $services = [];


	/**
	 * Initialize configuration.
	 *
	 * @return void
	 */
	public static function initialize(): void
	{
		self::setErrorReporting();
		self::autoloadRegister();
		self::setTimeZone();
		self::setHtmlHeaders();

		Database::initialize(
			self::DATABASE_HOST,
			self::DATABASE_NAME,
			self::DATABASE_USER,
			self::DATABASE_PASSWORD
		);
	}


	/**
	 * Returns HTML title tag content.
	 *
	 * @return string HTML title tag content
	 */
	public static function getTitle(): string
	{
		return self::$title;
	}


	/**
	 * Sets HTML title tag content.
	 *
	 * @param string $title HTML title tag content
	 * @return void
	 */
	public static function setTitle(string $title): void
	{
		self::$title = $title;
	}


	/**
	 * Sets HTML title tag section to default title.
	 *
	 * @param string $section title section
	 * @return void
	 */
	public static function setTitleSection(string $section): void
	{
		self::$title = "$section | " . self::DEFAULT_HTML_TITLE;
	}


	/**
	 * Finds out if is debug mode enable.
	 *
	 * @return bool true if debug mode is enabled, false otherwise
	 */
	public static function isDebugMode(): bool
	{
		return self::$debugMode;
	}


	/**
	 * Turn on/off debug mode.
	 *
	 * @param bool $debugMode on/off debug mode
	 * @return void
	 */
	public static function setDebugMode(bool $debugMode): void
	{
		self::$debugMode = $debugMode;
	}


	/**
	 * Returns user authentication object.
	 *
	 * @return User user authentication object
	 */
	public static function getUser(): User
	{
		if (!self::$user) {
			self::$user = new User(self::getSession());
		}

		return self::$user;
	}


	/**
	 * Returns HTTP request.
	 *
	 * @return Http\IRequest HTTP request
	 */
	public static function getHttpRequest(): Http\IRequest
	{
		if (!self::$httpRequest) {
			self::$httpRequest = new Http\Request();
		}

		return self::$httpRequest;
	}


	/**
	 * Returns HTTP response.
	 *
	 * @return Http\IResponse HTTP response
	 */
	public static function getHttpResponse(): Http\IResponse
	{
		if (!self::$httpResponse) {
			self::$httpResponse = new Http\Response();
		}

		return self::$httpResponse;
	}


	/**
	 * Returns session object instance.
	 *
	 * @return Http\Session session object instance
	 */
	public static function getSession(): Http\Session
	{
		if (!self::$session) {
			self::$session = new Http\Session(self::getHttpRequest(), self::getHttpResponse());
		}

		return self::$session;
	}


	/**
	 * Returns instance of service of given class.
	 *
	 * @param string $class class name of service
	 * @return Service\ProductService|Service\UserService|null|object
	 */
	public static function getService(string $class)
	{
		if (isset(self::$services[$class])) {
			return self::$services[$class];
		}

		if (!class_exists($class)) {
			$instance = null;
		} else {
			$instance = new $class();
		}

		return self::$services[$class] = $instance;
	}


	/**
	 * Redirect to new URL.
	 *
	 * @param string $url url for redirection
	 * @param int|null $code HTTP code
	 * @return void
	 */
	public static function redirect(string $url, int $code = null): void
	{
		if ($code === null) {
			$code = self::getHttpRequest()->isMethod(Http\IRequest::METHOD_POST)
				? Http\IResponse::C303_POST_GET
				: Http\IResponse::C302_FOUND;
		}

		self::getHttpResponse()->redirect($url, $code);
	}


	/**
	 * Sets error reporting according to debug mode.
	 *
	 * @return void
	 */
	private static function setErrorReporting(): void
	{
		if (self::isDebugMode()) {
			@ini_set('display_errors', '1');
			error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
		} else {
			@ini_set('display_errors', '0');
			error_reporting(0);
		}
	}


	/**
	 * Registers class autoloader.
	 *
	 * @return void
	 */
	private static function autoloadRegister(): void
	{
		spl_autoload_register(function (string $class) {
			$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
			$class = substr($class, strpos($class, DIRECTORY_SEPARATOR) + 1);

			include __DIR__ . "/$class.php";
		});
	}


	/**
	 * Sets time zone.
	 *
	 * @return void
	 */
	private static function setTimeZone(): void
	{
		date_default_timezone_set(self::TIME_ZONE);
		@ini_set('date.timezone', self::TIME_ZONE);
	}


	/**
	 * Sets HTML headers.
	 *
	 * @return void
	 */
	private static function setHtmlHeaders(): void
	{
		self::getHttpResponse()->setContentType('text/html');
	}
}
