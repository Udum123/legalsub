<?php
/**
 * Dashboard
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var String $note
 * @var WP_User $current_user
 */


use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
} ?>

	<div class="rtcl-user-info media">
		<div class="media-thumb rtcl-user-avatar mr-3">
			<?php
			$pp_id = absint(get_user_meta($current_user->ID, '_rtcl_pp_id', true));
			echo($pp_id ? wp_get_attachment_image($pp_id, [100, 100]) : get_avatar($current_user->ID)); ?>
		</div>
		<div class="media-body">
			<h5 class="mt-0 mb-2"><?php echo esc_html(Functions::get_author_name($current_user)); ?></h5>
			<p class="media-heading"><?php printf("<strong>%s</strong> : %s", esc_html__("Email", "classified-listing"), $current_user->user_email); ?></p>
			<?php $current_user->description ? printf("<p>%s</p>", $current_user->description) : '' ?>
		</div>
	</div>
<?php if (!empty($note)): ?>
	<div class="rtcl-user-note">
		<h4><?php echo esc_html__("Note from Admin", "classified-listing") ?></h4>
		<p><?php echo wp_kses_post($note); ?></p>
	</div>
<?php endif; ?>