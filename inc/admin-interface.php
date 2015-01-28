<?php

/**
 * Admin interface customization
 *
 * @package	Pilau_Base
 * @since	0.2
 * @todo	Selectively remove post category / link category options on options-writing.php
 */


add_action( 'add_meta_boxes', 'pilau_base_remove_meta_boxes', 1 );
/**
 * Remove meta boxes
 *
 * @since	Pilau_Base 0.2
 */
function pilau_base_remove_meta_boxes() {

	/* Comments */
	if ( ! PILAU_USE_COMMENTS ) {
		remove_meta_box( 'commentsdiv', 'post', 'normal' );
		remove_meta_box( 'commentsdiv', 'page', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
	}

}


add_action( 'admin_init', 'pilau_base_customize_list_columns' );
/**
 * Customize list columns
 *
 * For the most part these should be handled by the Codepress Admin Columns plugin.
 * Include any necessary overrides here.
 *
 * @since	Pilau_Base 0.2
 */
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
	if ( ( ! PILAU_USE_CATEGORIES || PILAU_HIDE_CATEGORIES ) && isset( $cols['categories'] ) ) {
		unset( $cols['categories'] );
	}
	if ( ( ! PILAU_USE_TAGS || PILAU_HIDE_TAGS ) && isset( $cols['tags'] ) ) {
		unset( $cols['tags'] );
	}
	if ( ! PILAU_USE_COMMENTS && isset( $cols['comments'] ) ) {
		unset( $cols['comments'] );
	}

	return $cols;
}


add_action( 'wp_dashboard_setup', 'pilau_base_disable_default_dashboard_widgets', 1 );
/**
 * Disable default dashboard widgets
 *
 * @since	Pilau_Base 0.2
 * @link	http://codex.wordpress.org/Dashboard_Widgets_API
 */
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