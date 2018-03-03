<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;


/**
 * String tools library.
 *
 * @package Main
 */
class Strings
{
	/**
	 * Returns number of characters (not bytes) in UTF-8 string.
	 *
	 * @param string $s input string
	 * @return int number of characters
	 */
	public static function length(string $s): int
	{
		return mb_strlen($s, 'UTF-8');
	}


	/**
	 * Returns a part of UTF-8 string.
	 *
	 * @param string $s input string
	 * @param int $start the first position in input string
	 * @param int $length maximum length of input string
	 * @return string appropriate part of input string
	 */
	public static function substring(string $s, int $start, int $length = null): string
	{
		return mb_substr($s, $start, $length, 'UTF-8');
	}


	/**
	 * Truncates UTF-8 string to maximal length.
	 *
	 * @param string $s input to be truncated
	 * @param int $maxLen maximal length
	 * @param string $append string to be appended if it is truncated, default ...
	 * @return string resulting string
	 */
	public static function truncate(string $s, int $maxLen, string $append = "\u{2026}"): string
	{
		if (self::length($s) > $maxLen) {
			$maxLen -= self::length($append);

			if ($maxLen < 1) {
				return $append;

			} else {
				return self::substring($s, 0, $maxLen) . $append;
			}
		}

		return $s;
	}


	/**
	 * Escape given input for print.
	 *
	 * @param string $s input to be printed
	 * @return string escaped input
	 */
	public static function escape(string $s): string
	{
		return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
	}
}
