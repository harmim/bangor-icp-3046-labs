<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main;

use Nette;


/**
 * Helpers static class.
 *
 * @package Main
 */
class Helpers
{
	use Nette\StaticClass;

	/**
	 * Formats given price value.
	 *
	 * @param mixed $price price value
	 * @return string formated price in string format
	 */
	public static function formatPrice($price): string
	{
		$price = (float) $price;
		if (!$price) {
			return 'Free';
		}

		return '£&nbsp;' . number_format($price, 2, '.', '&nbsp;');
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


	/**
	 * Returns running script name.
	 *
	 * @param bool $withExtension get file with extension
	 * @param bool $fullPath get full path of script
	 * @return string running script name
	 */
	public static function getScriptName(bool $withExtension = false, bool $fullPath = false): string
	{
		$scriptName = $_SERVER['SCRIPT_NAME'];

		if (!$fullPath) {
			$explodedScriptName = explode(DIRECTORY_SEPARATOR, $scriptName);
			$scriptName = end($explodedScriptName);
		}

		if (!$withExtension) {
			$scriptName = pathinfo($scriptName, PATHINFO_FILENAME);
		}

		return $scriptName;
	}


	/**
	 * Trimmes given array.
	 *
	 * @param $array array to be trimmed
	 * @return void
	 */
	public static function trimArray(array &$array): void
	{
		array_walk_recursive($array, function (&$item) {
			$item = trim($item);
		});
	}


	/**
	 * Returns mail HTML body from specific template.
	 *
	 * @param string $template template name without extension
	 * @param array $parameters template variables values
	 * @return string mail HTML body
	 */
	public static function getMailHtmlBody(string $template, array $parameters = []): string
	{
		$template = __SRC_DIR__ . "/templates/mails/$template.phtml";
		if (!is_readable($template)) {
			throw new \RuntimeException("Template '$template' has been not found.");
		}

		return call_user_func(function (string $_template, array $_parameters) {
			foreach ($_parameters as $_parameterName => $_parameter) {
				$$_parameterName = $_parameter;
			}

			try {
				ob_start();
				require $_template;

			} finally {
				return ob_get_clean();
			}
		}, $template, $parameters);
	}
}
