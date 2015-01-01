<?php

/**
 * Admin interface customization
 *
 * @package	Pilau_Base
 * @since	0.2
 * @todo	Selectively remove post category / link category options on options-writing.php
 */


/**
 * Admin scripts and styles
 *
 * @since	Pilau_Base 0.2
 */
add_action( 'admin_enqueue_scripts', 'pilau_base_admin_enqueue_scripts_styles', 1 );
function pilau_base_admin_enqueue_scripts_styles() {

	wp_enqueue_style( 'pilau-base-admin-css', get_template_directory_uri() . '/styles/wp-admin.css', array(), '1.0' );
	wp_enqueue_script( 'pilau-base-admin-js', get_template_directory_uri() . '/js/wp-admin.js', array(), '1.0' );

}


/**
 * Admin notices
 *
 * @since	Pilau_Base 0.2
 */
add_action( 'admin_notices', 'pilau_base_admin_notices', 1 );
function pilau_base_admin_notices() {
	global $pilau_base_options;

	// Theme activation
	if ( ! $pilau_base_options['settings_script_run'] ) {

	}

}


/**
 * Admin menus
 *
 * @since	Pilau_Base 0.2
 */
add_action( 'admin_menu', 'pilau_base_admin_menus', 1 );
function pilau_base_admin_menus() {

	/* Register new menus
	***************************************************************************/

}


/**
 * Remove meta boxes
 *
 * @since	Pilau_Base 0.2
 */
add_action( 'add_meta_boxes', 'pilau_base_remove_meta_boxes', 1 );
function pilau_base_remove_meta_boxes() {

	/* Comments */
	if ( ! PILAU_USE_COMMENTS ) {
		remove_meta_box( 'commentsdiv', 'post', 'normal' );
		remove_meta_box( 'commentsdiv', 'page', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
	}

}


/**
 * Customize list columns
 *
 * For the most part these should be handled by the Codepress Admin Columns plugin.
 * Include any necessary overrides here.
 *
 * @since	Pilau_Base 0.2
 */
add_action( 'admin_init', 'pilau_base_customize_list_columns' );
function pilau_base_customize_list_columns() {
	add_filter( 'manage_edit-post_columns', 'pilau_base_admin_columns', 10000, 1 );
	add_filter( 'manage_edit-page_columns', 'pilau_base_admin_columns', 10000, 1 );
	foreach ( get_post_types( array( 'public' => true ), 'names' ) as $pt ) {
		add_filter( 'manage_' . $pt . '_posts_columns', 'pilau_admin_columns', 10000, 1 );
	}
}

/**
 * Global handler for all post type columns
 *
 * @since	Pilau_Base 0.2
 *
 * @param	array $cols
 * @return	array
 */
function pilau_base_admin_columns( $cols ) {

	// Override core stuff
	if ( ! PILAU_USE_CATEGORIES && isset( $cols['categories'] ) ) {
		unset( $cols['categories'] );
	}
	if ( ! PILAU_USE_TAGS && isset( $cols['tags'] ) ) {
		unset( $cols['tags'] );
	}
	if ( ! PILAU_USE_COMMENTS && isset( $cols['comments'] ) ) {
		unset( $cols['comments'] );
	}

	return $cols;
}


/**
 * Disable default dashboard widgets
 *
 * @since	Pilau_Base 0.2
 * @link	http://codex.wordpress.org/Dashboard_Widgets_API
 */
add_action( 'wp_dashboard_setup', 'pilau_base_disable_default_dashboard_widgets', 1 );
function pilau_base_disable_default_dashboard_widgets() {

	if ( ! PILAU_USE_COMMENTS ) {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}

}


/* Functions to help building admin screens
-------------------------------------------------------------------------------------------*/

/**
 * Output admin tabs
 *
 * Gets the current tab from $_GET['tab']; defaults to the first tab supplied
 *
 * @since	Pilau_Base 0.2
 * @param	array	$tabs		In the format:
 * 								<code>array( 'slug1' => 'Title 1', 'slug2' => 'Title 2' )</code>
 * @param	string	$base_url	Base URL for admin screen
 */
function pilau_admin_tabs( $tabs, $base_url ) {
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $slug => $title ) {
		$classes = array( 'nav-tab' );
		if ( $slug == pilau_current_admin_tab( $tabs ) ) {
			$classes[] = ' nav-tab-active';
		}
		echo '<a class="' . implode( " ", $classes ) . '" href="' . $base_url . '&amp;tab=' . $slug . '">' . $title . '</a>';
	}
	echo '</h2>';
}

/**
 * Get the current tab in an admin screen
 *
 * @since	Pilau_Base 0.2
 * @param	array	$tabs	In the format:
 * 							<code>array( 'slug1' => 'Title 1', 'slug2' => 'Title 2' )</code>
 * @return	string
 */
function pilau_current_admin_tab( $tabs ) {
	return isset( $_GET['tab'] ) && $_GET['tab'] ? $_GET['tab'] : key( $tabs );
}