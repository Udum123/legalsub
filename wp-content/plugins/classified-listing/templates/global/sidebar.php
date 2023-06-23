<?php
/**
 * Sidebar
 *
 * @package     ClassifiedListing/Templates
 * @version     1.4.0
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ( Functions::is_listings() || Functions::is_listing_taxonomy() ) && is_active_sidebar( 'rtcl-archive-sidebar' ) ) {
	?>
    <div id="rtcl-sidebar" class="rtcl-sidebar-wrapper">
		<?php dynamic_sidebar( 'rtcl-archive-sidebar' ); ?>
    </div>
	<?php
} else if ( Functions::is_listing() && is_active_sidebar( 'rtcl-single-sidebar' ) ) {
	?>
    <div id="rtcl-sidebar" class="rtcl-sidebar-wrapper">
		<?php dynamic_sidebar( 'rtcl-single-sidebar' ); ?>
    </div>
	<?php
} else {
	get_sidebar( 'listing' );
}

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
