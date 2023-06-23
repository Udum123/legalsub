<?php
/**
 * Listing Price unite field
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.0
 *
 * @var array $price_unit_list
 * @var array $price_unit
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (empty($price_units)) {
    return;
}
?>
<div class="form-group rtcl-price-item" id="rtcl-price-unit-wrap">
    <label for="rtcl-price-unit"><?php esc_html_e("Price Unit", "classified-listing"); ?></label>
    <select class="form-control rtcl-select2" id="rtcl-price-unit" name="_rtcl_price_unit">
        <option value=""><?php esc_html_e("No unit", "classified-listing"); ?></option>
        <?php
        foreach ($price_unit_list as $unit_key => $unit) {
            if (in_array($unit_key, $price_units)) {
                echo sprintf('<option value="%s"%s>%s (%s)</label>',
                    esc_attr($unit_key),
                    $price_unit == $unit_key ? " selected" : null,
                    esc_html($unit['title']),
                    esc_html($unit['short'])
                );
            }
        }
        ?>
    </select>
</div>
