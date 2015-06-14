<?php

/**
 * Gravity Forms
 *
 * @package	Pilau_Base
 * @since	2.0
 */


/**
 * Return a field from a form object, based on a field meta value
 *
 * @since	2.0
 * @param	array	$form
 * @param	string	$field_meta_key
 * @param	string	$field_meta_value
 * @param	string	$checked_nested		Check this nested array of fields, e.g. 'inputs'
 * @return	mixed						If $checked_nested, returns an array in the format:
 * 										array( $field, [key of nested field] )
 */
function pilau_gf_get_field( $form, $field_meta_key, $field_meta_value, $checked_nested = null ) {
	$the_field = false;
	$got_it = false;

	// Try to find field
	if ( isset( $form['fields'] ) && is_array( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			foreach ( $field as $key => $value ) {
				if ( $key == $checked_nested && is_array( $value ) ) {

					// Go through nested fields
					foreach ( $value as $nested_key => $nested_value ) {
						if ( array_key_exists( $field_meta_key, $nested_value ) && $nested_value[ $field_meta_key ] == $field_meta_value ) {
							$the_field = array( $field, $nested_key );
							$got_it = true;
						}
					}
					if ( $got_it ) {
						break;
					}

				} else if ( $key == $field_meta_key && $field_meta_value == $value ) {

					// Got it
					$the_field = $field;
					$got_it = true;
					break;

				}
			}
			if ( $got_it ) {
				break;
			}
		}
	}

	return $the_field;
}


/**
 * Return a value from a submitted form / entry combination
 *
 * @since	2.0
 * @param	array	$form
 * @param	array	$entry
 * @param	string	$label				Label of field
 * @param	string	$checked_nested		Key of nested fields to check, e.g. 'inputs'
 * @return	mixed
 */
function pilau_gf_get_value( $form, $entry, $label, $checked_nested = null ) {
	$the_value = null;

	// Get the field
	$field = pilau_gf_get_field( $form, 'label', $label, $checked_nested );
	if ( $field ) {
		if ( $checked_nested ) {
			$id = $field[0]->{$checked_nested}[ $field[1] ]['id'];
			if ( ! empty( $id ) ) {
				$the_value = $entry[ "$id" ];
			}
		} else {
			$the_value = $entry[ $field->id ];
		}
	}

	return $the_value;
}