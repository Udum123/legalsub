<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 * @var Listing $listing ;
 */

use Rtcl\Models\Listing;

if (!$listing) global $listing;

if (empty($listing)) return;
//Make sure div has no empty space to hide it when empty

?>
<div class='rtcl-listing-badge-wrap'><?php do_action('rtcl_listing_badges', $listing); ?></div>
