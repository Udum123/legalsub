<?php
/**
 * The template to display the Social profile
 *
 * @author  RadiousTheme
 * @package classified-listing/Templates
 * @version 1.5.72
 * @var array $social_profiles
 */

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!Functions::is_enable_social_profiles() || empty($social_profiles) || empty($social_list = Options::get_social_profiles_list())) {
    return;
}
?>
<div class="rtcl-social-profile-wrap">
    <?php
		$label_text = esc_html__( 'Social Profiles:', 'classified-listing' );
		$profile_label       = apply_filters( 'rtcl_social_profile_label', $label_text );
        if ( $profile_label ) {
            ?>
            <div class="rtcl-social-profile-label">
                <?php echo esc_html( $profile_label ); ?>
            </div>
            <?php
        }
	?>
    <div class="rtcl-social-profiles">
        <?php
        foreach ($social_list as $item => $value) {
            if (!empty($social_profiles[$item])) {
                ?>
                <a target="_blank" href="<?php echo esc_url($social_profiles[$item]) ?>" title="<?php echo esc_attr($value) ?>"><i
                            class="rtcl-icon rtcl-icon-<?php echo esc_attr($item) ?>"></i></a>
                <?php
            }
        }
        ?>
    </div>
</div>