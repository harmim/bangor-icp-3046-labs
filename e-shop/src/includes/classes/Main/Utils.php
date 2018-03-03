<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;


/**
 * Utilities static class.
 *
 * @package Main
 */
class Utils
{
	/**
	 * Formats given price value.
	 *
	 * @param mixed $price price value
	 * @return string formated price in string format
	 */
	public static function formatPrice($price): string
	{
		return '£&nbsp;' . number_format((float) $price, 2, '.', ' ');
	}


	/**
	 * \DateTime object factory.
	 *
	 * @param string|int|\DateTimeInterface $time
	 * @return \DateTime
	 */
	public static function datetime($time): \DateTime
	{
		if ($time instanceof \DateTimeInterface) {
			return new \DateTime($time->format('Y-m-d H:i:s.u'), $time->getTimezone());

		} elseif (is_numeric($time)) {
			static $year = 31557600; // year in seconds
			if ($time <= $year) {
				$time += time();
			}

			return (new \DateTime("@$time"))->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		} else { // textual or null
			return new \DateTime($time);
		}
	}
}
