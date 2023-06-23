<?php

/**
 * This template displays the public-facing aspects of the widget.
 *
 * @link    https://pluginsware.com
 * @since   1.5.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */
?>

<div class="acadp acadp-widget-listing-video">
	<div class="embed-responsive embed-responsive-16by9">
		<iframe width="560" height="315" class="acadp-video embed-responsive-item" data-src="<?php echo esc_url( $video_url ); ?>" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		<?php the_acadp_cookie_consent( 'video' ); ?>
	</div>
</div>