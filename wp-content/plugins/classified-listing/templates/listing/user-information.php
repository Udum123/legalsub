<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var Listing $listing
 */

use Rtcl\Models\Listing;

?>
<div class="rtcl-listing-user-info">
    <div class="rtcl-listing-side-title">
        <h3><?php esc_html_e("Contact", 'classified-listing'); ?></h3>
    </div>
    <div class="list-group">
        <?php do_action('rtcl_listing_seller_information', $listing); ?>
    </div>
</div>

