<?php
/**
 * User Details
 * This template can be overridden by copying it to yourtheme/classified-listing/emails/email-customer-details.php.
 *
 * @package ClassifiedListing/Templates/Emails
 * @version 2.3.0
 */

defined('ABSPATH') || exit;
?>
<?php if (!empty($fields)) : ?>
    <div style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;">
        <h2><?php esc_html_e('User details', 'classified-listing'); ?></h2>
        <ul>
            <?php foreach ($fields as $field) : ?>
                <li><strong><?php echo wp_kses_post($field['label']); ?>:</strong> <span
                            class="text">
		<?php echo wp_kses_post($field['value']); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
