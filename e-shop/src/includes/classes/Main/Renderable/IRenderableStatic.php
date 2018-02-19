<?php

/**
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

namespace Main\Renderable;


/**
 * Renderable static interface.
 * All components which can be render should implement this interface.
 *
 * @package Main\Renderable
 */
interface IRenderableStatic
{
	/**
	 * Render component.
	 *
	 * @return void
	 */
	static function render(): void;
}
