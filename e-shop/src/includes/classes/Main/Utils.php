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
	public static function getScriptName(): string
	{
		$explodedScriptName = explode(DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_NAME']);

		return pathinfo(end($explodedScriptName))['filename'];
	}
}
