<?php

/**
 * Nav-related stuff
 *
 * @package	Pilau_Base
 * @since	0.2
 */


if ( ! function_exists( 'default_nav_menu' ) ) {
	/**
	 * Blank default nav menu
	 *
	 * @link	http://www.rlmseo.com/blog/cutom-navigation-menus-in-wordpress-3-0/
	 * @since	Pilau_Base 0.2
	 */
	function default_nav_menu() { return ''; }
}


if ( ! function_exists( 'pilau_menu_without_containers' ) ) {
	/**
	 * Get nav menu without markup containers
	 *
	 * @since	0.2
	 *
	 * @uses	wp_nav_menu()
	 *
	 * @param	string	$theme_location
	 * @param	integer	$depth
	 * @param	bool	$strip_whitespace	Strip whitespace from between menu items?
	 * @param	mixed	$walker				false (no custom walker) | [custom walker class instance] | null (default custom walker)
	 * @return	string
	 */
	function pilau_menu_without_containers( $theme_location, $depth = 1, $strip_whitespace = false, $walker = null ) {

		// Set up args
		$args = array(
			'theme_location'	=> $theme_location,
			'container'			=> false,
			'echo'				=> false,
			'depth'				=> $depth,
		);
		if ( is_null( $walker ) ) {
			$args['walker'] = new Pilau_Walker_Nav_Menu;
		} else if ( is_object( $walker ) ) {
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


/**
 * Essential improvements to nav menu output
 *
 * @since	0.2
 * @uses	Walker_Nav_Menu
 */
class Pilau_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 0.2
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		// Indent
		$indent = str_repeat("\t", $depth);

		// For keyboard accessibility
		$accessibility_attributes = '';
		// Seems to be the only way of getting ID of parent...
		if ( preg_match_all( '/ id="menu-item-([0-9]+)"/', $output, $matches, PREG_PATTERN_ORDER ) !== false ) {
			$parent_id = end( $matches[1] );
			$accessibility_attributes = ' id="sub-menu-for-' . $parent_id . '" role="group" aria-expanded="false" aria-labelledby="menu-item-' . $parent_id . '"';
		}

		// Add to output
		$output .= "\n$indent<ul class=\"sub-menu\"" . $accessibility_attributes . ">\n";

	}

	/**
	 * Start the element output.
	 *
	 * @since	0.2
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		//$classes[] = 'menu-item-' . $item->ID;

		// Strip out some unnecessary classes
		$new_classes = array();
		foreach ( $classes as $key => $class ) {
			if (
				in_array( $class, array( 'menu-item', 'menu-item-has-children' ) ) ||
				(
					! ( strlen( $class ) > 8 && substr( $class, 0, 9 ) == 'menu-item' ) &&
					( strpos( $class, 'page' ) === false || strpos( $class, 'ancestor' ) !== false )
				)
			) {
				$new_classes[] = $class;
			}
		}
		$classes = $new_classes;

		// Add menu level class
		$classes[] = 'menu-level-' . $depth;

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since	0.2
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since	0.2
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		/**
		 * ARIA attributes for keyboard accessibility
		 *
		 * @since	0.2
		 */
		$aria_attributes = array();
		if ( $depth == 0 && strpos( $class_names, 'menu-item-has-children' ) !== false ) {
			$aria_attributes['haspopup'] = 'true';
			$aria_attributes['owns'] = $aria_attributes['controls'] = 'sub-menu-for-' . $item->ID;
			$aria_attributes['expanded'] = 'false';
		}

		$output .= $indent . '<li' . $id . $class_names;
		if ( ! empty( $aria_attributes ) ) {
			foreach ( $aria_attributes as $attribute => $value ) {
				$output .= ' aria-' . $attribute . '="' . $value . '"';
			}
		}
		$output .= '>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 0.2
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * @since 0.2
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}
