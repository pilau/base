<?php

/**
 * Initial theme setup
 *
 * @package	Pilau_Base
 * @since	0.2
 */


/**
 * Set up theme
 *
 * @since	Pilau_Base 0.2
 */
add_action( 'after_setup_theme', 'pilau_base_setup', 1 );
function pilau_base_setup() {
	global $pilau_base_options, $pilau_breakpoints;

	/*
	 * Theme options
	 */
	$pilau_base_options = get_option( 'pilau_base_options', array() );
	if ( ! is_array( $pilau_base_options ) || empty( $pilau_base_options ) ) {

		// First time theme has been activated
		$pilau_base_options = array(
		);
		update_option( 'pilau_base_options', $pilau_base_options );

	}

	/*
	 * Responsive stuff
	 */

	// These breakpoints are currently used for responsive image sizes with Picturefill
	if ( ! is_array( $pilau_breakpoints ) ) {

		$pilau_breakpoints = array(
			'large'		=> '1000px', // This and above is "large"
			'medium'	=> '640px', // This and above is "medium"; below is "small"
		);

	}

	/*
	 * Refresh
	 *
	 * TODO:	Try to integrate with WP Super Cache to delete cache
	 */
	/*
	if ( PILAU_FRONT_OR_AJAX && isset( $_GET['refresh'] ) ) {

	}
	*/

}


/*
 * Tidy up core WP stuff
 */

/**
 * Remove unnecessary title attributes from page list links
 *
 * @since	Pilau_Base 0.2
 */
if ( ! function_exists( 'pilau_remove_title_attributes' ) ) {
	add_filter( 'wp_list_pages', 'pilau_remove_title_attributes' );
	function pilau_remove_title_attributes( $input ) {
		return preg_replace( '/\s*title\s*=\s*(["\']).*?\1/', '', $input );
	}
}

/**
 * Remove unnecessary attributes from nav menu items
 * Note that this will remove any custom classes added in
 * the "CSS Classes (optional) field in nav menus
 * if they start with "menu-item"
 *
 * @link	http://codex.wordpress.org/Function_Reference/wp_nav_menu#Menu_Item_CSS_Classes
 * @since	Pilau_Base 0.2
 */
if ( ! function_exists( 'pilau_nav_menu_css_classes' ) ) {
	add_filter( 'nav_menu_item_id', '__return_empty_array', 10000 );
	add_filter( 'nav_menu_css_class', 'pilau_nav_menu_css_classes', 10000, 3 );
	function pilau_nav_menu_css_classes( $classes, $item, $args ) {
		$new_classes = array();
		foreach ( $classes as $class ) {
			// We're only keeping classes that indicate location - all others seem redundant
			if ( ! ( strlen( $class ) > 8 && substr( $class, 0, 9 ) == 'menu-item' ) && ( strpos( $class, 'page' ) === false || strpos( $class, 'ancestor' ) !== false ) ) {
				$new_classes[] = $class;
			}
		}
		return $new_classes;
	}
}

/**
 * Blank default nav menu
 *
 * @link	http://www.rlmseo.com/blog/cutom-navigation-menus-in-wordpress-3-0/
 * @since	Pilau_Base 0.2
 */
if ( ! function_exists( 'default_nav_menu' ) ) {
	function default_nav_menu() { return ''; }
}
