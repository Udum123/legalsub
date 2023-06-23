<?php
/**
 * Login Form Information
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var int   $post_id
 * @var array $bhs         Business hours
 * @var array $special_bhs Special business hours
 * @var array $weekdays
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Utility;

if (!Functions::is_enable_business_hours())
    return;
?>
<div class="rtcl-post-bhs rtcl-post-section<?php echo esc_attr(is_admin() ? " rtcl-is-admin" : '') ?>">
    <div class="rtcl-post-section-title">
        <h3><i class="rtcl-icon rtcl-icon-clock"></i><?php esc_html_e("Open Hours", "classified-listing"); ?></h3>
    </div>
    <div id="rtcl-bhs-holder">
        <div class="form-check">
            <label class="form-check-label"
                   for="rtcl-active-bhs"><?php esc_html_e("Active Opening Hours", "classified-listing"); ?></label>
        </div>
        <input type="hidden" name="_rtcl_active_bhs" value="0">
        <input type="checkbox" name="_rtcl_active_bhs" value="1" class="form-check-input"
               id="rtcl-active-bhs"<?php echo !empty($bhs) ? ' checked' : '' ?>>
        <div id="rtcl-bhs-wrap">
            <p class="small text-muted"><?php esc_html_e("Define your weekly opening hours", "classified-listing"); ?></p>
            <div class="rtcl-bhs">
                <?php foreach ($weekdays as $day_key => $day) { ?>
                    <div class="rtcl-bh">
                        <div class="rtcl-day-label"><?php echo esc_html($day); ?></div>
                        <div class="rtcl-day-actions">
                            <div class="action-item form-check open">
                                <label class="form-check-label"
                                       for="business-hours-open-<?php echo esc_html($day_key); ?>">
                                    <?php esc_html_e("Open", "classified-listing"); ?>
                                </label>
                            </div>
                            <input type="checkbox" name="_rtcl_bhs[<?php echo $day_key ?>][open]"
                                   class="form-check-input check-open"
                                   id="business-hours-open-<?php echo esc_html($day_key); ?>"<?php echo !empty($bhs[$day_key]['open']) ? ' checked' : '' ?>>
                            <div class="action-item form-check day-time-slot">
                                <label class="form-check-label"
                                       for="time-slot-open-<?php echo esc_html($day_key); ?>">
                                    <?php esc_html_e("Want to set a time slot? (Default All day long)", "classified-listing"); ?>
                                </label>
                            </div>
                            <input type="checkbox" name="_rtcl_bhs[<?php echo $day_key ?>][open]"
                                   value="1"
                                   class="form-check-input check-time-slot"
                                   id="time-slot-open-<?php echo esc_html($day_key); ?>"
                                <?php echo !empty($bhs[$day_key]['times']) ? ' checked' : '' ?>>
                            <div class="action-item time-slots">
                                <?php
                                $count = 0;
                                if (!empty($bhs[$day_key]['times'])) {
                                    foreach ($bhs[$day_key]['times'] as $time_id => $time) {
                                        ?>
                                        <div class="time-slot">
                                            <div class="time-slot-start">
                                                <input type="text"
                                                       name="_rtcl_bhs[<?php echo $day_key ?>][times][<?php echo $count ?>][start]"
                                                       value="<?php echo esc_attr(Utility::formatTime($time['start'], NULL, 'H:i')) ?>"
                                                       class="bhs-timepicker">
                                            </div>
                                            <div class="time-slot-end">
                                                <input type="text"
                                                       name="_rtcl_bhs[<?php echo $day_key ?>][times][<?php echo $count ?>][end]"
                                                       value="<?php echo esc_attr(Utility::formatTime($time['end'], NULL, 'H:i')) ?>"
                                                       class="bhs-timepicker">
                                            </div>
                                            <div class="time-slot-action">
                                                <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-minus"></i>
                                            </div>
                                        </div>
                                        <?php $count++;
                                    }
                                }
                                ?>
                                <div class="time-slot">
                                    <div class="time-slot-start">
                                        <input type="text"
                                               name="_rtcl_bhs[<?php echo $day_key ?>][times][<?php echo $count ?>][start]"
                                               class="bhs-timepicker">
                                    </div>
                                    <div class="time-slot-end">
                                        <input type="text"
                                               name="_rtcl_bhs[<?php echo $day_key ?>][times][<?php echo $count ?>][end]"
                                               class="bhs-timepicker">
                                    </div>
                                    <div class="time-slot-action">
                                        <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-plus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div id="rtcl-special-bhs-wrap">
            <div class="form-check">
                <label class="form-check-label"
                       for="rtcl-active-special-bhs"><?php esc_html_e("Special Hours - overrides", "classified-listing"); ?></label>
            </div>
            <input type="hidden" name="_rtcl_active_special_bhs" value="0">
            <input type="checkbox" name="_rtcl_active_special_bhs" value="1"
                   class="form-check-input"
                   id="rtcl-active-special-bhs"<?php echo !empty($special_bhs) ? ' checked' : '' ?>>
            <div id="rtcl-special-bhs-container">
                <p class="small text-muted"><?php esc_html_e("Define your weekly opening hours to override", "classified-listing"); ?></p>
                <div class="rtcl-special-bhs rtcl-bhs">
                    <?php
                    $count = 0;
                    if (!empty($special_bhs)) {
                        foreach ($special_bhs as $key => $hours) { ?>
                            <div class="rtcl-special-bh rtcl-bh">
                                <div class="rtcl-special-bh-date">
                                    <input type="text" class="shs bhs-datepicker"
                                           value="<?php echo esc_attr(Utility::formatDate($hours['date'], NULL, 'Y-m-d')) ?>"
                                           name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][date]"/>
                                </div>
                                <div class="rtcl-special-bh-actions rtcl-day-actions">
                                    <div class="action-item form-check open">
                                        <label class="form-check-label"
                                               for="shs-open-<?php echo esc_attr($count) ?>"><?php esc_html_e("Open", "classified-listing"); ?></label>
                                    </div>
                                    <input type="checkbox"
                                           name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][open]"
                                           class="form-check-input check-open"
                                           id="shs-open-<?php echo esc_attr($count) ?>"
                                        <?php echo !empty($special_bhs[$key]['open']) ? 'checked' : '' ?>>
                                    <div class="action-item form-check day-time-slot">
                                        <label class="form-check-label"
                                               for="shs-time-slot-open-<?php echo esc_attr($count) ?>"><?php esc_html_e("Want to set atime slot? (Default All day long)", "classified-listing"); ?></label>
                                    </div>
                                    <input type="checkbox"
                                           name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][open]" value="1"
                                           class="form-check-input check-time-slot"
                                           id="shs-time-slot-open-<?php echo esc_attr($count) ?>"
                                        <?php echo !empty($special_bhs[$key]['times']) ? 'checked' : '' ?>>
                                    <div class="action-item time-slots">
                                        <?php $time_count = 0;
                                        if (!empty($special_bhs[$key]['times'])) {
                                            foreach ($special_bhs[$key]['times'] as $time_id => $time) {
                                                ?>
                                                <div class="time-slot">
                                                    <div class="time-slot-start">
                                                        <input type="text"
                                                               name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][times][<?php echo esc_attr($time_count) ?>][start]"
                                                               value="<?php echo esc_attr(Utility::formatTime($time['start'], NULL, 'H:i')) ?>"
                                                               class="shs bhs-timepicker">
                                                    </div>
                                                    <div class="time-slot-end">
                                                        <input type="text"
                                                               name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][times][<?php echo esc_attr($time_count) ?>][end]"
                                                               value="<?php echo esc_attr(Utility::formatTime($time['end'], NULL, 'H:i')) ?>"
                                                               class="shs bhs-timepicker">
                                                    </div>
                                                    <div class="time-slot-action">
                                                        <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-minus"></i>
                                                    </div>
                                                </div>
                                                <?php $time_count++;
                                            }
                                        } ?>
                                        <div class="time-slot">
                                            <div class="time-slot-start">
                                                <input type="text"
                                                       name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][times][<?php echo esc_attr($time_count) ?>][start]"
                                                       class="shs bhs-timepicker">
                                            </div>
                                            <div class="time-slot-end">
                                                <input type="text"
                                                       name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][times][<?php echo esc_attr($time_count) ?>][end]"
                                                       class="shs bhs-timepicker">
                                            </div>
                                            <div class="time-slot-action">
                                                <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-minus"></i>
                                </div>
                            </div>
                            <?php $count++;
                        }
                    }
                    ?>
                    <div class="rtcl-special-bh rtcl-bh">
                        <div class="rtcl-special-bh-date">
                            <input type="text" class="bhs-datepicker" value=""
                                   name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][date]"/>
                        </div>
                        <div class="rtcl-special-bh-actions rtcl-day-actions">
                            <div class="action-item form-check open">
                                <label class="form-check-label"
                                       for="bho-open-<?php echo esc_attr($count) ?>"><?php esc_html_e("Open", "classified-listing"); ?></label>
                            </div>
                            <input type="checkbox" name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][open]"
                                   class="form-check-input check-open"
                                   id="bho-open-<?php echo esc_attr($count) ?>">
                            <div class="action-item form-check day-time-slot">
                                <label class="form-check-label"
                                       for="bho-time-slot-open-<?php echo esc_attr($count) ?>">
                                    <?php esc_html_e("Want to set a time slot? (Default All day long)", "classified-listing"); ?>
                                </label>
                            </div>
                            <input type="checkbox" name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][open]"
                                   value="1"
                                   class="form-check-input check-time-slot"
                                   id="bho-time-slot-open-<?php echo esc_attr($count) ?>">
                            <div class="action-item time-slots">
                                <div class="time-slot">
                                    <div class="time-slot-start">
                                        <input type="text"
                                               name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][times][0][start]"
                                               class="shs bhs-timepicker">
                                    </div>
                                    <div class="time-slot-end">
                                        <input type="text"
                                               name="_rtcl_special_bhs[<?php echo esc_attr($count) ?>][times][0][end]"
                                               class="shs bhs-timepicker">
                                    </div>
                                    <div class="time-slot-action">
                                        <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-plus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item-actions">
                            <i class="rtcl-bhs-btn rtcl-icon rtcl-icon-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
