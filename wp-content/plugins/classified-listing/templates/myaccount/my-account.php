<?php
/**
 *
 * @author 		RadiusTheme
 * @package 	classified-listing/templates
 * @version     1.0.0
 */

use Rtcl\Helpers\Functions;

if (! defined('ABSPATH')) {
	exit;
}

Functions::print_notices();

?>
<div class="rtcl-MyAccount-wrap">
	<?php do_action('rtcl_account_navigation'); ?>

    <div class="rtcl-MyAccount-content">
		<?php do_action('rtcl_account_content'); ?>
    </div>
</div>
