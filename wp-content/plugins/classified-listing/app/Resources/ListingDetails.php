<?php

namespace Rtcl\Resources;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use WP_Post;

class ListingDetails {

	static function static_report( $post = null ) {
		$moderator_notification = absint( get_post_meta( $post->ID, '_notification_by_moderator', true ) );
		$visitor_notification   = absint( get_post_meta( $post->ID, '_notification_by_visitor', true ) );
		$aReport_notification   = absint( get_post_meta( $post->ID, '_abuse_report_by_visitor', true ) );
		?>
		<div class="rtcl-action-wrap">
			<div class="send-user-notification">
				<a id="send-email-to-user"
				   class="button button-primary button-large"><?php _e( "Send Email to User", 'classified-listing' ) ?></a>
			</div>
		</div>
		<div class="rtcl-report-wrap">
			<ul>
				<li><?php _e( "Notification by Moderator", 'classified-listing' ) ?>:
					<strong><?php echo $moderator_notification; ?></strong>
				</li>
				<li><?php _e( "Notification by Visitor", 'classified-listing' ) ?>:
					<strong><?php echo $visitor_notification; ?></strong></li>
				<li><?php _e( "Abuse Report by Visitor", 'classified-listing' ) ?>:
					<strong><?php echo $aReport_notification; ?></strong></li>
			</ul>
		</div>
		<?php
	}

