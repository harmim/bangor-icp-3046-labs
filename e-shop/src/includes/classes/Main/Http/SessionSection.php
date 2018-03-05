<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;

use Main\Utils;


/**
 * Session section.
 *
 * @package Main\Http
 */
class SessionSection implements \IteratorAggregate, \ArrayAccess, \Countable
{
	/**
	 * @var Session session instance
	 */
	private $session;

	/**
	 * @var string section name
	 */
	private $name;

	/**
	 * @var array session data
	 */
	private $data = [];

	/**
	 * @var array session metadata
	 */
	private $meta = [];

	/**
	 * @var bool has been session section started?
	 */
	private $started = false;


	/**
	 * Creates new session section.
	 * Do not call directly. Use Session::getSection().
	 *
	 * @param Session $session session instance
	 * @param string $name section name
	 *
	 * @internal
	 */
	public function __construct(Session $session, string $name)
	{
		$this->session = $session;
		$this->name = $name;
	}


	/**
	 * Returns a variable from this session section.
	 *
	 * @param string $name variable name
	 * @return mixed variable from session section
	 */
	public function &__get(string $name)
	{
		$this->start();

		return $this->data[$name];
	}


	/**
	 * Sets a variable in this session section.
	 *
	 * @param string $name variable name
	 * @param mixed $value value of variable
	 * @return void
	 */
	public function __set(string $name, $value): void
	{
		$this->start();
		$this->data[$name] = $value;
	}


	/**
	 * Determines whether a variable in this session section is set.
	 *
	 * @param string $name variable name
	 * @return bool true if variable in this session section is set, false otherwise
	 */
	public function __isset(string $name): bool
	{
		if ($this->session->exists()) {
			$this->start();
		}

		return isset($this->data[$name]);
	}


	/**
	 * Unsets a variable in this session section.
	 *
	 * @param string $name variable name
	 * @return void
	 */
	public function __unset(string $name): void
	{
		$this->start();
		unset($this->data[$name], $this->meta[$name]);
	}


	/**
	 * Returns an iterator over all section variables.
	 *
	 * @return \Iterator an iterator over all section variables
	 */
	public function getIterator(): \Iterator
	{
		$this->start();

		return new \ArrayIterator($this->data);
	}


	/**
	 * Returns a variable from this session section.
	 *
	 * @param mixed $name variable name
	 * @return mixed value of variable
	 */
	public function &offsetGet($name)
	{
		$this->checkSectionKey($name);

		return $this->__get($name);
	}


	/**
	 * Sets a variable in this session section.
	 *
	 * @param mixed $name variable name
	 * @param mixed $value value of variable
	 * @return void
	 */
	public function offsetSet($name, $value): void
	{
		$this->checkSectionKey($name);
		$this->__set($name, $value);
	}


	/**
	 * Determines whether a variable in this session section is set.
	 *
	 * @param mixed $name variable name
	 * @return bool true if variable in this session section is set, false otherwise
	 */
	public function offsetExists($name): bool
	{
		$this->checkSectionKey($name);

		return $this->__isset($name);
	}


	/**
	 * Unsets a variable in this session section.
	 *
	 * @param mixed $name variable name
	 * @return void
	 */
	public function offsetUnset($name): void
	{
		$this->checkSectionKey($name);
		$this->__unset($name);
	}


	/**
	 * Returns number of variables in this section.
	 *
	 * @return int number of variables in this section
	 */
	public function count(): int
	{
		return count($this->data);
	}


	/**
	 * Sets the expiration of the section or specific variables.
	 *
	 * @param string|int|\DateTimeInterface|null $time expiration, 0 or null means when a user closes a browser
	 * @param array $variables optional list of variables to expire
	 * @return SessionSection self
	 */
	public function setExpiration($time, array $variables = []): SessionSection
	{
		$this->start();

		if ($time) {
			$time = Utils::datetime($time)->format('U');

			$max = (int) ini_get('session.gc_maxlifetime');
			if ($max !== 0 && ($time - time() > $max + 3)) {
				trigger_error(
					'The expiration time is greater than the session expiration time.',
					E_USER_WARNING
				);
			}
		} else {
			$time = 0;
		}

		if ($variables) {
			foreach ($variables as $variable) {
				$this->checkSectionKey($variable);
				$this->meta[$variable][Session::SESSION_TIME] = $time;
			}

		} else {
			$this->meta[''][Session::SESSION_TIME] = $time;
		}

		return $this;
	}


	/**
	 * Removes the expiration from the section or specific variables.
	 *
	 * @param array $variables optional list of variables to expire
	 * @return SessionSection self
	 */
	public function removeExpiration(array $variables = []): SessionSection
	{
		$this->start();

		if ($variables) {
			foreach ($variables as $variable) {
				$this->checkSectionKey($variable);
				unset($this->meta[$variable][Session::SESSION_TIME]);
			}

		} else {
			unset($this->meta[''][Session::SESSION_TIME]);
		}

		return $this;
	}


	/**
	 * Cancels the current session section.
	 *
	 * @return void
	 */
	public function remove(): void
	{
		$this->start();
		$this->data = [];
		$this->meta = [];
	}


	/**
	 * Starts session and initialize instance variables.
	 */
	private function start(): void
	{
		if (!$this->started) {
			$this->session->start();
			$this->data = &$_SESSION[Session::SESSION_NAME][Session::SESSION_DATA][$this->name];
			$this->data = (array) $this->data;

			$this->meta = &$_SESSION[Session::SESSION_NAME][Session::SESSION_METADATA][$this->name];
			$this->meta = (array) $this->meta;
		}
	}


	/**
	 * Check if session variable key is valid.
	 *
	 * @param mixed $key session variable key
	 * @return void
	 */
	private function checkSectionKey($key): void
	{
		if (!is_string($key)) {
			trigger_error('Variables keys in session section may be only string.', E_USER_WARNING);
		}
	}
}
