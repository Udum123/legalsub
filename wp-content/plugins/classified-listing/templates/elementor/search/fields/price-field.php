<?php
/**
 * @var number  $id    Random id
 * @var         $settings
 * @var         $widget_base
 * @var         $orientation
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


?>

<?php if ( $settings['price_field'] ) : ?>
	<div class="form-group  ws-item ws-price  price-field col-md-6 col-xs-6">
		<?php if ( $settings['fields_label'] ) { ?>
			<label for="rtcl-search-price-min"><?php esc_html_e( 'Min Price', 'classified-listing' ); ?></label>
		<?php } ?>
		<?php $min_price = isset( $_GET['filters']['price']['min'] ) ? $_GET['filters']['price']['min'] : ''; ?>
		<input id='rtcl-search-price-min' type="text" name="filters[price][min]" class="form-control" placeholder="<?php esc_attr_e( 'min', 'classified-listing' ); ?>" value="<?php echo esc_attr( $min_price ); ?>">
	</div>
	<div class="form-group ws-item ws-price  price-field col-md-6 col-xs-6">
		<?php if ( $settings['fields_label'] ) { ?>
			<label for="rtcl-search-price-max"><?php esc_html_e( 'Max Price', 'classified-listing' ); ?></label>
		<?php } ?>
		<?php $max_price = isset( $_GET['filters']['price']['max'] ) ? $_GET['filters']['price']['max'] : ''; ?>
		<input id='rtcl-search-price-max' type="text" name="filters[price][max]" class="form-control" placeholder="<?php esc_attr_e( 'max', 'classified-listing' ); ?>" value="<?php echo esc_attr( $max_price ); ?>" >
	</div>
<?php endif; ?>