<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;


/**
 * Renderable interface.
 * All components which can be render should implement this interface.
 *
 * @package Main\Renderable
 */
interface IRenderable
{
	/**
	 * Render component.
	 *
	 * @return void
	 */
	function render(): void;
}
