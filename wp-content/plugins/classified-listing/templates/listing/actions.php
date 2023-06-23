<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var boolean $can_add_favourites
 * @var boolean $can_report_abuse
 * @var boolean $social
 * @var integer $listing_id
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;


if ( ! $can_add_favourites && ! $can_report_abuse && ! $social ) {
	return;
}
?>
    <ul class='list-group list-group-flush rtcl-single-listing-action'>
		<?php do_action( 'rtcl_single_action_before_list_item', $listing_id ); ?>
		<?php if ( $can_add_favourites ): ?>
            <li class="list-group-item"
                id="rtcl-favourites"><?php echo Functions::get_favourites_link( $listing_id ); ?></li>
		<?php endif; ?>
		<?php if ( $can_report_abuse ): ?>
            <li class='list-group-item'>
				<?php if ( is_user_logged_in() ): ?>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#rtcl-report-abuse-modal"><span
                                class='rtcl-icon rtcl-icon-trash'></span><?php echo esc_html( Text::report_abuse() ); ?>
                    </a>
				<?php else: ?>
                    <a href="javascript:void(0)" class="rtcl-require-login"><span
                                class='rtcl-icon rtcl-icon-trash'></span><?php echo esc_html( Text::report_abuse() ); ?>
                    </a>
				<?php endif; ?>
            </li>
		<?php endif; ?>
		<?php do_action( 'rtcl_single_action_after_list_item', $listing_id ); ?>
		<?php if ( $social ): ?>
            <li class="list-group-item rtcl-sidebar-social">
				<?php echo wp_kses_post( $social ); ?>
            </li>
		<?php endif; ?>
    </ul>

<?php do_action( 'rtcl_single_listing_after_action', $listing_id ); ?>

<?php if ( $can_report_abuse ) { ?>
    <div class="modal fade rtcl-bs-modal" id="rtcl-report-abuse-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="rtcl-report-abuse-form" class="form-vertical">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="rtcl-report-abuse-modal-label"><?php esc_html_e( 'Report Abuse', 'classified-listing' ); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label
                                    for="rtcl-report-abuse-message"><?php esc_html_e( 'Your Complaint', 'classified-listing' ); ?>
                                <span class="rtcl-star">*</span></label>
                            <textarea name="message" class="form-control" id="rtcl-report-abuse-message" rows="3"
                                      placeholder="<?php esc_attr_e( 'Message... ', 'classified-listing' ); ?>"
                                      required></textarea>
                        </div>
                        <div id="rtcl-report-abuse-g-recaptcha"></div>
                        <div id="rtcl-report-abuse-message-display"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit"
                                class="btn btn-primary"><?php esc_html_e( 'Submit', 'classified-listing' ); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>