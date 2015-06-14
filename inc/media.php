<?php

/**
 * Media functions
 *
 * @package	Pilau_Base
 * @since	2.0
 */


if ( ! function_exists( 'pilau_wmode_opaque' ) ) {
	/**
	 * Add wmode parameter to Flash embeds to avoid z-index issue
	 *
	 * @since	Pilau_Base 2.0
	 */
	add_filter( 'oembed_result', 'pilau_wmode_opaque', 10, 3 );
	function pilau_wmode_opaque( $html, $url, $args ) {
		if ( strpos( $html, '<param name="movie"' ) !== false ) {
			$html = preg_replace( '|</param>|', '</param><param name="wmode" value="opaque"></param>', $html, 1 );
		}
		if ( strpos( $html, '<embed' ) !== false ) {
			$html = str_replace( '<embed', '<embed wmode="opaque"', $html );
		}
		if ( strpos( $html, '<iframe' ) !== false ) {
			$html = str_replace( '<iframe', '<iframe wmode="opaque"', $html );
			$html = preg_replace( '/ src="[^"\?]+\?/', '\0wmode=transparent&amp;', $html );
		}
		return $html;
	}
}


if ( ! function_exists( 'pilau_video_or_image' ) ) {
	/**
	 * Try to output a video or an image
	 *
	 * @since	Pilau_Base 2.0
	 *
	 * @uses	wp_oembed_get()
	 * @uses	esc_url()
	 * @uses	esc_attr()
	 *
	 * @param	string	$url	URL, perhaps an image, perhaps a video embed URL
	 * @param	string	$alt	Alt text for an image
	 * @return	string
	 */
	function pilau_video_or_image( $url, $alt = '' ) {
		$url_parts = parse_url( $url );
		if ( $url_parts['host'] == $_SERVER['HTTP_HOST'] && file_exists( ABSPATH . trim( $url_parts['path'], '/' ) ) ) {
			// Image
			return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
		} else {
			// Video?
			return wp_oembed_get( $url );
		}
	}
}


if ( ! function_exists( 'pilau_image_maybe_caption' ) ) {
	/**
	 * Output an image with optional caption, using <figure> and <figcaption> tags
	 *
	 * @since	Pilau_Base 2.0
	 *
	 * @param	int				$image_id		ID of the image
	 * @param	string			$size			Size of the image; defaults to 'post-thumbnail'
	 * @param	string			$alt			Alternate text for the image; defaults to image alt or post title
	 * @param	array|string	$fig_class		Class(es) for the <figure> tag
	 * @param	string			$fig_id			ID for the <figure> tag
	 * @param	string			$link			Optional link to wrap around image
	 * @param	bool			$defer			Optional deferred loading
	 * @return	void
	 */
	function pilau_image_maybe_caption( $image_id, $size = 'post-thumbnail', $alt = null, $fig_class = null, $fig_id = null, $link = null, $defer = false ) {

		// Try to get image
		if ( ! is_int( $image_id ) && ! ctype_digit( $image_id ) ) {
			return;
		}
		$image = get_post( $image_id );
		if ( ! $image ) {
			return;
		}

		// Initialize
		$fig_class = (array) $fig_class;

		// Start output
		echo '<figure class="' . esc_attr( implode( ' ', $fig_class ) ) . '"';
		if ( $fig_id ) {
			echo ' id="' . esc_attr( $fig_id ) . '"';
		}
		echo '>';

		// Link?
		if ( $link ) {
			echo '<a href="' . esc_url( $link ) . '">';
		}

		// Image
		pilau_img_defer_load( $image_id, $size, $alt, array(), $defer );

		// Link?
		if ( $link ) {
			echo '</a>';
		}

		// Caption?
		if ( $image->post_excerpt ) {
			echo '<figcaption>' . $image->post_excerpt . '</figcaption>';
		}

		echo '</figure>';

	}
}


