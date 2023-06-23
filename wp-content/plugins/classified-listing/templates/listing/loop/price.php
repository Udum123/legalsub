<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/loop/price.php.
 *
 *
 * @package classified-listing/Templates
 * @version 1.5
 * @var Listing $listing
 */

use Rtcl\Models\Listing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $listing;

if ( ! $listing || ! $listing->can_show_price() ) {
	return;
}

if ( $price_html = $listing->get_price_html() ) : ?>
	<div class="rtcl-price-wrap"><?php echo $price_html; ?></div>
<?php endif; ?>

