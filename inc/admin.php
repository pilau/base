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


if ( ! function_exists( 'pilau_slug_stopwords' ) ) {
	add_filter( 'name_save_pre', 'pilau_slug_stopwords', 0 );
	/**
	 * Remove stopwords from slugs
	 *
	 * Based on SEO Slugs plugin
	 *
	 * @since	Pilau_Base 2.1.3
	 * @param	string	$slug
	 * @param	string	$title
	 * @return	string
	 */
	function pilau_slug_stopwords( $slug = '', $title = '' ) {

		if ( empty( $slug ) ) {

			// Has a title been passed?
			if ( empty( $title ) ) {
				$title = $_POST['post_title'];
			}

			if ( ! empty( $title ) ) {

				// Standard sanitisation
				$slug = sanitize_title( $title );

				// Turn it to an array to strip stopwords
				$slug_array = explode( '-', $slug );
				$seo_slug_array = array_diff( $slug_array, pilau_get_stopwords() );

				// If there's nothing left, default to first word of original
				if ( empty( $seo_slug_array ) ) {
					$seo_slug_array = reset( $slug_array );
				}

				// Back to string
				$slug = implode( '-', $seo_slug_array );

			}

		}

		return $slug;
	}
}


if ( ! function_exists( 'pilau_get_stopwords' ) ) {
	/**
	 * Get stopwords
	 *
	 * Based on Yoast SEO plugin
	 *
	 * @since	Pilau_Base 2.1.3
	 * @return	array
	 */
	function pilau_get_stopwords() {
		return explode( ',', __( "a,about,above,after,again,against,all,am,an,and,any,are,as,at,be,because,been,before,being,below,between,both,but,by,could,did,do,does,doing,down,during,each,few,for,from,further,had,has,have,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,it,it's,its,itself,let's,me,more,most,my,myself,nor,of,on,once,only,or,other,ought,our,ours,ourselves,out,over,own,same,she,she'd,she'll,she's,should,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,we,we'd,we'll,we're,we've,were,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,would,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves", 'wordpress-seo' ) );
	}
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
