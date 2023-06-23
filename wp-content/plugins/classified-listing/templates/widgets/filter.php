<?php
/**
 * @var array  $data
 * @var Filter $object
 */

use Rtcl\Helpers\Functions;
use Rtcl\Widgets\Filter;

?>
<div class="panel-block">
    <?php do_action('rtcl_widget_before_filter_form', $object, $data) ?>
    <form class="rtcl-filter-form"
          action="<?php echo esc_url(Functions::get_filter_form_url()) ?>">
        <?php do_action('rtcl_widget_filter_form_start', $object, $data) ?>
        <div class="ui-accordion">
            <?php do_action('rtcl_widget_filter_form', $object, $data) ?>
        </div>
        <?php do_action('rtcl_widget_filter_form_end', $object, $data) ?>
    </form>
    <?php do_action('rtcl_widget_after_filter_form', $object, $data) ?>
</div>
