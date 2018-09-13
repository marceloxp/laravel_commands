<?php

namespace marceloxp\laravel_commands;

use Illuminate\Support\ServiceProvider;

class LaravelCommandsServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				LaravelCommands::class
			]);
		}
	}

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
