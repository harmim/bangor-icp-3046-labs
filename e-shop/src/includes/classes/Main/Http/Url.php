<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Http;


/**
 * URL representation (RFC 3986).
 *
 * @package Main\Http
 */
class Url
{
	/**
	 * @var string URL scheme, e.g. http
	 */
	private $scheme = '';

	/**
	 * @var string URL user
	 */
	private $user = '';

	/**
	 * @var string URL password
	 */
	private $password = '';

	/**
	 * @var string URL host
	 */
	private $host = '';

	/**
	 * @var int|null URL port
	 */
	private $port;

	/**
	 * @var string URL path
	 */
	private $path = '';

	/**
	 * @var array URL query parameters, after question mark ?
	 */
	private $query = [];

	/**
	 * @var string URL fragment, after the hashmark #
	 */
	private $fragment = '';


	/**
	 * Creates empty URL or URL from string URL or from Url object.
	 *
	 * @param null|string|Url $url create new url from string or Url object
	 *
	 * @throws \InvalidArgumentException in case of unsupported initial URL
	 */
	public function __construct($url = null)
	{
		if ($url instanceof self) {
			foreach ($this as $key => $value) {
				$functionSuffix = ucfirst($key);
				$this->{"set$functionSuffix"}($url->{"get$functionSuffix"}());
			}

		} elseif (is_string($url)) {
			if (($parsedUrl = @parse_url($url)) == false) {
				throw new \InvalidArgumentException("Unsupported URL '$url'.");
			}

			$this->setScheme($parsedUrl['scheme'] ?? '');
			$this->setUser(isset($parsedUrl['user']) ? rawurldecode($parsedUrl['user']) : '');
			$this->setPassword(isset($parsedUrl['pass']) ? rawurldecode($parsedUrl['pass']) : '');
			$this->setHost(isset($parsedUrl['host']) ? rawurldecode($parsedUrl['host']) : '');
			$this->setPort($parsedUrl['port'] ?? null);
			$this->setPath($parsedUrl['path'] ?? '');
			$this->setQuery($parsedUrl['query'] ?? []);
			$this->setFragment(isset($parsedUrl['fragment']) ? rawurldecode($parsedUrl['fragment']) : '');
		}
	}


	/**
	 * Returns URL scheme.
	 *
	 * @return string URL scheme
	 */
	public function getScheme(): string
	{
		return $this->scheme;
	}


	/**
	 * Sets URL scheme.
	 *
	 * @param string $scheme URL scheme
	 * @return Url self
	 */
	public function setScheme(string $scheme): Url
	{
		$this->scheme = $scheme;

		return $this;
	}


	/**
	 * Returns URL user.
	 *
	 * @return string URL user
	 */
	public function getUser(): string
	{
		return $this->user;
	}


	/**
	 * Sets URL user.
	 *
	 * @param string $user URL user
	 * @return Url self
	 */
	public function setUser(string $user): Url
	{
		$this->user = $user;

		return $this;
	}


	/**
	 * Returns URL password.
	 *
	 * @return string URL password
	 */
	public function getPassword(): string
	{
		return $this->password;
	}


	/**
	 * Sets URL password.
	 *
	 * @param string $password URL password
	 * @return Url self
	 */
	public function setPassword(string $password): Url
	{
		$this->password = $password;

		return $this;
	}


	/**
	 * Returns URL host.
	 *
	 * @return string URL host
	 */
	public function getHost(): string
	{
		return $this->host;
	}


	/**
	 * Sets URL host.
	 *
	 * @param string $host URL host
	 * @return Url self
	 */
	public function setHost(string $host): Url
	{
		$this->host = $host;
		$this->setPath($this->getPath());

		return $this;
	}


	/**
	 * Returns URL port.
	 *
	 * @return int|null URL port
	 */
	public function getPort(): ?int
	{
		return $this->port;
	}


