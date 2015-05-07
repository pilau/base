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
