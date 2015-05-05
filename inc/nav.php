<?php

/**
 * Nav-related stuff
 *
 * @package	Pilau_Base
 * @since	0.2
 */


if ( ! function_exists( 'pilau_custom_menu' ) ) {
	/**
	 * Custom menu output - accessible and customisable
	 *
	 * @since	0.2
	 *
	 * @uses	wp_get_nav_menu_items()
	 *
	 * @param	string	$menu
	 * @param	array	$ul_class
	 * @param	bool	$strip_whitespace	Strip whitespace from between menu items?
	 * @return	string
	 */
	function pilau_custom_menu( $menu, $ul_classes = array(), $strip_whitespace = false ) {
		$output = '';

		// Filter to allow custom items to be inserted before
		$output = apply_filters( 'pilau_custom_menu_level1_before', $output, $menu );

		// Get first level items
		$items_level1 = wp_get_nav_menu_items( $menu );

		if ( $items_level1 ) {

			foreach ( $items_level1 as $item_level1 ) {

				// Init first level item
				$classes_level1 = array( 'menu-item' );
				if ( get_queried_object_id() == $item_level1->object_id ) {
					$classes_level1[] = 'current-menu-item';
				}

				// Open first level item
				$output .= '<li id="menu-item-' . $item_level1->object_id . '" class="' . implode( ' ', $classes_level1 ) . '">';

				// Close first level item
				$output .= '</li>';

			}

		}

		// Strip whitespace?
		if ( $strip_whitespace ) {
			$output = preg_replace( '/>\s+</', '><', $output );
		}

		// Filter to allow custom items to be inserted after
		$output = apply_filters( 'pilau_custom_menu_level1_after', $output, $menu );

		// Wrap in ul if there's anything
		if ( trim( $output ) ) {
			$output = '<ul class="' . implode( ' ', $ul_classes ) . '">' . $output . '</ul>';
		}

		return $output;
	}
}


if ( ! function_exists( 'pilau_menu_without_containers' ) ) {
	/**
	 * Get nav menu without markup containers
	 *
	 * @since	Pilau_Base 0.2
	 *
	 * @uses	wp_nav_menu()
	 *
	 * @param	string	$theme_location
	 * @param	integer	$depth
	 * @param	bool	$strip_whitespace	Strip whitespace from between menu items?
	 * @return	string
	 */
	function pilau_menu_without_containers( $theme_location, $depth = 1, $strip_whitespace = false, $walker = null ) {

		// Set up args
		$args = array(
			'theme_location'	=> $theme_location,
			'container'			=> '',
			'echo'				=> false,
			'depth'				=> $depth,
		);
		if ( is_object( $walker ) ) {
			$args['walker'] = $walker;
		}

		// Get menu items
		$menu_items = wp_nav_menu( $args );

		// Strip ul wrapper
		$menu_items = trim( $menu_items );
		$menu_items = preg_replace( '#<ul[^>]*>#i', '', $menu_items, 1 );
		$menu_items = substr( $menu_items, 0, -5 );

		// Strip whitespace?
		if ( $strip_whitespace ) {
			$menu_items = preg_replace( '/>\s+</', '><', $menu_items );
		}

		return $menu_items;
	}
}
