<?php

namespace Windy\Kendo\Facades;

use Illuminate\Support\Facades\Facade;

class Kendo extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {return 'kendo';}

}