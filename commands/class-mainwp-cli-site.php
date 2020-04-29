<?php


class MainWP_CLI_Site extends \WP_CLI_Command
{

	/**
	 * Lists the child sites
	 *
	 * @synopsis
	 */
	public function list( $args, $assoc_args )
	{
		// Abusing the sites to give me a list
		$mainWpCommands = new \MainWP_WP_CLI_Command();
		$mainWpCommands->sites( [], ['list' => null] );
	}

	/**
	 * Adds a child site
	 *
	 * ## OPTIONS
	 *
	 * --hostname=<hostname>
	 * : Your website hostname
	 *
	 * --admin=<admin>
	 * : The website Administrator username.
	 *
	 * [--name=<name>]
	 * : The websites title. Defaults to the hostname
	 *
	 * [--unique-id=<uniqueId>]
	 * : Force http on your site
	 *
	 * [--no-https]
	 * : Force http on your site
	 *
	 * [--ignore-ssl]
	 * : Do you dont want to verify SSL certificate.
	 *
	 * [--ssl-version=<sslVersion>]
	 * : Select SSL version. Defaults to "auto"
	 * ---
	 * default: auto
	 * options:
	 *  - auto
	 *  - 1.2
	 *  - 1.x
	 *  - 2
	 *  - 3
	 *  - 1.0
	 *  - 1.1
	 * ---
	 *
	 * [--http-username=<httpUsername>]
	 * : If the child site is HTTP Basic Auth protected, enter the HTTP username here.
	 *
	 * [--http-password=<httpPassword>]
	 * : If the child site is HTTP Basic Auth protected, enter the HTTP password here.
	 *
	 * [--force-ipv4]
	 * : Do you want to force IPv4 for this child site?.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mwp site add --hostname=example.com --admin=admin --unique-id=ZDGy03su
	 *     wp mwp site add --hostname=example.com --admin=admin
	 *
	 * @synopsis --hostname=<hostname> --admin=<admin>
	 */
	public function add( $__, $options )
	{
		$sites = \MainWP_DB::Instance()->getWebsitesByUrl( $options['url'] );
		if ( count( $sites ) > 0 ) {
			\WP_CLI::error( 'Site with that is already added' );
		}

		$data = $this->convertKeys($options, [
			'hostname' => 'url',
			'admin' => 'wpadmin',
			'unique-id' => 'unique_id',
			'ssl-version' => 'ssl_version',
			'http-username' => 'http_user',
			'http-password' => 'http_pass',
		]);

		$data['name'] = array_key_exists('name', $data) ? $data['name'] : $options['hostname'];
		$data['ssl_verify'] = !array_key_exists('ignore-ssl', $data);
		$data['force_use_ipv4'] = array_key_exists('force-ipv4', $data);
		$data['url'] = ( array_key_exists('no-https', $data) ? 'http://' : 'https://' ) . $data['url'];

		$site = apply_filters( 'mainwp_addsite', $data );

		if ( array_key_exists( 'error', $site ) ) {
			\WP_CLI::error( $site['error'] );
		} else if ( array_key_exists( 'response', $site ) && array_key_exists( 'siteid', $site ) ) {
			\WP_CLI::success( strip_tags($site['response']) );
			\WP_CLI::line( $site['siteid'] );
		} else {
			\WP_CLI::warning( 'Nothing happend' );
		}
	}

	/**
	 * Sync Data with Child Sites
	 *
	 * ## OPTIONS
	 *
	 * [<websiteid>]
	 * : The id (or ids, comma separated) of the child sites that need to be synced.
	 *
	 * [--all]
	 * : If set, all child sites will be synced.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mainwp sync 2,5
	 *     wp mainwp sync --all
	 *
	 * @synopsis [<websiteid>] [--all]
	 */
	public function sync( $args, $assoc_args )
	{
		// For this one the original just works logically
		$mainWpCommands = new \MainWP_WP_CLI_Command();
		$mainWpCommands->sync( $args, $assoc_args );
	}

	/**
	 * Reconnect with Child Sites
	 *
	 * ## OPTIONS
	 *
	 * [<websiteid>]
	 * : The id (or ids, comma separated) of the child sites that need to be reconnect.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mainwp reconnect 2,5
	 *
	 * @synopsis [<websiteid>]
	 */
	public function reconnect( $args, $assoc_args )
	{
		// For this one the original just works logically
		$mainWpCommands = new \MainWP_WP_CLI_Command();
		$mainWpCommands->reconnect( $args, $assoc_args );
	}

	/**
	 * Removes a child site
	 *
	 * <id>
	 * : Give either the child site's
	 *
	 * [--yes]
	 * : Confirms the deletion without confirmation
	 */
	public function remove( $args, $options )
	{
		list( $id ) = $args;

		// Check if the site exists
		$site = \MainWP_DB::Instance()->getWebsiteById( $id );
		if ( !$site ) {
			\WP_CLI::error( "Unable to find child site with ID \"$id\"" );
		}

		// Confirm & remove
		\WP_CLI::confirm( "Are you sure you want to remove child site \"$site->name\"?", $options );
		\MainWP_DB::Instance()->removeWebsite( $id );

		// Check if the site is removed
		$site = \MainWP_DB::Instance()->getWebsiteById( $id );
		if ( $site ) {
			\WP_CLI::error( "Failed to remove child site with ID \"$id\"" );
		} else {
			\WP_CLI::success( "Child site with ID \"$id\" is removed" );
		}
	}

	private function convertKeys($source, $map)
	{
		$target = [];

		foreach ($source as $key => $value ) {
			if ( array_key_exists( $key, $map ) ) {
				$target[ $map[$key] ] = $value;
			} else {
				$target[ $key ] = $value;
			}
		}

		return $target;
	}



}