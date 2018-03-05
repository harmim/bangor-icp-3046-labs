<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Security;


/**
 * Default implementation of IIdentity.
 *
 * @package Main\Security
 */
class Identity implements IIdentity
{
	/**
	 * @var int user ID
	 */
	private $id;

	/**
	 * @var array user data
	 */
	private $data;


	/**
	 * Creates new identity.
	 *
	 * @param int $id user ID
	 * @param iterable $data user data
	 */
	public function __construct(int $id, iterable $data)
	{
		$this->id = $id;
		$this->data = $data instanceof \Traversable ? iterator_to_array($data) : (array) $data;
	}


	/**
	 * @inheritdoc
	 */
	public function getId(): int
	{
		return $this->id;
	}


	/**
	 * @inheritdoc
	 */
	public function getData(): array
	{
		return $this->data;
	}
}
