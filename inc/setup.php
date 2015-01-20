<?php

/**
 * Initial theme setup
 *
 * @package	Pilau_Base
 * @since	0.2
 */


add_action( 'after_setup_theme', 'pilau_base_setup', 1 );
/**
 * Set up theme
 *
 * @since	Pilau_Base 0.2
 */
function pilau_base_setup() {
	global $post;

	/*
	 * Refresh
	 */
	if ( PILAU_FRONT_OR_AJAX && isset( $_GET['refresh'] ) ) {

		// Do WP Super Cache clear?
		if ( function_exists( 'wp_super_cache_text_domain' ) && is_object( $post ) ) {
			wp_cache_post_change( $post->ID );
		}

	}

}


/*
 * Tidy up core WP stuff
 */

if ( ! function_exists( 'pilau_remove_title_attributes' ) ) {
	add_filter( 'wp_list_pages', 'pilau_remove_title_attributes' );
	/**
	 * Remove unnecessary title attributes from page list links
	 *
	 * @since	Pilau_Base 0.2
	 */
	function pilau_remove_title_attributes( $input ) {
		return preg_replace( '/\s*title\s*=\s*(["\']).*?\1/', '', $input );
	}
}


if ( ! function_exists( 'pilau_nav_menu_css_classes' ) ) {
	add_filter( 'nav_menu_item_id', '__return_empty_array', 10000 );
	add_filter( 'nav_menu_css_class', 'pilau_nav_menu_css_classes', 10000, 3 );
	/**
	 * Remove unnecessary attributes from nav menu items
	 * Note that this will remove any custom classes added in
	 * the "CSS Classes (optional) field in nav menus
	 * if they start with "menu-item"
	 *
	 * @link	http://codex.wordpress.org/Function_Reference/wp_nav_menu#Menu_Item_CSS_Classes
	 * @since	Pilau_Base 0.2
	 */
	function pilau_nav_menu_css_classes( $classes, $item, $args ) {
		$new_classes = array();
		foreach ( $classes as $class ) {
			// We're only keeping classes that indicate location, plus standard class
			if ( $class == 'menu-item' || ( ! ( strlen( $class ) > 8 && substr( $class, 0, 9 ) == 'menu-item' ) && ( strpos( $class, 'page' ) === false || strpos( $class, 'ancestor' ) !== false ) ) ) {
				$new_classes[] = $class;
			}
		}
		return $new_classes;
	}
}


if ( ! function_exists( 'default_nav_menu' ) ) {
	/**
	 * Blank default nav menu
	 *
	 * @link	http://www.rlmseo.com/blog/cutom-navigation-menus-in-wordpress-3-0/
	 * @since	Pilau_Base 0.2
	 */
	function default_nav_menu() { return ''; }
}
