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

?>

<?php if ( $settings['category_field'] ) : ?>
	<div class="form-group ws-item ws-category ws-category-<?php echo esc_attr( $style ); ?> col-sm-6 col-12">
		<?php if ( $settings['fields_label'] ) { ?>
			<label><?php esc_html_e( 'Category', 'classified-listing' ); ?></label>
		<?php } ?>
		<?php
		if ( $style === 'standard' || $style === 'suggestion' ) {
			$cat_args = array(
				'show_option_none'  => Text::get_select_category_text(),
				'option_none_value' => '',
				'taxonomy'          => rtcl()->category,
				'name'              => 'rtcl_category',
				'id'                => 'rtcl-category-search-' . $id,
				'class'             => 'form-control rtcl-category-search',
				'selected'          => get_query_var( 'rtcl_category' ),
				'hierarchical'      => true,
				'value_field'       => 'slug',
				'depth'             => Functions::get_category_depth_limit(),
				'orderby'           => $orderby,
				'order'             => ( 'DESC' === $order ) ? 'DESC' : 'ASC',
				'show_count'        => false,
				'hide_empty'        => false,
			);
			if ( '_rtcl_order' === $orderby ) {
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_rtcl_order';
			}
			wp_dropdown_categories( $cat_args );
		} elseif ( $style === 'dependency' ) {
			Functions::dropdown_terms(
				array(
					'show_option_none'  => Text::get_select_category_text(),
					'option_none_value' => -1,
					'taxonomy'          => rtcl()->category,
					'name'              => 'c',
					'class'             => 'form-control rtcl-category-search',
					'selected'          => $selected_category ? $selected_category->term_id : 0,
				)
			);
		} elseif ( $style == 'popup' ) {
			?>
			<div class="rtcl-search-input-button form-control  rtcl-search-input-category ">
							<span class="search-input-label category-name">
								<?php echo $selected_category ? esc_html( $selected_category->name ) : esc_html( Text::get_select_category_text() ); ?>
							</span>
				<input type="hidden" name="rtcl_category" class="rtcl-term-field" value="<?php echo $selected_category ? esc_attr( $selected_category->slug ) : ''; ?>">
			</div>
		<?php } ?>
	</div>
<?php endif; ?>