	/**
	 * Sets URL port.
	 *
	 * @param int|null $port URL port
	 * @return Url self
	 */
	public function setPort(?int $port): Url
	{
		$this->port = $port;

		return $this;
	}


	/**
	 * Returns URL path.
	 *
	 * @return string URL path
	 */
	public function getPath(): string
	{
		return $this->path;
	}


	/**
	 * Sets URL path.
	 *
	 * @param string $path URL path
	 * @return Url self
	 */
	public function setPath(string $path): Url
	{
		if ($this->getHost() && substr($path, 0, 1) !== '/') {
			$path = "/$path";
		}
		$this->path = $path;

		return $this;
	}


	/**
	 * Returns URL query.
	 *
	 * @return string URL query
	 */
	public function getQuery(): string
	{
		return http_build_query($this->query, '', '&', PHP_QUERY_RFC3986);
	}


	/**
	 * Sets URL query.
	 *
	 * @param string|array $query URL query
	 * @return Url self
	 */
	public function setQuery($query): Url
	{
		if (!is_array($query)) {
			parse_str((string) $query, $query);
		}
		$this->query = $query;

		return $this;
	}


	/**
	 * Appends query to current URL query.
	 *
	 * @param string|array $query URL query to be appended
	 * @return Url self
	 */
	public function appendQuery($query): Url
	{
		if (!is_array($query)) {
			parse_str((string) $query, $query);
		}
		$this->query = $query + $this->query;

		return $this;
	}


	/**
	 * Returns URL query parameters.
	 *
	 * @return array URL query parameters
	 */
	public function getQueryParameters(): array
	{
		return $this->query;
	}


	/**
	 * Returns particular query parameter.
	 *
	 * @param string $name query parameter name
	 * @return mixed query parameter value
	 */
	public function getQueryParameter(string $name)
	{
		return $this->query[$name] ?? null;
	}


	/**
	 * Sets particular query parameter value.
	 *
	 * @param string $name query parameter name
	 * @param mixed $value query parameter value
	 * @return Url self
	 */
	public function setQueryParameter(string $name, $value): Url
	{
		$this->query[$name] = $value;

		return $this;
	}


	/**
	 * Returns URL fragment.
	 *
	 * @return string URL fragment
	 */
	public function getFragment(): string
	{
		return $this->fragment;
	}


	/**
	 * Sets URL fragment.
	 *
	 * @param string $fragment URL fragment
	 * @return Url self
	 */
	public function setFragment(string $fragment): Url
	{
		$this->fragment = $fragment;

		return $this;
	}


	/**
	 * Returns authority URL (user, password, host and port).
	 *
	 * @return string authority URL (user, password, host and port)
	 */
	public function getAuthority(): string
	{
		return $this->getHost() !== ''
			? (
				$this->getUser() !== ''
				? rawurlencode($this->getUser())
				. ($this->getPassword() !== '' ? ':' . rawurlencode($this->getPassword()) : '')
				. '@'
				: ''
			)
			. rawurlencode($this->getHost())
			. ($this->getPort() ? ":{$this->getPort()}" : '')
			: '';
	}


	/**
	 * Returns host part of URL (scheme and authority).
	 *
	 * @return string host part of URL (scheme and authority)
	 */
	public function getHostUrl(): string
	{
		return ($this->getScheme() !== '' ? "{$this->getScheme()}:" : '')
			. (($authority = $this->getAuthority()) || $this->getScheme() ? "//$authority" : '');
	}


	/**
	 * Returns entire URL including all parts.
	 *
	 * @return string entire URL including all parts
	 */
	public function getAbsoluteUrl(): string
	{
		return $this->getHostUrl()
			. $this->getPath()
			. (($query = $this->getQuery()) ? "?$query" : '')
			. ($this->getFragment() !== '' ? '#' . rawurlencode($this->getFragment()) : '');
	}


	/**
	 * Returns string representation of URL.
	 *
	 * @return string string representation of URL
	 */
	public function __toString(): string
	{
		return $this->getAbsoluteUrl();
	}
}
