<?php

/**
 * General admin stuff
 *
 * @package	Pilau_Base
 * @since	0.1
 */


/* Any admin-specific includes */

/**
 * Admin interface customization
 *
 * @since	Pilau_Base 0.1
 */
require( dirname( __FILE__ ) . '/admin-interface.php' );

/**
 * Pilau plugins management
 *
 * @since	Pilau_Base 0.1
 */
if ( PILAU_USE_PLUGINS_PAGE ) {
	require( dirname( __FILE__ ) . '/plugins-infos.php' );
}


/**
 * Admin initialization
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'admin_init', 'pilau_base_admin_init', 1 );
function pilau_base_admin_init() {
	global $pilau_wp_plugins;

	/**
	 * Installed plugins data
	 *
	 * @since	Pilau_Base 0.1
	 * @global	array
	 */
	if ( PILAU_USE_PLUGINS_PAGE )
		$pilau_wp_plugins = get_plugins();

}


/**
 * Limit length of slugs
 *
 * @since	Pilau_Base 0.1
 */
if ( ! function_exists( 'pilau_slug_length' ) ) {
	add_filter( 'name_save_pre', 'pilau_slug_length', 10 );
	function pilau_slug_length( $slug ) {
		$maxwords = PILAU_SLUG_LENGTH;
		$slug_array = explode( "-", $slug );
		if ( count( $slug_array ) > $maxwords )
			$slug_array = array_slice( $slug_array, 0, $maxwords );
		return implode( "-", $slug_array );
	}
}


/**
 * Ignore updates for inactive plugins
 *
 * @since	Pilau_Base 0.1
 * @link	http://wordpress.org/extend/plugins/update-active-plugins-only/
 */
if ( PILAU_IGNORE_UPDATES_FOR_INACTIVE_PLUGINS ) {
	add_filter( 'http_request_args', 'pilau_ignore_updates_for_inactive_plugins', 10, 2 );
	function pilau_ignore_updates_for_inactive_plugins( $r, $url ) {
		if ( 0 === strpos( $url, 'http://api.wordpress.org/plugins/update-check/' ) ) {
			$plugins = unserialize( $r['body']['plugins'] );
			$plugins->plugins = array_intersect_key( $plugins->plugins, array_flip( $plugins->active ) );
			$r['body']['plugins'] = serialize( $plugins );
		}
		return $r;
	}
}


/**
 * A workaround to fix the Dynamic Widgets lists of CPTs
 *
 * @since	Pilau_Base 0.1
 */
if ( defined( 'DW_VERSION' ) ) {
	add_filter( 'pre_get_posts', 'pilau_dynwid_cpt_fix' );
	function pilau_dynwid_cpt_fix( $query ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'dynwid-config' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
			$query->set( 'posts_per_page', -1 );
		}
		return $query;
	}
}
