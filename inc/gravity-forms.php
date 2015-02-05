<?php

/**
 * Gravity Forms
 *
 * @package	Pilau_Base
 * @since	0.2
 */


/**
 * Return a field from a form object, based on a field meta value
 *
 * @since	0.2
 * @param	array	$form
 * @return	mixed
 */
function pilau_gf_get_field( $form, $field_meta_key, $field_meta_value ) {
	$the_field = false;

	// Try to find field
	if ( isset( $form['fields'] ) && is_array( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			foreach ( $field as $key => $value ) {
				if ( $key == $field_meta_key && $field_meta_value == $value ) {
					$the_field = $field;
					break;
				}
			}
		}
	}

	return $the_field;
}