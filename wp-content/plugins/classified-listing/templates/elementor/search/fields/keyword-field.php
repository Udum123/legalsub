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

<?php if ( $settings['keyword_field'] ) : ?>
	<div class="form-group ws-item ws-text col-sm-6">
		<div class="rt-autocomplete-wrapper">
			<?php
			$keywords = isset( $_GET['q'] ) ? Functions::clean( wp_unslash( ( $_GET['q'] ) ) ) : '';
			?>
			<?php if ( $settings['fields_label'] ) { ?>
				<label for="rtcl-search-keyword">
					<?php esc_html_e( 'Keyword', 'classified-listing' ); ?></label>
			<?php } ?>
			<div class="keywords-field-wrapper">
				<input type="text" name="q" data-type="listing" class="rtcl-autocomplete form-control" placeholder="<?php esc_attr_e( 'Enter your keyword here ...', 'classified-listing' ); ?>" value="<?php echo esc_attr( $keywords ); ?>">
			</div>
		</div>
	</div>
<?php endif; ?>