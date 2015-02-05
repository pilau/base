<?php

/**
 * Configuration and functions
 *
 * As part of a parent theme, this functions.php file will be loaded AFTER the functions.php
 * file of the child theme. All function and constant definitions should be wrapped in
 * tests to see if they've already been defined (unless prefixed with pilau_base_*).
 * @link	http://codex.wordpress.org/Child_Themes#Using_functions.php
 *
 * However, note that when hooking functions that have equivalents in parent and child (e.g.
 * pilau_base_setup() and pilau_setup()), set the priority higher in the parent (e.g. 1) than
 * in the child (e.g. 10). Base stuff should be processed first, in case it contains stuff
 * the child depends on.
 *
 * @package	Pilau_Base
 * @since	0.2
 *
 */


/**
 * Constants
 *
 * Includes defaults for some constants that are intended to be configured
 * in the child theme, but are defaulted here in case the definitions are
 * removed from the child theme
 */

if ( ! defined( 'PILAU_USE_COMMENTS' ) ) {
	define( 'PILAU_USE_COMMENTS', false );
}
if ( ! defined( 'PILAU_USE_LINKS' ) ) {
	define( 'PILAU_USE_LINKS', false );
}
if ( ! defined( 'PILAU_USE_CATEGORIES' ) ) {
	define( 'PILAU_USE_CATEGORIES', false );
}
if ( ! defined( 'PILAU_HIDE_CATEGORIES' ) ) {
	define( 'PILAU_HIDE_CATEGORIES', true );
}
if ( ! defined( 'PILAU_USE_TAGS' ) ) {
	define( 'PILAU_USE_TAGS', false );
}
if ( ! defined( 'PILAU_HIDE_TAGS' ) ) {
	define( 'PILAU_HIDE_TAGS', true );
}
if ( ! defined( 'PILAU_IGNORE_UPDATES_FOR_INACTIVE_PLUGINS' ) ) {
	define( 'PILAU_IGNORE_UPDATES_FOR_INACTIVE_PLUGINS', true );
}
if ( ! defined( 'PILAU_USE_COOKIE_NOTICE' ) ) {
	define( 'PILAU_USE_COOKIE_NOTICE', false );
}
if ( ! defined( 'PILAU_SLUG_LENGTH' ) ) {
	define( 'PILAU_SLUG_LENGTH', 8 );
}
if ( ! defined( 'PILAU_USE_PICTUREFILL' ) ) {
	define( 'PILAU_USE_PICTUREFILL', false );
}

/**
 * Constants not intended for configuration
 *
 * These are defined in the child theme functions.php, because that is loaded first, even
 * though they are not intended to be changed. They may get used before this functions.php
 * is loaded - but they are still defined here as a fall-back.
 */

// Flag for requests from front, or AJAX - is_admin() returns true for AJAX
// because the AJAX script is in /wp-admin/
if ( ! defined( 'PILAU_FRONT_OR_AJAX' ) ) {
	define( 'PILAU_FRONT_OR_AJAX', ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) );
}
if ( ! defined( 'PILAU_REQUEST_PROTOCOL' ) ) {
	define( 'PILAU_REQUEST_PROTOCOL', isset( $_SERVER[ 'HTTPS' ] ) ? 'https' : 'http' );
}
if ( ! defined( 'PILAU_TOP_LEVEL_SLUG' ) ) {
	define( 'PILAU_TOP_LEVEL_SLUG', reset( explode( '/', trim( $_SERVER['REQUEST_URI'], '/' ) ) ) );
}
if ( ! defined( 'PILAU_PLACEHOLDER_GIF_URL' ) ) {
	define( 'PILAU_PLACEHOLDER_GIF_URL', get_template_directory_uri() . '/img/placeholder.gif' );
}


/**
 * Get loaded
 */
require( dirname( __FILE__ ) . '/inc/setup.php' );
require( dirname( __FILE__ ) . '/inc/security.php' );
require( dirname( __FILE__ ) . '/inc/lib.php' );
require( dirname( __FILE__ ) . '/inc/content.php');
require( dirname( __FILE__ ) . '/inc/media.php');
require( dirname( __FILE__ ) . '/inc/wp-toolbar.php' );
require( dirname( __FILE__ ) . '/inc/gravity-forms.php' );
if ( ! PILAU_FRONT_OR_AJAX ) {
	require( dirname( __FILE__ ) . '/inc/admin.php' );
}
