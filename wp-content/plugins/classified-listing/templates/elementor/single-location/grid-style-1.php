<?php

/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

$count_html = sprintf( _nx( '%s Ad', '%s Ads', $count, 'Number of Ads', 'classified-listing' ), number_format_i18n( $count ) );

$link_start   = $settings['enable_link'] ? '<a href="' . $permalink . '">' : '';
$link_end     = $settings['enable_link'] ? '</a>' : '';
$location_box = $settings['rtcl_location_style'] ? $settings['rtcl_location_style'] : ' style-1';
$class        = $settings['display_count'] ? ' rtin-has-count ' : '';
$class       .= ' location-box-' . $location_box;

?>
<div class="rtcl-el-listing-location-box <?php echo esc_attr( $class ); ?>">
	<?php echo wp_kses_post( $link_start ); ?>
	<div class="rtin-img"></div>
	<div class="rtin-content">
		<h3 class="rtin-title"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $settings['display_count'] ) : ?>
			<div class="rtin-counter"><?php echo esc_html( $count_html ); ?></div>
		<?php endif; ?>
	</div>

	<?php echo wp_kses_post( $link_end ); ?>
</div>
