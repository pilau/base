<?php

/**
 * User-related stuff
 *
 * @package	Pilau_Base
 * @since	0.2
 */


if ( ! function_exists( 'pilau_default_user_display_name' ) ) {
	add_action( 'user_register', 'pilau_default_user_display_name' );
	/**
	 * Better default display name for users
	 *
	 * @since	Pilau_Base 0.2
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


if ( ! function_exists( 'pilau_get_user_role' ) ) {
	/**
	 * Get a WordPress user's role
	 *
	 * @since 0.1
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
	 * @since 0.1
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

