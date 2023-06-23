<?php
/**
 * Listing Form Contact
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var int $post_id
 * @var array $hidden_fields
 * @var string $state_text
 * @var string $city_text
 * @var string $town_text
 * @var string $zipcode
 * @var string $phone
 * @var string $whatsapp_number
 * @var boolean $enable_post_for_unregister
 * @var string $website
 * @var bool $latitude
 * @var bool $longitude
 * @var string $email
 * @var string $address
 * @var string $geo_address
 * @var integer $selected_locations
 */

use Rtcl\Helpers\Functions;

?>
<div class="rtcl-post-contact-details rtcl-post-section<?php echo esc_attr( is_admin() ? " rtcl-is-admin" : '' ) ?>">
    <div class="rtcl-post-section-title">
        <h3>
            <i class="rtcl-icon rtcl-icon-users"></i><?php esc_html_e( "Contact Details", "classified-listing" ); ?>
        </h3>
    </div>
    <div class="row">
		<?php if ( 'local' === Functions::location_type() ) : ?>
            <div class="col-md-6">
				<?php if ( ! in_array( 'location', $hidden_fields ) ): ?>
                    <div class="form-group" id="rtcl-location-row">
                        <label for='rtcl-location'><?php echo esc_html( $state_text ); ?><span
                                    class="require-star">*</span></label>
                        <select id="rtcl-location" name="location"
                                class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
                            <option value="">--<?php esc_html_e( 'Select location', 'classified-listing' ) ?>--</option>
							<?php
							$locations = Functions::get_one_level_locations();
							if ( ! empty( $locations ) ) {
								foreach ( $locations as $location ) {
									$slt = '';
									if ( in_array( $location->term_id, $selected_locations ) ) {
										$location_id = $location->term_id;
										$slt         = " selected";
									}
									echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
								}
							}
							?>
                        </select>
                    </div>
					<?php
					$sub_locations = [];
					if ( $location_id ) {
						$sub_locations = Functions::get_one_level_locations( $location_id );
					}
					?>
                    <div class="form-group<?php echo empty( $sub_locations ) ? ' rtcl-hide' : ''; ?>"
                         id="sub-location-row">
                        <label for='rtcl-sub-location'><?php echo esc_html( $city_text ) ?><span
                                    class="require-star">*</span></label>
                        <select id="rtcl-sub-location" name="sub_location"
                                class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
                            <option value="">--<?php esc_html_e( 'Select location', 'classified-listing' ) ?>--</option>
							<?php
							if ( ! empty( $sub_locations ) ) {
								foreach ( $sub_locations as $location ) {
									$slt = '';
									if ( in_array( $location->term_id, $selected_locations ) ) {
										$sub_location_id = $location->term_id;
										$slt             = " selected";
									}
									echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
								}
							}
							?>
                        </select>
                    </div>
					<?php
					$sub_sub_locations = [];
					if ( $sub_location_id ) {
						$sub_sub_locations = Functions::get_one_level_locations( $sub_location_id );
					}
					?>
                    <div class="form-group<?php echo empty( $sub_sub_locations ) ? ' rtcl-hide' : ''; ?>"
                         id="sub-sub-location-row">
                        <label for='rtcl-sub-sub-location'><?php echo esc_html( $town_text ) ?>
                            <span class="require-star">*</span></label>
                        <select id="rtcl-sub-sub-location" name="sub_sub_location"
                                class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
                            <option value="">--<?php esc_html_e( 'Select location', 'classified-listing' ) ?>--</option>
							<?php
							if ( ! empty( $sub_sub_locations ) ) {
								foreach ( $sub_sub_locations as $location ) {
									$slt = '';
									if ( in_array( $location->term_id, $selected_locations ) ) {
										$slt = " selected";
									}
									echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
								}
							}
							?>
                        </select>
                    </div>
				<?php endif; ?>
				<?php if ( ! in_array( 'zipcode', $hidden_fields ) ): ?>
                    <div class="form-group">
                        <label for="rtcl-zipcode"><?php esc_html_e( "Zip Code", "classified-listing" ) ?></label>
                        <input type="text" name="zipcode" value="<?php echo esc_attr( $zipcode ); ?>"
                               class="rtcl-map-field form-control" id="rtcl-zipcode"/>
                    </div>
				<?php endif; ?>
				<?php if ( ! in_array( 'address', $hidden_fields ) ): ?>
                    <div class="form-group">
                        <label for="rtcl-address"><?php esc_html_e( "Address", "classified-listing" ) ?></label>
                        <textarea name="address" rows="2" class="rtcl-map-field form-control"
                                  id="rtcl-address"><?php echo esc_textarea( $address ); ?></textarea>
                    </div>
				<?php endif; ?>
            </div>
		<?php endif; ?>
        <div class="col-md-6">
			<?php if ( 'geo' === Functions::location_type() ) : ?>
                <div class="form-group">
                    <label for="rtcl-geo-address"><?php esc_html_e( "Location", "classified-listing" ) ?></label>
                    <div class="rtcl-geo-address-field">
                        <input type="text" name="rtcl_geo_address" autocomplete="off"
                               value="<?php echo esc_attr( $geo_address ) ?>"
                               id="rtcl-geo-address"
                               placeholder="<?php esc_attr_e( "Select a location", "classified-listing" ) ?>"
                               class="form-control rtcl-geo-address-input rtcl_geo_address_input"/>
                        <i class="rtcl-get-location rtcl-icon rtcl-icon-target" id="rtcl-geo-loc-form"></i>
                    </div>
                </div>
			<?php endif; ?>
			<?php if ( ! in_array( 'phone', $hidden_fields ) ):
				$phoneIsRequired = Functions::listingFormPhoneIsRequired();
				?>
                <div class="form-group">
                    <label for="rtcl-phone">
						<?php esc_html_e( "Phone", "classified-listing" ) ?>
						<?php if ( $phoneIsRequired ) { ?><span class="rtcl-required">*</span> <?php } ?>
                    </label>
					<?php
					$required_attr = $phoneIsRequired ? 'required' : '';
					$field         = '<input type="text" name="phone" id="rtcl-phone" value="' . esc_attr( $phone ) . '" class="form-control" ' . esc_attr( $required_attr ) . '/>';
					Functions::print_html( apply_filters( 'rtcl_verification_listing_form_phone_field', $field, $phone ), true );
					?>
					<?php do_action( 'rtcl_listing_form_phone_warning' ); ?>
                </div>
			<?php endif; ?>
			<?php if ( ! in_array( 'whatsapp_number', $hidden_fields ) ): ?>
                <div class="form-group">
                    <label
                            for="rtcl-whatsapp-number"><?php esc_html_e( "Whatsapp number", "classified-listing" ) ?></label>
                    <input type="text" class="form-control" id="rtcl-whatsapp-number" name="_rtcl_whatsapp_number"
                           value="<?php echo esc_attr( $whatsapp_number ); ?>"/>
                    <p class="description small"><?php esc_html_e( "Whatsapp number with your country code. e.g.+1xxxxxxxxxx", 'classified-listing' ) ?></p>
                </div>
			<?php endif; ?>
			<?php if ( ! in_array( 'email', $hidden_fields ) || $enable_post_for_unregister ): ?>
                <div class="form-group">
                    <label for="rtcl-email">
						<?php esc_html_e( "Email", "classified-listing" ) ?><?php if ( $enable_post_for_unregister ): ?>
                            <span
                                    class="require-star">*</span><?php endif; ?></label>
                    <input type="email" class="form-control" id="rtcl-email" name="email"
                           value="<?php echo esc_attr( $email ); ?>"<?php echo $enable_post_for_unregister ? " required" : '' ?> />
					<?php if ( $enable_post_for_unregister ): ?>
                        <p class="description small"><?php esc_html_e( "This will be your user name.", 'classified-listing' ) ?></p>
					<?php endif; ?>
                </div>
			<?php endif; ?>
			<?php if ( ! in_array( 'website', $hidden_fields ) ): ?>
                <div class="form-group">
                    <label for="rtcl-website"><?php esc_html_e( "Website", "classified-listing" ) ?></label>
                    <input type="url" class="form-control" id="rtcl-website" value="<?php echo esc_url( $website ); ?>"
                           name="website"/>
                    <p class="description small"><?php esc_html_e( "e.g. https://example.com", 'classified-listing' ) ?></p>
                </div>
			<?php endif; ?>
        </div>
    </div>
	<?php do_action( 'rtcl_listing_form_template_contact_end', $post_id ); ?>
</div>
