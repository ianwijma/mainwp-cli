<?php

include_once __DIR__ . '/class-mainwp-cli-site.php';

class MainWP_CLI_Main extends \WP_CLI_Command
{

	public static function init()
	{
		add_action( 'plugin_loaded', ['MainWP_CLI_Main', 'register_commands'] );
	}

	public static function register_commands()
	{
		\WP_CLI::add_command( 'mwp', 'MainWP_CLI_Main' );
		\WP_CLI::add_command( 'mwp site', 'MainWP_CLI_Site' );
	}

	/**
	 * This is just an test command
	 *
	 * @when before_wp_load
	 */
	public function test()
	{
		\WP_CLI::line('This is an test command');
	}


}