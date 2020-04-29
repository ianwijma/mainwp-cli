<?php
/**
 * Plugin Name: MainWP CLI
 *
 * Description: Adds proper WP CLI intergration to your MainWP instances
 *
 * Author: Ian Wijma
 * Author URI: https://ian.wij.ma/
 * Plugin URI: https://ian.wij.ma/
 * Version:  1.0.0
 */

// Prevent calling the file directly
if ( !defined( 'ABSPATH' ) ) die;

// Continue only if WP_CLI is loaded
if ( !defined( 'WP_CLI' ) && WP_CLI ) return;

include_once __DIR__ . '/commands/class-mainwp-cli-main.php';
MainWP_CLI_Main::init();