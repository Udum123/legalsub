<?php
/**
 * Show options for ordering
 *
 * @version     1.5.5
 *
 * @var array  $catalog_orderby_options
 * @var string $orderby
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit;
}
if (empty($catalog_orderby_options)) {
    return;
}
?>
<form class="rtcl-ordering" method="get">
    <select name="orderby" class="orderby" aria-label="<?php esc_attr_e('Listing order', 'classified-listing'); ?>">
        <?php foreach ($catalog_orderby_options as $id => $name) : ?>
            <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="hidden" name="paged" value="1"/>
    <?php Functions::query_string_form_fields(null, ['orderby', 'submit', 'paged']); ?>
</form>
