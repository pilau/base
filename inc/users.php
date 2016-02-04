<?php

/**
 * User-related functions
 *
 * @package	Pilau_Base
 * @since	2.2
 */


/**
 * Get all roles that can edit, create or publish posts or a certain type
 *
 * @link	http://themehybrid.com/weblog/correcting-the-author-meta-box-drop-down
 * @since	2.2.2
 *
 * @param	string 	$post_type
 * @return	array
 */
function pilau_get_roles_for_post_type( $post_type ) {
	global $wp_roles;

	$roles = array();
	$type  = get_post_type_object( $post_type );

	// Get the post type object caps.
	$caps = array( $type->cap->edit_posts, $type->cap->publish_posts, $type->cap->create_posts );
	$caps = array_unique( $caps );

	// Loop through the available roles.
	foreach ( $wp_roles->roles as $name => $role ) {

		foreach ( $caps as $cap ) {

			// If the role is granted the cap, add it.
			if ( isset( $role['capabilities'][ $cap ] ) && true === $role['capabilities'][ $cap ] ) {
				$roles[] = $name;
				break;
			}
		}
	}

	return $roles;
}


add_filter( 'editable_roles', 'pilau_editable_roles' );
/**
 * Filter the editable roles list
 *
 * @since	2.2
 * @uses	current_user_can()
 */
function pilau_editable_roles( $roles ) {

	// Only admins should be able to update_core, and only admins can edit admins
	if ( ! current_user_can( 'update_core' ) && array_key_exists( 'administrator', $roles ) ) {
		unset( $roles['administrator'] );
	}

	return $roles;
}


add_filter( 'user_has_cap', 'pilau_edit_user_cap_protect_admins', 10, 3 );
/**
 * Make sure non-admins can't edit admin accounts
 *
 * @since	2.2
 * @uses	WP_User
 * @uses	user_can()
 * @param	array	$allcaps	All the capabilities of the user
 * @param	array	$cap		[0] Required capability
 * @param	array	$args		[0] Requested capability
 *								[1] User ID
 *								[2] Associated object ID
 * @return	array
 */
function pilau_edit_user_cap_protect_admins( $allcaps, $cap, $args ) {

	// If the check is about editing or deleting users and the user is specified
	if ( in_array( $args[0], array( 'edit_user', 'delete_user' ) ) && ! empty( $args[2] ) ) {

		// Get the user
		$user = new WP_User( $args[2] );

		// If the user in question is an admin and the current user isn't...
		if ( user_can( $user, 'update_core' ) && ! user_can( $args[1], 'update_core' ) ) {
			$allcaps[ $args[0] . 's' ] = false;
		}

	}

	return $allcaps;
}


if ( ! function_exists( 'pilau_default_user_display_name' ) ) {
	add_action( 'user_register', 'pilau_default_user_display_name' );
	/**
	 * Better default display name for users
	 *
	 * @since	Pilau_Base 2.0
	 *
	 * @uses	get_user_meta()
	 * @uses	wp_update_user()
	 */
	function pilau_default_user_display_name( $user_id ) {
		// Fetch current user meta information
		$first = get_user_meta( $user_id, 'first_name', true );
		$last = get_user_meta( $user_id, 'last_name', true );
		$display = trim( $first . " " . $last );
		// Update
		wp_update_user( array( "ID" => $user_id, "display_name" => $display ) );
	}
}


if ( ! function_exists( 'pilau_get_users_by_capability' ) ) {
	/**
	 * Get users by capability
	 *
	 * @since	Pilau_Base 2.2
	 *
	 * @uses	get_users()
	 * @return	array
	 */
	function pilau_get_users_by_capability( $cap ) {
		static $all_users = null;
		$users_with_cap = array();

		// Only get all users once per request!
		if ( $all_users === null ) {
			$all_users = get_users();
		}

		// Filter by capability
		foreach( $all_users as $user ) {
			if ( $user->has_cap( $cap ) ) {
				$users_with_cap[] = $user;
			}
		}

		return $users_with_cap;
	}
}


if ( ! function_exists( 'pilau_get_user_role' ) ) {
	/**
	 * Get a WordPress user's role
	 *
	 * @since 1.0
	 *
	 * @uses	$wpdb
	 * @uses	maybe_unserialize()
	 * @uses	WP_User
	 *
	 * @param	int|object	$user	Either a user's ID or a user object
	 * @param	bool		$manual	Optional. If true, a "manual" check is done that avoids using WP functions; use this if the code calling this function is hooked to something that may be called by WP_User, creating an infinite loop
	 * @return	string|null			The user's role if the operation was successful, otherwise null
	 */
	function pilau_get_user_role( $user, $manual = false ) {
		global $wpdb;
		$role = null;
		if ( is_int( $user ) || ctype_digit( $user ) ) {
			if ( $manual ) {
				// Manual check
				global $wpdb;
				$caps = $wpdb->get_var( $wpdb->prepare("
				SELECT	meta_value
				FROM	$wpdb->usermeta
				WHERE	user_id		= %d
				AND		meta_key	= %s
			", intval( $user ), $wpdb->prefix . "capabilities" ) );
				if ( $caps ) {
					$user = new StdClass;
					$user->roles = array_keys( maybe_unserialize( $caps ) );
				}
			} else {
				// Standard WP User
				$user = new WP_User( $user );
			}
		}
		if ( is_object( $user ) ) {
			$caps_field = $wpdb->prefix . 'capabilities';
			if ( property_exists( $user, 'roles' ) && is_array( $user->roles ) && ! empty( $user->roles ) ) {
				$role = $user->roles[0];
			} else if ( property_exists( $user, $caps_field ) && is_array( $user->$caps_field ) && ! empty( $user->$caps_field ) ) {
				$role = array_shift( array_keys( $user->$caps_field ) );
			}
		}
		return $role;
	}
}


if ( ! function_exists( 'pilau_get_user_with_meta' ) ) {
	/**
	 * Get a user with metadata
	 *
	 * Currently doesn't work with meta fields that have multiple values -
	 * only the first is returned.
	 *
	 * @since 1.0
	 *
	 * @uses	get_userdata()
	 * @uses	get_user_meta()
	 * @uses	maybe_unserialize()
	 *
	 * @param	int		$id	The user's ID
	 * @return	object
	 */
	function pilau_get_user_with_meta( $id ) {
		$user = get_userdata( $id );
		if ( $user ) {
			$user = $user->data;
			$user_meta = get_user_meta( $id );
			foreach ( $user_meta as $user_meta_key => $user_meta_value ) {
				$user->{$user_meta_key} = maybe_unserialize( $user_meta_value[0] );
			}
		}
		return $user;
	}
}
