<?php
/**
 * Custom Field
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var string $field
 * @var string $id
 * @var string $field_attr
 * @var string $label
 * @var string $required_label
 * @var string $description
 */

use Rtcl\Helpers\Functions;

?>
<div class="form-group rtcl-cf-wrap"<?php Functions::esc_attrs_e($field_attr) ?>>
    <label for="<?php echo esc_attr($id) ?>"
           class="col-form-label rtcl-cf-label"><?php echo esc_html($label);
        Functions::print_html($required_label); ?></label>
    <div class='rtcl-cf-field-wrap'>
        <?php Functions::print_html($field, true); ?>
        <div class='help-block with-errors'></div>
        <?php if ($description) : ?>
            <small class='help-block'><?php echo esc_html($description); ?></small>
        <?php endif; ?>
    </div>
</div>