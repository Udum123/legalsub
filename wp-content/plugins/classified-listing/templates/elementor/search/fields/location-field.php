<?php
/**
 * @var number  $id    Random id
 * @var         $settings
 * @var         $widget_base
 * @var         $orientation
 * @var         $orderby
 * @var         $order
 * @var         $style [classic , modern]
 * @var array   $classes
 * @var int     $active_count
 * @var WP_Term $selected_location
 * @var WP_Term $selected_category
 * @var bool    $radius_search
 * @var bool    $can_search_by_location
 * @var bool    $can_search_by_category
 * @var array   $data
 * @var bool    $can_search_by_listing_types
 * @var bool    $can_search_by_price
 */


use Rtcl\Helpers\Text;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

$geo_location = ( $settings['location_field'] && 'geo' === Functions::location_type() );
if ( $geo_location ) :
	$rs_data = Options::radius_search_options();
	?>
	<div class="form-group ws-item ws-location rtcl-geo-address-field col-sm-6 ">
		<?php if ( $settings['fields_label'] ) { ?>
			<label for="rtc-geo-search"><?php esc_html_e( 'Location', 'classified-listing' ); ?></label>
		<?php } ?>
		<div class="rtc-geo-search-wrapper">
			<input id='rtc-geo-search' type="text" name="geo_address" autocomplete="off" value="<?php echo ! empty( $_GET['geo_address'] ) ? esc_attr( $_GET['geo_address'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Select a location', 'classified-listing' ); ?>" class="form-control rtcl-geo-address-input"/>
			<i class="rtcl-get-location rtcl-icon rtcl-icon-target"></i>
			<input type="hidden" class="latitude" name="center_lat" value="<?php echo ! empty( $_GET['center_lat'] ) ? esc_attr( $_GET['center_lat'] ) : ''; ?>">
			<input type="hidden" class="longitude" name="center_lng" value="<?php echo ! empty( $_GET['center_lng'] ) ? esc_attr( $_GET['center_lng'] ) : ''; ?>">
		</div>
	</div>
	<?php if ( isset( $settings['geo_location_range'] ) && $settings['geo_location_range'] ) { ?>
		<div class=" form-group ws-item ws-location rtcl-range-slider-field col-sm-6 ">
			<?php
			$radius_placeholder = sprintf(
				__( 'Radius (%1$s)', 'classified-listing' ),
				isset( $rs_data['units'] ) ? $rs_data['units'] : ''
			);
			?>
			<?php if ( $settings['fields_label'] ) { ?>
				<label for="rtc-geo-search"><?php echo esc_html( $radius_placeholder ); ?></label>
			<?php } ?>
	
			<input type="number" class="form-control-range rtcl-range-slider-input form-control" title='<?php echo esc_attr( $radius_placeholder ); ?>' placeholder="<?php echo esc_attr( $radius_placeholder ); ?>" name="distance" max="<?php echo absint( $rs_data['max_distance'] ); ?>" value="<?php echo absint( isset( $_GET['distance'] ) ? $_GET['distance'] :  $rs_data['default_distance'] ); ?>">
		</div>
	<?php } ?>
<?php elseif ( $settings['location_field'] && 'local' === Functions::location_type() ) : ?>
	<div class="form-group ws-item ws-location col-sm-6 col-12">
		<?php if ( $settings['fields_label'] ) { ?>
			<label for="rtcl-search-location-<?php echo esc_attr( $id ); ?>"> <?php esc_html_e( 'Location', 'classified-listing' ); ?> </label>
		<?php } ?>
		<?php if ( $style === 'suggestion' ) { ?>
			<div class="location-field-wrapper">
				<input type="text" data-type="location" class="rtcl-autocomplete rtcl-location form-control" placeholder="<?php echo esc_html( Text::get_select_location_text() ); ?>" value="<?php echo $selected_location ? $selected_location->name : ''; ?>">
				<input type="hidden" name="rtcl_location" value="<?php echo $selected_location ? $selected_location->slug : ''; ?>">
			</div>
			<?php
		} elseif ( $style === 'standard' ) {
			$location_args = array(
				'show_option_none'  => Text::get_select_location_text(),
				'option_none_value' => '',
				'taxonomy'          => rtcl()->location,
				'name'              => 'rtcl_location',
				'id'                => 'rtcl-location-search-' . $id,
				'class'             => 'form-control rtcl-location-search',
				'selected'          => get_query_var( 'rtcl_location' ),
				'hierarchical'      => true,
				'value_field'       => 'slug',
				'depth'             => Functions::get_location_depth_limit(),
				'orderby'           => $orderby,
				'order'             => ( 'DESC' === $order ) ? 'DESC' : 'ASC',
				'show_count'        => false,
				'hide_empty'        => false,
			);
			if ( '_rtcl_order' === $orderby ) {
				$location_args['orderby']  = 'meta_value_num';
				$location_args['meta_key'] = '_rtcl_order';
			}
			wp_dropdown_categories( $location_args );
		} elseif ( $style === 'dependency' ) {
			Functions::dropdown_terms(
				array(
					'show_option_none' => Text::get_select_location_text(),
					'taxonomy'         => rtcl()->location,
					'name'             => 'l',
					'class'            => 'form-control',
					'selected'         => $selected_location ? $selected_location->term_id : 0,
				)
			);
		} elseif ( $style == 'popup' ) {
			?>
			<div class="rtcl-search-input-button form-control rtcl-search-input-location ">
					<span class="search-input-label location-name">
					<?php echo $selected_location ? esc_html( $selected_location->name ) : esc_html( Text::get_select_location_text() ); ?>
					</span>
				<input type="hidden" class="rtcl-term-field" name="rtcl_location" value="<?php echo $selected_location ? esc_attr( $selected_location->slug ) : ''; ?>">
			</div>
		<?php } ?>
	</div>
<?php endif; ?>