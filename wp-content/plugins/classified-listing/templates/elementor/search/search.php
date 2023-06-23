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

use Rtcl\Helpers\Text;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

$orderby   = strtolower( Functions::get_option_item( 'rtcl_general_settings', 'taxonomy_orderby', 'name' ) );
$order     = strtoupper( Functions::get_option_item( 'rtcl_general_settings', 'taxonomy_order', 'DESC' ) );
$classes[] = 'rtcl-elementor-widget-search';

$data = array(
	'template'              => 'elementor/search/fields',
	'id'                    => $id,
	'settings'              => $settings,
	'style'                 => $style,
	'orientation'           => $orientation,
	'selected_category'     => $selected_category,
	'selected_location'     => $selected_location,
	'orderby'     => $orderby,
	'order'     => $order,
	'default_template_path' => '',
);
$data = apply_filters( 'rtcl/elementor/search/data/' . $widget_base, $data );

?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<form action="<?php echo esc_url( Functions::get_filter_form_url() ); ?>" class=" rtcl-widget-search-form">
		<div class="row rtcl-no-margin active-field-<?php echo esc_attr( $active_count ); ?>  <?php echo ! empty( $settings['fields_label'] ) ? 'show-field-label' : ''; ?>">
			
			<?php Functions::get_template( 'elementor/search/fields/location-field', $data, '', $data['default_template_path'] );  ?>
			
			<?php Functions::get_template( 'elementor/search/fields/category-field', $data, '', $data['default_template_path'] );  ?>
			
			<?php Functions::get_template( 'elementor/search/fields/types-field', $data, '', $data['default_template_path'] );  ?>
			
			<?php Functions::get_template( 'elementor/search/fields/price-field', $data, '', $data['default_template_path'] );  ?>
			
			<?php Functions::get_template( 'elementor/search/fields/keyword-field', $data, '', $data['default_template_path'] );  ?>
			
			<?php Functions::get_template( 'elementor/search/fields/submit-button', $data, '', $data['default_template_path'] );  ?>
			
		</div>
		<?php do_action( 'rtcl_widget_search_' . $orientation . '_form', $settings ); ?>
	</form>
</div>
