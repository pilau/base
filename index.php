<?php

/**
 * Theme index (main posts listing)
 *
 * This is the same code as in Pilau Starter's index.php; it is intended to be
 * overridden, and is included here so Pilau Base is not seen as "broken" by WP
 *
 * @package	Pilau_Base
 * @since	2.0
 */

?>

<?php get_header(); ?>

<div id="content" role="main">

	<h1><?php _e( 'News' ) ?></h1>

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'loop', get_post_format() ); ?>

		<?php endwhile; ?>

	<?php endif; ?>

</div>

<?php get_sidebar( 'primary' ); ?>

<?php get_footer(); ?>