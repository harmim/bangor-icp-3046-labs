<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;

use Main\Http;
use Main\Security;


/**
 * User authentication.
 *
 * @package Main
 */
class User
{
	/**
	 * @var Http\Session session instance
	 */
	private $session;

	/**
	 * @var Security\IAuthenticator authenticator instance
	 */
	private $authenticator;

	/**
	 * @var Http\SessionSection|null user session section
	 */
	private $userSection;


	/**
	 * Creates user authentication object.
	 *
	 * @param Http\Session $session session instance
	 * @param Security\IAuthenticator $authenticator authenticator instance
	 */
	public function __construct(Http\Session $session, Security\IAuthenticator $authenticator)
	{
		$this->session = $session;
		$this->authenticator = $authenticator;
	}


	/**
	 * Conducts the authentication process.
	 *
	 * @param string $username username
	 * @param string $password password
	 * @return User self
	 *
	 * @throws Security\AuthenticationException if authentication failed
	 */
	public function login(string $username, string $password): User
	{
		$this->logout();
		$this->setIdentity($this->authenticator->authenticate($username, $password));
		$this->setAuthenticated(true);

		return $this;
	}


	/**
	 * Logs out the user from the current session.
	 *
	 * @return User self
	 */
	public function logout(): User
	{
		if ($this->isLoggedIn()) {
			$this->setAuthenticated(false);
		}
		$this->setIdentity(null);

		return $this;
	}


	/**
	 * Is this user authenticated?
	 *
	 * @return bool true if user is authenticated, false otherwise
	 */
	public function isLoggedIn(): bool
	{
		$section = $this->getUserSection(false);

		return $section && $section->authenticated;
	}


	/**
	 * Returns current user identity, if any.
	 *
	 * @return Security\IIdentity|null user identity, if any
	 */
	public function getIdentity(): ?Security\IIdentity
	{
		$section = $this->getUserSection(false);

		return $section && $section->identity instanceof Security\IIdentity
			? $section->identity
			: null;
	}


	/**
	 * Sets the user identity.
	 *
	 * @param Security\IIdentity|null user identity
	 * @return User self
	 */
	public function setIdentity(?Security\IIdentity $identity): User
	{
		$this->getUserSection()->identity = $identity;

		return $this;
	}


	/**
	 * Enables log out after inactivity.
	 *
	 * @param string|int|\DateTimeInterface $time time expiration
	 * @return User self
	 */
	public function setExpiration($time): User
	{
		$section = $this->getUserSection();
		if ($time) {
			$time = Utils::datetime($time)->format('U');
			$section->expireDelta = $time - time();

		} else {
			unset($section->expireDelta);
		}

		$section->setExpiration($time);

		return $this;
	}


	/**
	 * Sets the authenticated status of this user.
	 *
	 * @param bool $state authenticated status of user
	 * @return User self
	 *
	 * @throws \RuntimeException if HTTP headers have been sent
	 */
	private function setAuthenticated(bool $state): User
	{
		$this->getUserSection()->authenticated = $state;

		// Session Fixation defence
		$this->session->regenerateId();

		return $this;
	}


	/**
	 * Returns user session section.
	 *
	 * @param bool $need need this section
	 * @return Http\SessionSection|null user session section
	 */
	private function getUserSection(bool $need = true): ?Http\SessionSection
	{
		if ($this->userSection) {
			return $this->userSection;
		}

		if (!$need && !$this->session->exists()) {
			return null;
		}

		$this->userSection = $section = $this->session->getSection('user');

		if (!$section->identity instanceof Security\IIdentity || !is_bool($section->authenticated)) {
			$section->remove();
		}

		if ($section->authenticated) {
			if ($section->expireDelta > 0) {
				$section->setExpiration(time() + $section->expireDelta);
			}

		} else {
			unset($section->expireDelta);
		}

		return $section;
	}
}
