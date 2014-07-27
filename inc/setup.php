<?php

/**
 * Initial theme setup
 *
 * @package	Pilau_Base
 * @since	0.1
 */


/**
 * Set up theme
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'after_setup_theme', 'pilau_base_setup', 1 );
function pilau_base_setup() {
	global $pilau_base_options, $pilau_breakpoints;

	/*
	 * Theme options (not settings page)
	 */
	$pilau_base_options = get_option( 'pilau_base_options', array() );
	if ( ! is_array( $pilau_base_options ) || empty( $pilau_base_options ) ) {

		// First time theme has been activated
		$pilau_base_options = array(
			'plugins_installer_run'		=> false,
			'plugins_nag_dismissed'		=> false,
			'settings_script_run'		=> false,
			'settings_nag_dismissed'	=> false
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


/**
 * Manage scripts for the front-end
 *
 * Always use the $ver parameter when registering or enqueuing styles or scripts, and
 * update it when deploying a new version - this helps prevent browser caching issues.
 * (Actually this is made redundant by using Better WordPress Minify, with its
 * appended parameter - but this is a good habit to get into ;-)
 *
 * The Modernizr script has to be included in the header, so in case pilau_scripts_to_footer()
 * is used to move scripts to the footer, Modernizr is hard-coded into header.php
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'wp_enqueue_scripts', 'pilau_base_enqueue_scripts', 1 );
function pilau_base_enqueue_scripts() {
	// This test is done here because applying the test to the hook breaks due to pilau_is_login_page() not being defined yet...
	if ( ! is_admin() && ! pilau_is_login_page() ) {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'pilau-base', get_template_directory_uri() . '/js/pilau-base.js', array( 'jquery' ), '1.0' );
		if ( PILAU_USE_PICTUREFILL ) {
			wp_enqueue_script( 'picturefill', get_template_directory_uri() . '/js/picturefill.js', array(), '2.1.0' );
		}

	}
}


/**
 * Manage styles for the front-end
 *
 * Always use the $ver parameter when registering or enqueuing styles or scripts, and
 * update it when deploying a new version - this helps prevent browser caching issues.
 * (Actually this is made redundant by using Better WordPress Minify, with its
 * appended parameter - but this is a good habit to get into ;-)
 *
 * @since	Pilau_Base 0.1
 */
add_action( 'wp_enqueue_scripts', 'pilau_base_enqueue_styles', 1 );
function pilau_base_enqueue_styles() {
	// This test is done here because applying the test to the hook breaks due to pilau_is_login_page() not being defined yet...
	if ( ! is_admin() && ! pilau_is_login_page() ) {

		wp_enqueue_style( 'html5-reset', get_template_directory_uri() . '/styles/html5-reset.css', array(), '1.0' );
		wp_enqueue_style( 'wp-core', get_template_directory_uri() . '/styles/wp-core.css', array(), '1.0' );
		wp_enqueue_style( 'pilau-classes', get_template_directory_uri() . '/styles/classes.css', array(), '1.0' );

	}
}


/*
 * Tidy up core WP stuff
 */

/**
 * Remove unnecessary title attributes from page list links
 *
 * @since	Pilau_Base 0.1
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
 * @since	Pilau_Base 0.1
 */
if ( ! function_exists( 'pilau_nav_menu_css_classes' ) ) {
	add_filter( 'nav_menu_item_id', '__return_empty_array', 10000 );
	add_filter( 'nav_menu_css_class', 'pilau_nav_menu_css_classes', 10000, 3 );
	function pilau_nav_menu_css_classes( $classes, $item, $args ) {
		$new_classes = array();
		foreach ( $classes as $class ) {
			// We're only keeping classes that indicate location - all others seem redundant
			if ( ! ( strlen( $class ) > 8 && substr( $class, 0, 9 ) == 'menu-item' ) && ( strpos( $class, 'page' ) === false || strpos( $class, 'ancestor' ) !== false ) )
				$new_classes[] = $class;
		}
		return $new_classes;
	}
}

/**
 * Blank default nav menu
 *
 * @link	http://www.rlmseo.com/blog/cutom-navigation-menus-in-wordpress-3-0/
 * @since	Pilau_Base 0.1
 */
if ( ! function_exists( 'default_nav_menu' ) ) {
	function default_nav_menu() { return ''; }
}
