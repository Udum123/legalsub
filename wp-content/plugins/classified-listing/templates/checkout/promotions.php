<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var array $pricing_options
 */


use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

$currency = Functions::get_order_currency();
$currency_symbol = Functions::get_currency_symbol($currency);
?>

<table id="rtcl-checkout-form-data"
	   class="rtcl-responsive-table rtcl-pricing-options form-group table table-hover table-stripped table-bordered">
	<tr>
		<th><?php esc_html_e("Pricing Option", "classified-listing"); ?></th>
		<th><?php esc_html_e("Description", "classified-listing"); ?></th>
		<th><?php esc_html_e("Visibility", "classified-listing"); ?></th>
		<th><?php printf(__('Price [%s %s]', 'classified-listing'),
				$currency,
				$currency_symbol); ?></th>
	</tr>
	<?php
	if (!empty($pricing_options)):
		foreach ($pricing_options as $pricing) :
			$price = get_post_meta($pricing->ID, 'price', true);
			$visible = get_post_meta($pricing->ID, 'visible', true);
			$featured = get_post_meta($pricing->ID, 'featured', true);
			$top = get_post_meta($pricing->ID, '_top', true);
			$bump_up = get_post_meta($pricing->ID, '_bump_up', true);
			$description = get_post_meta($pricing->ID, 'description', true);
			?>
			<tr>
				<td class="rtcl-pricing-option form-check"
					data-label="<?php esc_attr_e("Pricing Option:", "classified-listing"); ?>">
					<?php
					printf('<label><input type="radio" name="%s" value="%s" class="rtcl-checkout-pricing" required data-price="%s"/> %s</label>',
						'pricing_id', esc_attr($pricing->ID), esc_attr($price), esc_html($pricing->post_title));
					?>
				</td>
				<td class="rtcl-pricing-features"
					data-label="<?php esc_attr_e("Description:", "classified-listing"); ?>">
					<?php Functions::print_html($description, true); ?>
				</td>
				<td class="rtcl-pricing-visibility"
					data-label="<?php esc_attr_e("Visibility:", "classified-listing"); ?>">
					<?php
					printf('<span>%s</span>', sprintf(_n('%s Day', '%s Days', absint($visible), 'classified-listing'), number_format_i18n(absint($visible))));
					$promotions = Options::get_listing_promotions();
					foreach ($promotions as $promo_id => $promotion) {
						if (get_post_meta($pricing->ID, $promo_id, true)) {
							echo '<span class="badge rtcl-badge-' . esc_attr($promo_id) . '">' . esc_html($promotion) . '</span>';
						}
					}
					?>
				</td>
				<td class="rtcl-pricing-price text-right"
					data-label="<?php printf(__('Price [%s %s]:', 'classified-listing'),
						$currency,
						$currency_symbol); ?>"><?php echo Functions::get_payment_formatted_price($price); ?> </td>
			</tr>
		<?php endforeach;
	else: ?>
		<tr>
			<th colspan="4"><?php esc_html_e("No promotion plan found.", "classified-listing"); ?></th>
		</tr>
	<?php endif; ?>
</table>