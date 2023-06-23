<?php
/**
 * Social profiles
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var int   $post_id
 * @var array $social_profiles
 */

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!Functions::is_enable_social_profiles() || empty($social_list = Options::get_social_profiles_list()))
    return;
?>
    <div class="rtcl-post-video-urls rtcl-post-section<?php echo esc_attr(is_admin() ? " rtcl-is-admin" : '') ?>">
        <div class="rtcl-post-section-title">
            <h3>
                <i class="rtcl-icon rtcl-icon-share"></i><?php esc_html_e("Social Profiles", "classified-listing"); ?>
            </h3>
        </div>
        <?php
        foreach ($social_list as $item_key => $item) {
            ?>
            <div class="form-group row">
                <label for="rtcl-social-<?php echo esc_attr($item_key) ?>"
                       class="col-md-2 col-form-label"><?php echo esc_html($item); ?></label>
                <div class="col-md-10">
                    <input type="url" class="form-control" id="rtcl-social-<?php echo esc_attr($item_key) ?>"
                           name="rtcl_social_profiles[<?php echo esc_attr($item_key) ?>]"
                           value="<?php echo !empty($social_profiles[$item_key]) ? esc_url($social_profiles[$item_key]) : '' ?>"
                    />
                </div>
            </div>
            <?php
        }
        ?>
    </div>
<?php


