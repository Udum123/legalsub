<?php

/**
 * @var number  $id    Random id
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

$orderby   = strtolower(Functions::get_option_item('rtcl_general_settings', 'taxonomy_orderby', 'name'));
$order     = strtoupper(Functions::get_option_item('rtcl_general_settings', 'taxonomy_order', 'DESC'));
$classes[] = 'rtcl-gb-widget-search';

$wrap_class = '';
if (isset($settings['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $settings['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($settings['className'])) {
	$wrap_class .= $settings['className'];
}

// echo '<pre>';
// //print_r($settings);
// echo '</pre>';
?>
<div class="<?php echo esc_attr($wrap_class); ?>">
	<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
		<form action="<?php echo esc_url(Functions::get_filter_form_url()); ?>" class=" rtcl-widget-search-form">
			<div class="row rtcl-no-margin active-field-<?php echo esc_attr($active_count); ?>  <?php echo !empty($settings['fields_label']) ? 'show-field-label' : ''; ?>">
				<?php
				$geo_location = ($settings['location_field'] && 'geo' === Functions::location_type());
				if ($geo_location) :
					$rs_data = Options::radius_search_options();
				?>
					<div class="form-group ws-item ws-location rtcl-geo-address-field col-sm-6 ">
						<?php if ($settings['fields_label']) { ?>
							<label for="rtc-geo-search"><?php esc_html_e('Location', 'classified-listing'); ?></label>
						<?php } ?>
						<div class="rtc-geo-search-wrapper">
							<input id='rtc-geo-search' type="text" name="geo_address" autocomplete="off" value="<?php echo !empty($_GET['geo_address']) ? esc_attr($_GET['geo_address']) : ''; ?>" placeholder="<?php esc_attr_e('Select a location', 'classified-listing'); ?>" class="form-control rtcl-geo-address-input" />
							<i class="rtcl-get-location rtcl-icon rtcl-icon-target"></i>
							<input type="hidden" class="latitude" name="center_lat" value="<?php echo !empty($_GET['center_lat']) ? esc_attr($_GET['center_lat']) : ''; ?>">
							<input type="hidden" class="longitude" name="center_lng" value="<?php echo !empty($_GET['center_lng']) ? esc_attr($_GET['center_lng']) : ''; ?>">
						</div>
					</div>
					<?php if (isset($settings['geo_location_range']) && $settings['geo_location_range']) { ?>
						<div class=" form-group ws-item ws-location rtcl-range-slider-field col-sm-6 ">
							<?php
							$radius_placeholder = sprintf(
								__('Radius (%1$s)', 'classified-listing'),
								isset($rs_data['units']) ? $rs_data['units'] : ''
							);
							?>
							<?php if ($settings['fields_label']) { ?>
								<label for="rtc-geo-search"><?php echo esc_html($radius_placeholder); ?></label>
							<?php } ?>

							<input type="number" class="form-control-range rtcl-range-slider-input form-control" title='<?php echo esc_attr($radius_placeholder); ?>' placeholder="<?php echo esc_attr($radius_placeholder); ?>" name="distance" max="<?php echo absint($rs_data['max_distance']); ?>" value="<?php echo absint(isset($_GET['distance']) ? $_GET['distance'] :  $rs_data['default_distance']); ?>">
						</div>
					<?php } ?>
				<?php
				elseif ($settings['location_field'] && 'local' === Functions::location_type()) :
				?>
					<div class="form-group ws-item ws-location col-sm-6 col-12">
						<?php if ($settings['fields_label']) { ?>
							<label for="rtcl-search-location-<?php echo esc_attr($id); ?>"> <?php esc_html_e('Location', 'classified-listing'); ?> </label>
						<?php } ?>
						<?php if ($style === 'suggestion') { ?>
							<div class="location-field-wrapper">
								<input type="text" data-type="location" class="rtcl-autocomplete rtcl-location form-control" placeholder="<?php echo esc_html(Text::get_select_location_text()); ?>" value="<?php echo $selected_location ? $selected_location->name : ''; ?>">
								<input type="hidden" name="rtcl_location" value="<?php echo $selected_location ? $selected_location->slug : ''; ?>">
							</div>
						<?php
						} elseif ($style === 'standard') {
							$location_args = array(
								'show_option_none'  => Text::get_select_location_text(),
								'option_none_value' => '',
								'taxonomy'          => rtcl()->location,
								'name'              => 'rtcl_location',
								'id'                => 'rtcl-location-search-' . $id,
								'class'             => 'form-control rtcl-location-search',
								'selected'          => get_query_var('rtcl_location'),
								'hierarchical'      => true,
								'value_field'       => 'slug',
								'depth'             => Functions::get_location_depth_limit(),
								'orderby'           => $orderby,
								'order'             => ('DESC' === $order) ? 'DESC' : 'ASC',
								'show_count'        => false,
								'hide_empty'        => false,
							);
							if ('_rtcl_order' === $orderby) {
								$location_args['orderby']  = 'meta_value_num';
								$location_args['meta_key'] = '_rtcl_order';
							}
							wp_dropdown_categories($location_args);
						} elseif ($style === 'dependency') {
							Functions::dropdown_terms(
								array(
									'show_option_none' => Text::get_select_location_text(),
									'taxonomy'         => rtcl()->location,
									'name'             => 'l',
									'class'            => 'form-control',
									'selected'         => $selected_location ? $selected_location->term_id : 0,
								)
							);
						} elseif ($style == 'popup') {
						?>
							<div class="rtcl-search-input-button form-control rtcl-search-input-location ">
								<span class="search-input-label location-name">
									<?php echo $selected_location ? esc_html($selected_location->name) : esc_html(Text::get_select_location_text()); ?>
								</span>
								<input type="hidden" class="rtcl-term-field" name="rtcl_location" value="<?php echo $selected_location ? esc_attr($selected_location->slug) : ''; ?>">
							</div>
						<?php } ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['category_field']) : ?>
					<div class="form-group ws-item ws-category ws-category-<?php echo esc_attr($style); ?> col-sm-6 col-12">
						<?php if ($settings['fields_label']) { ?>
							<label><?php esc_html_e('Category', 'classified-listing'); ?></label>
						<?php } ?>
						<?php
						if ($style === 'standard' || $style === 'suggestion') {
							$cat_args = array(
								'show_option_none'  => Text::get_select_category_text(),
								'option_none_value' => '',
								'taxonomy'          => rtcl()->category,
								'name'              => 'rtcl_category',
								'id'                => 'rtcl-category-search-' . $id,
								'class'             => 'form-control rtcl-category-search',
								'selected'          => get_query_var('rtcl_category'),
								'hierarchical'      => true,
								'value_field'       => 'slug',
								'depth'             => Functions::get_category_depth_limit(),
								'orderby'           => $orderby,
								'order'             => ('DESC' === $order) ? 'DESC' : 'ASC',
								'show_count'        => false,
								'hide_empty'        => false,
							);
							if ('_rtcl_order' === $orderby) {
								$args['orderby']  = 'meta_value_num';
								$args['meta_key'] = '_rtcl_order';
							}
							wp_dropdown_categories($cat_args);
						} elseif ($style === 'dependency') {
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
						} elseif ($style == 'popup') {
						?>
							<div class="rtcl-search-input-button form-control  rtcl-search-input-category ">
								<span class="search-input-label category-name">
									<?php echo $selected_category ? esc_html($selected_category->name) : esc_html(Text::get_select_category_text()); ?>
								</span>
								<input type="hidden" name="rtcl_category" class="rtcl-term-field" value="<?php echo $selected_category ? esc_attr($selected_category->slug) : ''; ?>">
							</div>
						<?php } ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['types_field']) : ?>
					<div class="form-group ws-item ws-type col-sm-6 col-12">
						<?php if ($settings['fields_label']) { ?>
							<label for="rtcl-search-type-<?php echo esc_attr($id); ?>"><?php esc_html_e('Type', 'classified-listing'); ?></label>
						<?php } ?>
						<select class="form-control" id="rtcl-search-type-<?php echo esc_attr($id); ?>" name="filters[ad_type]">
							<option value=""><?php esc_html_e('Select a type', 'classified-listing'); ?></option>
							<?php
							$listing_types = Functions::get_listing_types();
							if (!empty($listing_types)) {
								foreach ($listing_types as $key => $listing_type) {
							?>
									<option value="<?php echo esc_attr($key); ?>" <?php echo isset($_GET['filters']['ad_type']) && trim($_GET['filters']['ad_type']) == $key ? ' selected' : null; ?>><?php echo esc_html($listing_type); ?></option>
							<?php
								}
							}
							?>
						</select>
					</div>
				<?php endif; ?>

				<?php if ($settings['price_field']) : ?>
					<div class="form-group  ws-item ws-price  price-field col-md-6 col-xs-6">
						<?php if ($settings['fields_label']) { ?>
							<label for="rtcl-search-price-min"><?php esc_html_e('Min Price', 'classified-listing'); ?></label>
						<?php } ?>
						<?php $min_price = isset($_GET['filters']['price']['min']) ? $_GET['filters']['price']['min'] : ''; ?>
						<input id='rtcl-search-price-min' type="text" name="filters[price][min]" class="form-control" placeholder="<?php esc_attr_e('Min', 'classified-listing'); ?>" value="<?php echo esc_attr($min_price); ?>">
					</div>
					<div class="form-group ws-item ws-price  price-field col-md-6 col-xs-6">
						<?php if ($settings['fields_label']) { ?>
							<label for="rtcl-search-price-max"><?php esc_html_e('Max Price', 'classified-listing'); ?></label>
						<?php } ?>
						<?php $max_price = isset($_GET['filters']['price']['max']) ? $_GET['filters']['price']['max'] : ''; ?>
						<input id='rtcl-search-price-max' type="text" name="filters[price][max]" class="form-control" placeholder="<?php esc_attr_e('Max', 'classified-listing'); ?>" value="<?php echo esc_attr($max_price); ?>">
					</div>
				<?php endif; ?>
				<?php if ($settings['keyword_field']) : ?>
					<div class="form-group ws-item ws-text col-sm-6">
						<div class="rt-autocomplete-wrapper">
							<?php
							$keywords = isset($_GET['q']) ? Functions::clean(wp_unslash(($_GET['q']))) : '';
							?>
							<?php if ($settings['fields_label']) { ?>
								<label for="rtcl-search-keyword">
									<?php esc_html_e('Keyword', 'classified-listing'); ?></label>
							<?php } ?>
							<div class="keywords-field-wrapper">
								<input type="text" name="q" data-type="listing" class="rtcl-autocomplete form-control" placeholder="<?php esc_attr_e('Enter your keyword here ...', 'classified-listing'); ?>" value="<?php echo esc_html($keywords); ?>">
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div class="form-group ws-item ws-button  col-sm-6">
					<?php if ($settings['fields_label']) { ?>
						<label for="rtcl-search-button"><?php esc_html_e('Submit button', 'classified-listing'); ?></label>
					<?php } ?>
					<div class="rtcl-action-buttons button-<?php echo !empty($settings['button_alignment']) ? esc_attr($settings['button_alignment']) : 'left'; ?>">
						<button type="submit" class="btn btn-primary"><?php esc_html_e('Search', 'classified-listing'); ?></button>
					</div>
				</div>

			</div>
			<?php do_action('rtcl_widget_search_' . $orientation . '_form', $settings); ?>
		</form>
	</div>
</div>