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
	 * @return self
	 */
	public function setScheme(string $scheme): self
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
	 * @return self
	 */
	public function setUser(string $user): self
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
	 * @return self
	 */
	public function setPassword(string $password): self
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
	 * @return self
	 */
	public function setHost(string $host): self
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
	 * @return self
	 */
	public function setPort(?int $port): self
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
	 * @return self
	 */
	public function setPath(string $path): self
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
	 * @return self
	 */
	public function setQuery($query): self
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
	 * @return self
	 */
	public function appendQuery($query): self
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
	 * @return self
	 */
	public function setQueryParameter(string $name, $value): self
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
	 * @return self
	 */
	public function setFragment(string $fragment): self
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