if ( ! function_exists( 'pilau_img_defer_load' ) ) {
	/**
	 * Ouput an image, with optional deferred loading
	 *
	 * @since	Pilau_Base 2.0
	 * @link	http://24ways.org/2010/speed-up-your-site-with-delayed-content/
	 *
	 * @param	mixed	$image	Either an attachment ID, or an array with 'width', 'height', 'src'
	 * @param	string	$size	Size of the image (if attachment ID is passed); defaults to 'post-thumbnail'
	 * @param	string	$alt	Alternate text for the image; defaults to image alt or post title
	 * @param	bool	$defer	Defaults to false
	 * @return	void
	 */
	function pilau_img_defer_load( $image, $size = 'post-thumbnail', $alt = null, $class = array(), $defer = false ) {

		// Initialize
		if ( ! is_array( $image ) && $image_meta = wp_get_attachment_metadata( $image ) ) {
			$image_id = $image;
			$image = array(
				'width'		=> $image_meta['width'],
				'height'	=> $image_meta['height'],
				'src'		=> pilau_get_image_url( $image_id, $size )
			);
			if ( array_key_exists( $size, $image_meta['sizes'] ) ) {
				$image['width'] = $image_meta['sizes'][ $size ]['width'];
				$image['height'] = $image_meta['sizes'][ $size ]['height'];
			}
			if ( ! is_null( $alt ) ) {
				$image['alt'] = $alt;
			} else if ( ! $image['alt'] = array_shift( get_post_meta( $image_id, '_wp_attachment_image_alt' ) ) ) {
				$image['alt'] = get_the_title( $image_id );
			}
		} else {
			$image['alt'] = is_null( $alt ) ? '' : $alt;
		}

		// Output?
		if ( is_array( $image ) ) {
			echo '<img ';
			if ( $defer ) {
				echo 'data-defer-src="' . esc_url( $image['src'] ) . '" src="' . get_stylesheet_directory_uri() . '/img/placeholder.gif"';
			} else {
				echo 'src="' . esc_url( $image['src'] ) . '"';
			}
			echo ' class="' . implode( ' ', $class ) . '" width="' . esc_attr( $image['width'] ) . '" height="' . esc_attr( $image['height'] ) . '" alt="' . esc_attr( $image['alt'] ) . '">';
		}

	}
}


