<?php
/**
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit;
}

Functions::print_notices();

?>
<div class="rtcl-checkout-content">
    <?php do_action('rtcl_checkout_content'); ?>
</div>