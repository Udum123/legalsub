<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var WP_User $user
 * @var string $phone
 * @var string $whatsapp_number
 * @var string $website
 * @var string $geo_address
 * @var string $state_text
 * @var string $city_text
 * @var array $user_locations
 * @var int $sub_location_id
 * @var int $location_id
 * @var string $town_text
 * @var string $zipcode
 * @var float $latitude
 * @var float $longitude
 * @var int $pp_id
 */

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'rtcl_before_edit_account_form' ); ?>

<form class="rtcl-EditAccountForm form-horizontal" id="rtcl-user-account" method="post">

	<?php do_action( 'rtcl_edit_account_form_start' ); ?>

    <div class="form-group row">
        <label for="rtcl-username"
               class="col-sm-3 control-label"><?php esc_html_e( 'Username', 'classified-listing' ); ?></label>
        <div class="col-sm-9">
            <p class="form-control-static"><strong><?php echo esc_html( $user->user_login ); ?></strong></p>
        </div>
    </div>

    <div class="form-group row">
        <label for="rtcl-first-name"
               class="col-sm-3 control-label"><?php esc_html_e( 'First Name', 'classified-listing' ); ?></label>
        <div class="col-sm-9">
            <input type="text" name="first_name" id="rtcl-first-name"
                   value="<?php echo esc_attr( $user->first_name ); ?>"
                   class="form-control"/>
        </div>
    </div>

    <div class="form-group row">
        <label for="rtcl-last-name"
               class="col-sm-3 control-label"><?php esc_html_e( 'Last Name', 'classified-listing' ); ?></label>
        <div class="col-sm-9">
            <input type="text" name="last_name" id="rtcl-last-name" value="<?php echo esc_attr( $user->last_name ); ?>"
                   class="form-control"/>
        </div>
    </div>

    <div class="form-group row">
        <label for="rtcl-email"
               class="col-sm-3 control-label"><?php esc_html_e( 'E-mail Address', 'classified-listing' ); ?>
            <strong>*</strong></label>
        <div class="col-sm-9">
            <input type="email" name="email" id="rtcl-email" class="form-control"
                   value="<?php echo esc_attr( $user->user_email ); ?>" required="required"/>
        </div>
    </div>

    <div class="form-group row">
        <label for="rtcl-profile-picture" class="col-sm-3 control-label">
			<?php esc_html_e( 'Profile Picture', 'classified-listing' ); ?><strong>*</strong>
        </label>
        <div class="col-sm-9">
            <div class="rtcl-profile-picture-wrap">
				<?php if ( ! $pp_id ): ?>
                    <div class="rtcl-gravatar-wrap">
						<?php echo get_avatar( $user->ID );
						echo "<p>" . sprintf(
								__( '<a href="%s">Change on Gravatar</a>.', 'classified-listing' ),
								__( 'https://en.gravatar.com/', 'classified-listing' )
							) . "</p>";
						?>
                    </div>
				<?php endif; ?>
                <div class="rtcl-media-upload-wrap">
                    <div class="rtcl-media-upload rtcl-media-upload-pp<?php echo( $pp_id ? ' has-media' : ' no-media' ) ?>">
                        <div class="rtcl-media-action">
                            <span class="rtcl-icon-plus add"><?php esc_html_e( "Add Logo", "classified-listing" ); ?></span>
                            <span class="rtcl-icon-trash remove"><?php esc_html_e( "Delete Logo", "classified-listing" ); ?></span>
                        </div>
                        <div class="rtcl-media-item">
							<?php echo( $pp_id ? wp_get_attachment_image( $pp_id, [ 100, 100 ] ) : '' ) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-offset-3 col-sm-9">
            <div class="form-check">
                <input type="hidden" name="change_password" value="0">
                <input type="checkbox" name="change_password" class="form-check-input" id="rtcl-change-password"
                       value="1">
                <label class="form-check-label" for="rtcl-change-password">
					<?php esc_html_e( 'Change Password', 'classified-listing' ); ?>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row rtcl-password-fields" style="display: none;">
        <label for="password"
               class="col-sm-3 control-label"><?php esc_html_e( 'New Password', 'classified-listing' ); ?>
            <strong>*</strong></label>
        <div class="col-sm-9">
            <input type="password" name="pass1" id="password" class="form-control rtcl-password" autocomplete="off"
                   required="required"/>
        </div>
    </div>

    <div class="form-group row rtcl-password-fields" style="display: none">
        <label for="password_confirm"
               class="col-sm-3 control-label"><?php esc_html_e( 'Confirm Password', 'classified-listing' ); ?>
            <strong>*</strong></label>
        <div class="col-sm-9">
            <input type="password" name="pass2" id="password_confirm" class="form-control" autocomplete="off"
                   data-rule-equalTo="#password" data-msg-equalTo="<?php esc_attr_e( 'Password does not match.', 'classified-listing' ); ?>"
                   required/>
        </div>
    </div>

    <div class="form-group row">
        <label for="rtcl-phone"
               class="col-sm-3 control-label"><?php esc_html_e( 'Phone', 'classified-listing' ); ?></label>
        <div class="col-sm-9">
			<?php
			$phone = esc_attr( $phone );
			$field = "<input type='text' name='phone' id='rtcl-phone' value='{$phone}' class='form-control'/>";
			Functions::print_html( apply_filters( 'rtcl_edit_account_phone_field', $field, $phone ), true );
			?>
        </div>
    </div>
    <div class="form-group row">
        <label for="rtcl-last-name"
               class="col-sm-3 control-label"><?php esc_html_e( 'Whatsapp phone', 'classified-listing' ); ?></label>
        <div class="col-sm-9">
            <input type="text" name="whatsapp_number" id="rtcl-whatsapp-phone"
                   value="<?php echo esc_attr( $whatsapp_number ); ?>"
                   class="form-control"/>
        </div>
    </div>
    <div class="form-group row">
        <label for="rtcl-website"
               class="col-sm-3 control-label"><?php esc_html_e( 'Website', 'classified-listing' ); ?></label>
        <div class="col-sm-9">
            <input type="url" name="website" id="rtcl-website" value="<?php echo esc_attr( $website ); ?>"
                   class="form-control"/>
        </div>
    </div>
	<?php
	do_action( 'rtcl_edit_account_form' );

	do_action( 'rtcl_edit_account_form_end' );
	?>
</form>

<?php do_action( 'rtcl_after_edit_account_form' ); ?>
