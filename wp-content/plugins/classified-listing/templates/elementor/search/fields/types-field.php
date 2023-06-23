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

use Rtcl\Helpers\Functions;

?>

<?php if ( $settings['types_field'] ) : ?>
	<div class="form-group ws-item ws-type col-sm-6 col-12">
		<?php if ( $settings['fields_label'] ) { ?>
			<label for="rtcl-search-type-<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Type', 'classified-listing' ); ?></label>
		<?php } ?>
		<select class="form-control" id="rtcl-search-type-<?php echo esc_attr( $id ); ?>" name="filters[ad_type]">
			<option value=""><?php esc_html_e( 'Select type', 'classified-listing' ); ?></option>
			<?php
			$listing_types = Functions::get_listing_types();
			if ( ! empty( $listing_types ) ) {
				foreach ( $listing_types as $key => $listing_type ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php echo isset( $_GET['filters']['ad_type'] ) && trim( $_GET['filters']['ad_type'] ) == $key ? ' selected' : null; ?>><?php echo esc_html( $listing_type ); ?></option>
					<?php
				}
			}
			?>
		</select>
	</div>
<?php endif; ?>