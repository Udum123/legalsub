<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.1.4
 */

use Rtcl\Helpers\Functions;

global $listing;

$sidebar_position = Functions::get_option_item( 'rtcl_moderation_settings', 'detail_page_sidebar_position', 'right' );
$sidebar_class    = [
	'col-md-3',
	'order-2'
];
if ( $sidebar_position == "left" ) {
	$sidebar_class   = array_diff( $sidebar_class, [ 'order-2' ] );
	$sidebar_class[] = 'order-1';
} else if ( $sidebar_position == "bottom" ) {
	$sidebar_class   = array_diff( $sidebar_class, [ 'col-md-3' ] );
	$sidebar_class[] = 'rtcl-listing-bottom-sidebar';
}
?>

<!-- Seller / User Information -->
<div class="<?php echo esc_attr( implode( ' ', $sidebar_class ) ); ?>">
    <div class="listing-sidebar">
		<?php $listing->the_user_info(); ?>
		<?php do_action( 'rtcl_after_single_listing_sidebar', $listing->get_id() ); ?>
    </div>
</div>
