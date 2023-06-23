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
<div class="form-group ws-item ws-button  col-sm-6">
	<?php if ( $settings['fields_label'] ) { ?>
		<label for="rtcl-search-button"><?php esc_html_e( 'Submit button', 'classified-listing' ); ?></label>
	<?php } ?>
	<div class="rtcl-action-buttons button-<?php echo ! empty( $settings['button_alignment'] ) ? esc_attr( $settings['button_alignment'] ) : 'left'; ?>">
		<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Search', 'classified-listing' ); ?></button>
	</div>
</div>