	static function listing_details( $post = null ) {
		$listing          = rtcl()->factory->get_listing( $post->ID );
		$ad_type_selected = Functions::is_ad_type_disabled() ? null : $listing->get_ad_type();
		$price_type       = get_post_meta( $post->ID, 'price_type', true );
		$listing_pricing  = get_post_meta( $post->ID, '_rtcl_listing_pricing', true );
		$listing_pricing  = in_array( $listing_pricing, array_keys( Options::get_listing_pricing_types() ) ) ? $listing_pricing : 'price';
		$category_id      = 0;
		if ( ! Functions::is_ad_type_disabled() ):
			?>
			<div class="form-group">
				<label for="rtcl-ad-type"
					   class="col-form-label"><?php esc_html_e( 'Listing Type', 'classified-listing' ); ?>
					<span class="require-star">*</span></label>
				<select class="rtcl-select2 form-control" id="rtcl-ad-type" name="ad_type" required>
					<option value=""><?php esc_html_e( "Select a type", "classified-listing" ) ?></option>
					<?php
					$adTypes = Functions::get_listing_types();
					foreach ( $adTypes as $ad_type_id => $ad_type ) {
						$slt          = $ad_type_id === $ad_type_selected ? ' selected' : null;
						$ad_type_text = esc_html( $ad_type );
						echo "<option value='{$ad_type_id}'{$slt}>{$ad_type_text}</option>";
					}
					?>
				</select>
			</div>
		<?php endif; ?>
		<div class="form-group">
			<label for="rtcl-category"
				   class="rtcl-from-label"><?php esc_html_e( 'Category', 'classified-listing' ); ?><span
					class="require-star">*</span></label>
			<div id="rtcl-category-wrap">
				<?php
				$selected_cat_ids    = $listing->get_ancestors_category_ids_with_last_child();
				$parents_cats        = Functions::get_one_level_categories( 0, $ad_type_selected );
				$parent_cat_id       = 0;
				$current_category    = $listing->get_current_selected_category();
				$current_category_id = $current_category ? $current_category->term_id : null;
				?>
				<select class="form-control" name="rtcl-category-of-type" id="rtcl-category-of-type" required>
					<option value=""><?php echo esc_html( Text::get_select_category_text() ) ?></option>
					<?php
					if ( ! empty( $parents_cats ) ) {
						foreach ( $parents_cats as $cat ) {
							$slt = '';
							if ( in_array( $cat->term_id, $selected_cat_ids ) ) {
								$slt           = ' selected';
								$parent_cat_id = $cat->term_id;
							}
							echo "<option value='{$cat->term_id}'{$slt}>{$cat->name}</option>";
						}
					}
					?>
				</select>
				<?php
				while ( $parent_cat_id > 0 ) {
					$cats          = Functions::get_one_level_categories( $parent_cat_id );
					$old_cat       = $parent_cat_id;
					$parent_cat_id = 0;
					if ( ! empty( $cats ) ) {
						echo '<select class="form-control" id="rtcl-category-of-' . $old_cat . '" name="rtcl-category-of-' . $old_cat . '" required>';
						echo '<option value="">' . esc_html( Text::get_select_category_text() ) . '</option>';
						$parent_cat_id = 0;
						foreach ( $cats as $cat ) {
							$slt = '';
							if ( in_array( $cat->term_id, $selected_cat_ids ) ) {
								$slt           = ' selected';
								$parent_cat_id = $cat->term_id;
							}
							echo "<option value='{$cat->term_id}'{$slt}>{$cat->name}</option>";
						}
						echo '</select>';
					}
				}
				?>

			</div>
			<input type="hidden" value="<?php echo esc_attr( $current_category_id ); ?>" name="rtcl_category"
				   id="rtcl-category-input">
		</div>
		<?php if ( ! Functions::is_price_disabled() ):
			$listingPricingTypes = Options::get_listing_pricing_types();
			?>
			<div id="rtcl-pricing-wrap">
				<div class="rtcl-form-group">
					<label class="rtcl-from-label"><?php esc_html_e( "Pricing:", "classified-listing" ); ?></label>
					<div class="rtcl-checkbox-list rtcl-checkbox-inline rtcl-listing-pricing-types">
						<?php
						foreach ( $listingPricingTypes as $type_id => $type ) {
							?>
							<div class="rtcl-checkbox rtcl-listing-pricing-type">
								<input type="radio" name="_rtcl_listing_pricing"
									   id="_rtcl_listing_pricing_<?php echo esc_attr( $type_id ) ?>"
									<?php echo $listing_pricing === $type_id ? 'checked' : '' ?>
									   value="<?php echo esc_attr( $type_id ) ?>">
								<label for="_rtcl_listing_pricing_<?php echo esc_attr( $type_id ) ?>">
									<?php echo esc_html( $type ); ?>
								</label>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div id="rtcl-pricing-items" class="<?php echo esc_attr( 'rtcl-pricing-' . $listing_pricing ) ?>">
					<?php if ( ! Functions::is_price_type_disabled() ): ?>
						<div class="form-group rtcl-pricing-item rtcl-form-group">
							<label for="rtcl-price-type">
								<?php esc_html_e( 'Price Type', 'classified-listing' ); ?>
								<span class="require-star">*</span>
							</label>
							<select class="form-control" id="rtcl-price-type" name="price_type">
								<?php
								$price_types = Options::get_price_types();
								foreach ( $price_types as $key => $type ) {
									$slt = $price_type == $key ? " selected" : null;
									echo "<option value='{$key}'{$slt}>{$type}</option>";
								}
								?>
							</select>
						</div>
					<?php endif; ?>
					<?php do_action( 'rtcl_listing_form_price_items', $listing ); ?>
					<div id="rtcl-price-items"
						 class="rtcl-pricing-item<?php echo ! Functions::is_price_type_disabled() ? ' rtcl-price-type-' . esc_attr( $price_type ) : '' ?>">
						<div class="form-group rtcl-price-item" id="rtcl-price-wrap">
							<div class="price-wrap">
								<label
									for="rtcl-price"><?php echo sprintf( '<span class="price-label">%s [<span class="rtcl-currency-symbol">%s</span>]</span>',
										__( "Price", 'classified-listing' ),
										apply_filters( 'rtcl_listing_price_currency_symbol', Functions::get_currency_symbol(), $listing )
									); ?>
									<span
										class="require-star">*</span></label>
								<input type="text"
									   class="form-control rtcl-price"
									   value="<?php echo $listing ? esc_attr( $listing->get_price() ) : ''; ?>"
									   name="price"
									   id="rtcl-price"<?php echo esc_attr( ! $price_type || $price_type == 'fixed' ? " required" : '' ) ?>>
							</div>
							<div class="price-wrap rtcl-max-price rtcl-hide">
								<label
									for="rtcl-max-price"><?php echo sprintf( '<span class="price-label">%s [<span class="rtcl-currency-symbol">%s</span>]</span>',
										__( "Max Price", 'classified-listing' ),
										apply_filters( 'rtcl_listing_price_currency_symbol', Functions::get_currency_symbol(), $listing )
									); ?><span
										class="require-star">*</span></label>
								<input type="text"
									   class="form-control rtcl-price"
									   value="<?php echo $listing ? esc_attr( $listing->get_max_price() ) : ''; ?>"
									   name="_rtcl_max_price"
									   id="rtcl-max-price"<?php echo esc_attr( ! $price_type || $price_type == 'fixed' ? " required" : '' ) ?>>
							</div>
						</div>
						<?php do_action( 'rtcl_listing_form_price_unit', $listing, $category_id ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div id="rtcl-custom-fields-list" data-post_id="<?php echo $post->ID; ?>">
			<?php do_action( 'wp_ajax_rtcl_custom_fields_listings', $post->ID, $current_category_id ); ?>
		</div>
		<?php
	}

	/**
	 * @param WP_Post $post
	 */
	static function contact_details( WP_Post $post ) {
		$post_id = $post->ID;
		$data    = [
			'post_id'                    => $post_id,
			'state_text'                 => Text::location_level_first(),
			'city_text'                  => Text::location_level_second(),
			'town_text'                  => Text::location_level_third(),
			'selected_locations'         => wp_get_object_terms( $post_id, rtcl()->location, [ 'fields' => 'ids' ] ),
			'latitude'                   => get_post_meta( $post_id, 'latitude', true ),
			'longitude'                  => get_post_meta( $post_id, 'longitude', true ),
			'zipcode'                    => get_post_meta( $post_id, 'zipcode', true ),
			'address'                    => get_post_meta( $post_id, 'address', true ),
			'geo_address'                => get_post_meta( $post_id, '_rtcl_geo_address', true ),
			'phone'                      => get_post_meta( $post_id, 'phone', true ),
			'whatsapp_number'            => get_post_meta( $post_id, '_rtcl_whatsapp_number', true ),
			'email'                      => get_post_meta( $post_id, 'email', true ),
			'website'                    => get_post_meta( $post_id, 'website', true ),
			'location_id'                => 0,
			'sub_location_id'            => 0,
			'sub_sub_location_id'        => 0,
			'hidden_fields'              => Functions::get_option_item( 'rtcl_moderation_settings', 'hide_form_fields', [] ),
			'enable_post_for_unregister' => ! is_user_logged_in() && Functions::is_enable_post_for_unregister()
		];

		if ( 'local' != Functions::location_type() ) {
			$data['selected_locations'] = [];
		}
		Functions::get_template( "listing-form/contact", apply_filters( 'rtcl_listing_form_contact_tpl_attributes', $data, $post_id ) );
	}

	/**
	 * @param \WP_Post $post
	 */
	static function video_urls_box( $post ) {

		$video_urls = get_post_meta( $post->ID, '_rtcl_video_urls', true );
		$video_urls = ! empty( $video_urls ) && is_array( $video_urls ) ? $video_urls : [];
		$post_id    = $post->ID;
		Functions::get_template( "listing-form/video-urls", compact( 'post_id', 'video_urls' ) );
	}

}
