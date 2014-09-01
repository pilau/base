/**
 * Pilau Base JS lib
 */


/**
 * Get a part of a string
 *
 * @since	Pilau_Starter 0.1
 * @param	{string}		s		The string
 * @param	{number|string}	i		The numeric index, or 'first' or 'last' (default 'last')
 * @param	{string}		sep		The character used a separator in the passed string (default '-')
 * @return	{string}
 */
function pilau_get_string_part( s, i, sep ) {
	var parts;
	if ( ! sep )
		sep = '-';
	parts = s.split( sep );
	if ( ! i || i == 'last' )
		i = parts.length - 1;
	else if ( i == 'first' )
		i = 0;
	return parts[ i ];
}
