<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;

use Main\Database;
use Main\Mail;
use Main\Renderable;
use Main\Security;
use Main\Service;
use Nette;


/**
 * Static configuration class.
 *
 * @package Main
 */
class Configuration
{
	use Nette\StaticClass;

	/**
	 * public constants
	 */
	public const
		DOMAIN = 'http://icp3046.localhost.com',
		DOMAIN_WITHOUT_PROTOCOL = 'icp3046.localhost.com';


	/**
	 * default HTML title tag content
	 */
	private const DEFAULT_HTML_TITLE = 'Inside';

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
	 * @var Database\IDatabase|null database wrapper
	 */
	private static $database;

	/**
	 * @var User|null user authentication
	 */
	private static $user;

	/**
	 * @var Nette\Http\IRequest|null HTTP request
	 */
	private static $httpRequest;

	/**
	 * @var Nette\Http\IResponse|null HTTP response
	 */
	private static $httpResponse;

	/**
	 * @var Nette\Http\Session|null session
	 */
	private static $session;

	/**
	 * @var Renderable\Messages|null Messages component
	 */
	private static $messages;

	/**
	 * @var Nette\Mail\IMailer|null mailer
	 */
	private static $mailer;

	/**
	 * @var Service\UserService|null user service
	 */
	private static $userService;

	/**
	 * @var Service\ProductService|null product service
	 */
	private static $productService;

	/**
	 * @var Service\BasketService|null basket service
	 */
	private static $basketService;

	/**
	 * @var Service\OrderService|null order service
	 */
	private static $orderService;


	/**
	 * Initialize configuration.
	 *
	 * @return void
	 */
	public static function initialize(): void
	{
		self::setErrorReporting();
		self::setTimeZone();
		self::setHtmlHeaders();
		self::initializeSession();

		// set basket session expiration
		self::getBasketService()->setExpiration('14 days');
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
	 * Append HTML title tag section to default title.
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
	 * Returns database wrapper.
	 *
	 * @return Database\IDatabase database wrapper
	 */
	public static function getDatabase(): Database\IDatabase
	{
		if (!self::$database) {
			try {
				self::$database = new Database\MySqlDatabase(
					self::DATABASE_HOST,
					self::DATABASE_NAME,
					self::DATABASE_USER,
					self::DATABASE_PASSWORD
				);
			} catch (\PDOException $e) {
				self::getMessages()->addMessage('Database error.', Renderable\Messages::TYPE_DANGER);
				self::$database = new Database\DummyDatabase();
			}
		}

		return self::$database;
	}


	/**
	 * Returns user authentication object.
	 *
	 * @return User user authentication object
	 */
	public static function getUser(): User
	{
		if (!self::$user) {
			self::$user = new User(self::getSession(), new Security\Authenticator());
		}

		return self::$user;
	}


	/**
	 * Returns HTTP request.
	 *
	 * @return Nette\Http\IRequest HTTP request
	 */
	public static function getHttpRequest(): Nette\Http\IRequest
	{
		if (!self::$httpRequest) {
			self::$httpRequest = (new Nette\Http\RequestFactory())->createHttpRequest();
		}

		return self::$httpRequest;
	}


	/**
	 * Returns HTTP response.
	 *
	 * @return Nette\Http\IResponse HTTP response
	 */
	public static function getHttpResponse(): Nette\Http\IResponse
	{
		if (!self::$httpResponse) {
			self::$httpResponse = new Nette\Http\Response();
		}

		return self::$httpResponse;
	}


	/**
	 * Returns session object instance.
	 *
	 * @return Nette\Http\Session session object instance
	 */
	public static function getSession(): Nette\Http\Session
	{
		if (!self::$session) {
			self::$session = new Nette\Http\Session(self::getHttpRequest(), self::getHttpResponse());
		}

		return self::$session;
	}


	/**
	 * Returns Messages component.
	 *
	 * @return Renderable\Messages Messages component
	 */
	public static function getMessages(): Renderable\Messages
	{
		if (!self::$messages) {
			self::$messages = new Renderable\Messages(self::getSession()->getSection('messages'));
		}

		return self::$messages;
	}


	/**
	 * Returns mailer.
	 *
	 * @return Nette\Mail\IMailer mailer
	 */
	public static function getMailer(): Nette\Mail\IMailer
	{
		if (!self::$mailer) {
			if (self::isDebugMode()) {
				self::$mailer = new Mail\LogMailer();
			} else {
				self::$mailer = new Nette\Mail\SendmailMailer();
			}
		}

		return self::$mailer;
	}


	/**
	 * Returns user service.
	 *
	 * @return Service\UserService user service
	 */
	public static function getUserService(): Service\UserService
	{
		if (!self::$userService) {
			self::$userService = new Service\UserService(self::getDatabase());
		}

		return self::$userService;
	}


	/**
	 * Returns product service.
	 *
	 * @return Service\ProductService product service
	 */
	public static function getProductService(): Service\ProductService
	{
		if (!self::$productService) {
			self::$productService = new Service\ProductService(self::getDatabase());
		}

		return self::$productService;
	}


	/**
	 * Returns basket service.
	 *
	 * @return Service\BasketService basket service
	 */
	public static function getBasketService(): Service\BasketService
	{
		if (!self::$basketService) {
			self::$basketService = new Service\BasketService(
				self::getSession()->getSection('basket'),
				self::getProductService()
			);
		}

		return self::$basketService;
	}


	/**
	 * Returns order service.
	 *
	 * @return Service\OrderService order service
	 */
	public static function getOrderService(): Service\OrderService
	{
		if (!self::$orderService) {
			self::$orderService = new Service\OrderService(
				self::getDatabase(),
				self::getSession()->getSection('order'),
				self::getBasketService(),
				self::getHttpRequest(),
				self::getMailer()
			);
		}

		return self::$orderService;
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
			$code = self::getHttpRequest()->isMethod(Nette\Http\IRequest::POST)
				? Nette\Http\IResponse::S303_POST_GET
				: Nette\Http\IResponse::S302_FOUND;
		}

		self::getHttpResponse()->redirect($url, $code);
		exit();
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
		$httpResponse = self::getHttpResponse();

		$httpResponse->setContentType('text/html');

		/// Security Headers
		// page can be inserted to iframe, only when iframe is in the same domain
		$httpResponse->setHeader('X-Frame-Options', 'SAMEORIGIN');
		// runs files by MIME type
		$httpResponse->setHeader('X-Content-Type-Options', 'nosniff');
		// if XSS auditor detects Reflected XSS, page will not show
		$httpResponse->setHeader('X-XSS-Protection', '1; mode=block;');
		// page will load resources only from authorized location
		// remove new lines and multiple white spaces
		$httpResponse->setHeader('Content-Security-Policy', preg_replace(['~\n~', '~\s+~'], ['', ' '], "
			default-src 'self';
			frame-src 'none';
			img-src 'self' data: *;
			style-src 'self' 'unsafe-inline';
			font-src 'self';
			script-src 'self' 'unsafe-inline';
			base-uri 'self';
			form-action 'self';
		"));
	}


	/**
	 * Initializes session.
	 *
	 * @return void
	 */
	private static function initializeSession(): void
	{
		$session = self::getSession();
		$session->setExpiration('30 days');
		if ($session->exists()) {
			$session->start();
		}
	}
}
