<?php

/**
 *
 * @author        RadiusTheme
 * @package    classified-listing/templates
 * @version     1.0.0
 *
 * @var array $misc_settings
 */

if ( in_array( 'facebook', $misc_settings['social_services'] ) ) : ?>
	<a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url($url); ?>" target="_blank" rel="nofollow"><span class="rtcl-icon rtcl-icon-facebook"></span></a>
<?php endif; ?>

<?php if ( in_array( 'twitter', $misc_settings['social_services'] ) )  : ?>
	<a class="twitter" href="https://twitter.com/intent/tweet?text=<?php echo esc_attr($title); ?>&amp;url=<?php echo esc_url($url); ?>" target="_blank" rel="nofollow"><span class="rtcl-icon rtcl-icon-twitter"></span></a>
<?php endif; ?>

<?php if ( in_array( 'linkedin', $misc_settings['social_services'] ) )  : ?>
	<a class="linkedin" href="https://www.linkedin.com/shareArticle?url=<?php echo esc_url($url); ?>&amp;title=<?php echo esc_attr($title); ?>" target="_blank" rel="nofollow"><span class="rtcl-icon rtcl-icon-linkedin"></span></a>
<?php endif; ?>

<?php if ( in_array( 'pinterest', $misc_settings['social_services'] ) )  : ?>
	<a class="pinterest" href="https://pinterest.com/pin/create/button/?url=<?php echo esc_url($url); ?>&amp;media=<?php echo esc_url($thumbnail); ?>&amp;description=<?php echo esc_attr($title); ?>" target="_blank" rel="nofollow"><span class="rtcl-icon rtcl-icon-pinterest-circled"></span></a>
<?php endif; ?>

<?php if ( in_array( 'whatsapp', $misc_settings['social_services'] ) ): ?>
	<a class="whatsapp" href="https://wa.me/?text=<?php echo esc_attr($title).' '.urlencode( $url); ?>" data-action="share/whatsapp/share" target="_blank" rel="nofollow"><i class="rtcl-icon rtcl-icon-whatsapp"></i></a>
<?php endif; ?>

<?php if ( in_array( 'telegram', $misc_settings['social_services'] ) ): ?>
    <a class="telegram" href="https://telegram.me/share/url?text=<?php echo esc_attr($title); ?>&amp;url=<?php echo esc_url($url); ?>" target="_blank" rel="nofollow"><i class="rtcl-icon rtcl-icon-telegram"></i></a>
<?php endif; ?>
