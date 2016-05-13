<?php

add_action( 'admin_bar_menu', 'pilau_base_customize_toolbar', 10000 );
/**
 * WordPress Toolbar customization (formerly admin bar)
 *
 * @package	Pilau_Base
 * @since	2.0
 * @link	http://www.sitepoint.com/change-wordpress-33-toolbar/
 */
function pilau_base_customize_toolbar( $toolbar ) {

	/* Remove comments? */
	if ( ! PILAU_USE_COMMENTS ) {
		$toolbar->remove_node( 'comments' );
	}

	/* Generic refreshing of any data cached by theme */
	if ( current_user_can( 'edit_posts' ) ) {
		$toolbar->add_node(array(
			'id'		=> 'refresh',
			'title'		=> 'Refresh',
			'href'		=> add_query_arg( 'refresh', 1 ),
		));
	}

}
