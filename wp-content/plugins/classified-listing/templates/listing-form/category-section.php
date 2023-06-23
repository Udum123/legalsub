<?php
/**
 * @var int    $category_id
 * @var string $selected_type
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

?>
<div class="rtcl-post-section-title">
    <h3>
        <i class="rtcl-icon rtcl-icon-tags"></i><?php esc_html_e("Selected Category", "classified-listing"); ?>
    </h3>
    <div class="selected-cat">
        <?php echo Functions::get_selected_cat($category_id); ?>
        <?php if (!$post_id):
            $parent_id = Functions::get_term_top_most_parent_id($category_id, rtcl()->category);
            $args = array(
                'category' => $parent_id,
            );
            if ($parent_id === $category_id) {
                unset($args['category']);
            }
            if (!Functions::is_ad_type_disabled()) {
                array_unshift($args, array('type' => $selected_type));
            }
            ?>
            <a href="<?php echo add_query_arg($args, Link::get_listing_form_page_link()); ?>"
               class="change-cat"><?php esc_html_e("Change Category", "classified-listing"); ?></a>
        <?php endif; ?>
    </div>
</div>