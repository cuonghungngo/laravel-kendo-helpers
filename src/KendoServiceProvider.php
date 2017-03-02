<?php

namespace Windy\Kendo;

use Illuminate\Support\ServiceProvider;

class KendoServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->app['kendo'] = $this->app->share(function ($app) {
			return new Kendo;
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
