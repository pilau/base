<?php

/**
 * General admin stuff
 *
 * @package	Pilau_Base
 * @since	2.0
 */


/* Any admin-specific includes */

/**
 * Admin interface customization
 *
 * @since	Pilau_Base 2.0
 */
require( dirname( __FILE__ ) . '/admin-interface.php' );


add_action( 'admin_init', 'pilau_base_admin_init', 1 );
/**
 * Admin initialization
 *
 * @since	Pilau_Base 2.0
 */
function pilau_base_admin_init() {

}


if ( ! function_exists( 'pilau_slug_length' ) ) {
	add_filter( 'name_save_pre', 'pilau_slug_length', 10 );
	/**
	 * Limit length of slugs
	 *
	 * @since	Pilau_Base 2.0
	 */
	function pilau_slug_length( $slug ) {
		$maxwords = PILAU_SLUG_LENGTH;
		$slug_array = explode( "-", $slug );
		if ( count( $slug_array ) > $maxwords ) {
			$slug_array = array_slice( $slug_array, 0, $maxwords );
		}
		return implode( "-", $slug_array );
	}
}


if ( PILAU_IGNORE_UPDATES_FOR_INACTIVE_PLUGINS ) {
	add_filter('transient_update_plugins', 'update_active_plugins');
}
/**
 * Ignore updates for inactive plugins
 *
 * @since	Pilau_Base 2.0
 * @link	http://bloke.org/wordpress/remove-plugin-update-notice-only-for-inactive-plugins/
 */
function pilau_update_active_plugins( $value = '' ) {

	if ( ( isset( $value->response ) ) && ( count( $value->response ) ) ) {

		// Get the list cut current active plugins
		$active_plugins = get_option( 'active_plugins' );
		if ( $active_plugins ) {

			//  Here we start to compare the $value->response
			//  items checking each against the active plugins list.
			foreach ( $value->response as $plugin_idx => $plugin_item ) {

				// If the response item is not an active plugin then remove it.
				// This will prevent WordPress from indicating the plugin needs update actions.
				if ( ! in_array( $plugin_idx, $active_plugins ) ) {
					unset( $value->response[ $plugin_idx ] );
				}
			}

		} else {

			// If no active plugins then ignore the inactive out of date ones.
			foreach( $value->response as $plugin_idx => $plugin_item ) {
				unset( $value->response );
			}

		}

	}

	return $value;
}


if ( defined( 'DW_VERSION' ) ) {
	add_filter( 'pre_get_posts', 'pilau_dynwid_cpt_fix' );
	/**
	 * A workaround to fix the Dynamic Widgets lists of CPTs
	 *
	 * @since	Pilau_Base 2.0
	 */
	function pilau_dynwid_cpt_fix( $query ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'dynwid-config' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
			$query->set( 'posts_per_page', -1 );
		}
		return $query;
	}
}