if ( ! function_exists( 'pilau_responsive_image' ) ) {
	/**
	 * Generate image markup using the srcset and sizes attributes for the <img> element
	 *
	 * - Make sure you populate $pilau_image_sizes in pilau_setup_media() in the starter theme
	 * - For proper browser support, enqueue the Picturefill script in the starter theme
	 *
	 * @since	Pilau_Base 2.0
	 * @link	https://css-tricks.com/responsive-images-youre-just-changing-resolutions-use-srcset/
	 * @link	http://ericportis.com/posts/2014/srcset-sizes/
	 *
	 * @uses	array	$pilau_image_sizes
	 * @param	int		$image_id
	 * @param	array	$srcset_sizes	The WP image sizes to pass to srcset. Defaults to thumbnail,
	 * 									medium, large
	 * @param	string	$default_size	Defaults to 'full'
	 * @param	array	$sizes			Defaults to 100vw
	 * @param	string	$alt			Explicitly pass an empty string if necessary; null will trigger
	 * 									an attempt to grab the alt text from the attachment, with title
	 * 									as a fallback
	 * @param	array	$classes
	 * @param	array	$picture_srcs	An array of arrays, each with a 'media' key/value and a 'srcset'
	 * 									key/value, for each source element
	 * @return	string
	 */
	function pilau_responsive_image( $image_id, $srcset_sizes = null, $default_size = 'full', $sizes = null, $alt = null, $classes = array(), $picture_srcs = array() ) {
		global $pilau_image_sizes;
		$output = '';

		// Defaults
		if ( empty( $srcset_sizes ) ) {
			$srcset_sizes = array( 'thumbnail', 'medium', 'large' );
		}
		if ( empty( $sizes ) ) {
			$sizes = array( '100vw' );
		}

		// Allow an empty string to be passed for alt
		if ( is_null( $alt ) ) {
			$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( empty( $alt ) ) {
				$alt = get_the_title( $image_id );
			}
		}

		// Build the srcset attribute
		$srcset = array();
		foreach ( $srcset_sizes as $srcset_size ) {
			if ( isset( $pilau_image_sizes[ $srcset_size ]['width'] ) ) {
				$srcset[] = pilau_get_image_url( $image_id, $srcset_size ) . ' ' . $pilau_image_sizes[ $srcset_size ]['width'] . 'w';
			}
		}

		// Full width needs independent attention
		if ( in_array( 'full-width', $srcset_sizes ) ) {
			$image_metadata = wp_get_attachment_image_src( $image_id, 'full-width' );
			$srcset[] = $image_metadata[0] . ' ' . $image_metadata[1] . 'w';
		}

		// Generate the markup
		$output .= '<img src="' . esc_url( pilau_get_image_url( $image_id, $default_size ) ) . '" srcset="' . esc_attr( implode( ', ', $srcset ) ) . '" sizes="' . esc_attr( implode( ', ', $sizes ) ) . '" alt="' . esc_attr( $alt ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		// Using <picture> for art direction?
		if ( ! empty( $picture_srcs ) ) {
			foreach ( $picture_srcs as $picture_src ) {
				$output = '<source media="' . esc_attr( $picture_src['media'] ) . '" srcset="' . esc_attr( $picture_src['srcset'] ) . '">' . "\n" . $output;
			}
			$output = '<!--[if IE 9]><video style="display: none;"><![endif]-->' . "\n" . $output . "\n" . '<!--[if IE 9]></video><![endif]-->' . "\n";
			$output = '<picture>' . "\n" . $output . '</picture>' . "\n";
		}

		return $output;
	}
}


if ( ! function_exists( 'pilau_responsive_picture' ) ) {
	/**
	 * Generate image markup using the <picture> element for responsive sizes
	 *
	 * @since	Pilau_Base 2.0
	 * @link	http://scottjehl.github.io/picturefill/
	 * @link	http://www.bobz.co/responsive-images-picturefill-wordpress-theme-development/
	 *
	 * @param	int		$image_id
	 * @param	string	$size_suffix	A suffix to prepend before the three generic image size names
	 * @param	array	$size_names		Instead of a suffix, three completely custom image size names
	 * 									can be supplied in this array. 3 size names should be supplied,
	 * 									each one smaller than the previous one.
	 * @param	string	$alt
	 * @param	array	$classes
	 * @return	string
	 */
	function pilau_responsive_picture( $image_id, $size_suffix = '', $size_names = null, $alt = null, $classes = array() ) {
		global $pilau_breakpoints;
		$output = '';

		// Determine size names
		if ( ! is_array( $size_names ) || count( $size_names ) != 3 ) {
			$size_names = array(
				$size_suffix . 'large',
				$size_suffix . 'medium',
				$size_suffix . 'thumbnail',
			);
		}

		// Try to get the images
		$images = array(
			'large'		=> pilau_get_image_url( $image_id, $size_names[0] ),
			'medium'	=> pilau_get_image_url( $image_id, $size_names[1] ),
			'small'		=> pilau_get_image_url( $image_id, $size_names[2] )
		);

		// Generate the markup
		$output .= '<picture class="' . implode( ' ', $classes ) . '">' . "\n";
		$output .= '<!--[if IE 9]><video style="display: none;"><![endif]-->' . "\n";
		// Go through the breakpoints
		foreach ( $pilau_breakpoints as $size_name => $width ) {
			if ( $images[ $size_name ] ) {
				$output .= '<source srcset="' . $images[ $size_name ] . '" media="(min-width: ' . $width . ')">' . "\n";
			}
		}
		// Add the final "small" size
		$output .= '<source srcset="' . $images['small'] . '">' . "\n";
		$output .= '<!--[if IE 9]></video><![endif]-->' . "\n";
		$output .= '<img srcset="' . $images['small'] . '" alt="' . $alt . '">' . "\n";
		$output .= '</picture>' . "\n";

		return $output;
	}
}
