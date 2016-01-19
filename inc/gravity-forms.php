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
 * @param	string	$checked_nested		Key of nested fields to check, e.g. 'inputs'. Deprecated in
 * 										favour of checking by type
 * @return	mixed
 */
function pilau_gf_get_value( $form, $entry, $label, $checked_nested = null ) {
	$the_value = null;

	// Get the field
	$field = pilau_gf_get_field( $form, 'label', $label, $checked_nested );
	if ( $field ) {

		if ( $checked_nested ) {

			// With current project, this code seems to be wrong
			// However, needs to be kept as it was tested and worked with previous projects
			$id = $field[0]->{$checked_nested}[ $field[1] ]['id'];
			if ( ! empty( $id ) ) {
				$the_value = $entry[ "$id" ];
			}

		} else {

			switch ( $field->type ) {

				case 'checkbox':

					if ( count( $field->choices ) > 1 ) {

						// Gather mutiple values into an array
						$the_value = array();
						foreach ( $entry as $name => $value ) {
							$name_parts = explode( '.', $name );
							if ( count( $name_parts ) == 2 && $name_parts[0] == $field->id && $value ) {
								$the_value[] = $value;
							}
						}


					} else {

						// Just pass the value of single checkbox
						$the_value = $entry[ $field->inputs[0]['id'] ];

					}

					break;

				default:

					// Simple field!
					$the_value = $entry[ $field->id ];

					break;

			}

		}

	}

	return $the_value;
}