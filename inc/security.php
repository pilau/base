<?php

/**
 * Security functions
 *
 * @package	Pilau_Base
 * @since	2.0
 */


if ( ! function_exists( 'pilau_check_referrer' ) ) {
	add_action( 'check_comment_flood', 'pilau_check_referrer' );
	/**
	 * Block attempted comments without a referrer
	 *
	 * @since	Pilau_Base 2.0
	 * @uses	wp_die()
	 */
	function pilau_check_referrer() {
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) || $_SERVER['HTTP_REFERER'] == "" ) {
			wp_die( __( 'Please enable referrers in your browser.' ) );
		}
	}
}


if ( ! function_exists( 'pilau_block_malicious_requests' ) ) {
	add_action( 'init', 'pilau_block_malicious_requests' );
	/**
	 * Block malicious requests
	 *
	 * @since	Pilau_Base 2.0
	 * @link	http://perishablepress.com/press/2009/12/22/protect-wordpress-against-malicious-url-requests/
	 * @uses	is_user_logged_in()
	 */
	function pilau_block_malicious_requests() {
		if (	( strlen( $_SERVER['REQUEST_URI'] ) > 255 && ! is_user_logged_in() ) ||
				strpos( $_SERVER['REQUEST_URI'], "eval(" ) ||
				strpos( $_SERVER['REQUEST_URI'], "base64" ) ) {
			@header( "HTTP/1.1 414 Request-URI Too Long" );
			@header( "Status: 414 Request-URI Too Long" );
			@header( "Connection: Close" );
			@exit;
		}
	}
}


if ( ! function_exists( 'pilau_rss_version' ) ) {
	/**
	 * Remove WP version from RSS
	 *
	 * @since	Pilau_Base 2.0
	 */
	add_filter( 'the_generator', '__return_empty_string' );
}


