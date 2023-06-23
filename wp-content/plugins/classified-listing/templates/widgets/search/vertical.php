<?php
/**
 * @var string  $id
 * @var bool    $radius_search
 * @var boolean $can_search_by_location
 * @var boolean $can_search_by_category
 * @var boolean $can_search_by_listing_types
 * @var boolean $can_search_by_price
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use Rtcl\Resources\Options;

$orderby = strtolower(Functions::get_option_item('rtcl_general_settings', 'taxonomy_orderby', 'name'));
$order = strtoupper(Functions::get_option_item('rtcl_general_settings', 'taxonomy_order', 'DESC'));
?>
<div class="rtcl rtcl-search rtcl-search-vertical">
	<form action="<?php echo esc_url(Functions::get_filter_form_url()) ?>"
		  class="rtcl-widget-search-vertical rtcl-widget-search-form">
		<div class="form-group">
			<input type="text" name="q" class="form-control"
				   placeholder="<?php esc_attr_e('Enter your keyword here ...', 'classified-listing'); ?>"
				   value="<?php if (isset($_GET['q'])) {
					   echo esc_attr(Functions::clean(wp_unslash($_GET['q'])));
				   } ?>">
		</div>

		<?php if ($radius_search):
			$rs_data = Options::radius_search_options();
			?>
			<div class="form-group">
				<label
					for="rtc-geo-search-<?php echo esc_attr($id); ?>"><?php echo esc_html(Text::get_select_location_text()); ?></label>
				<div class="rtcl-geo-address-field">
					<input type="text" name="geo_address" autocomplete="off"
						   value="<?php echo !empty($_GET['geo_address']) ? esc_attr($_GET['geo_address']) : '' ?>"
						   placeholder="<?php esc_attr_e('Select a location', 'classified-listing') ?>"
						   class="form-control rtcl-geo-address-input"/>
					<i class="rtcl-get-location rtcl-icon rtcl-icon-target"></i>
					<input type="hidden" class="latitude" name="center_lat"
						   value="<?php echo !empty($_GET['center_lat']) ? esc_attr($_GET['center_lat']) : '' ?>">
					<input type="hidden" class="longitude" name="center_lng"
						   value="<?php echo !empty($_GET['center_lng']) ? esc_attr($_GET['center_lng']) : '' ?>">
				</div>
				<div class="rtcl-range-slider-field">
					<div class="rtcl-range-label">
						<?php echo wp_kses(
							sprintf(
								__("Radius (%s %s)", 'classified-listing'),
								sprintf('<span class="rtcl-range-value">%s</span>', !empty($_GET['distance']) ? absint($_GET['distance']) : 0),
								in_array($rs_data['units'], ['km', 'kilometers']) ? __('km', 'classified-listing') : __('Miles', 'classified-listing')
							),
							[
								'span' => [
									'class' => []
								]
							]
						) ?>
					</div>
					<input type="range" class="form-control-range rtcl-range-slider-input" name="distance" min="0"
						   max="<?php echo absint($rs_data['max_distance']) ?>"
						   value="<?php echo absint(isset($_GET['distance']) ? $_GET['distance'] : $rs_data['default_distance']) ?>">
				</div>
			</div>
		<?php endif ?>

		<?php if ('local' === Functions::location_type() && $can_search_by_location) : ?>
			<!-- Location field -->
			<div class="form-group">
				<label><?php esc_html_e('Select a location', 'classified-listing'); ?></label>
				<?php
				$location = 0;
				if ($loc_slug = get_query_var('rtcl_location')) {
					$lTerm = get_term_by('slug', $loc_slug, rtcl()->location);
					if ($lTerm) {
						$location = $lTerm->term_id;
					}
				}
				Functions::dropdown_terms(array(
					'show_option_none' => '-- ' . esc_html__('Select a location', 'classified-listing') . ' --',
					'taxonomy'         => rtcl()->location,
					'name'             => 'l',
					'class'            => 'form-control',
					'selected'         => $location
				));
				?>
			</div>
		<?php endif; ?>

		<?php if ($can_search_by_category) : ?>
			<!-- Category field -->
			<div class="form-group">
				<label><?php esc_html_e('Select a category', 'classified-listing'); ?></label>
				<?php
				$category = 0;
				if ($cat_slug = get_query_var('rtcl_category')) {
					$cTerm = get_term_by('slug', $cat_slug, rtcl()->category);
					if ($cTerm) {
						$category = $cTerm->term_id;
					}
				}
				Functions::dropdown_terms(array(
					'show_option_none'  => '-- ' . esc_html__('Select a category', 'classified-listing') . ' --',
					'option_none_value' => -1,
					'taxonomy'          => rtcl()->category,
					'name'              => 'c',
					'class'             => 'form-control rtcl-category-search',
					'selected'          => $category
				));
				?>
			</div>
		<?php endif; ?>

		<?php if ($can_search_by_listing_types) : ?>
			<div class="form-group">
				<label><?php esc_html_e('Select Type', 'classified-listing'); ?></label>
				<select class="form-control" name="filters[ad_type]">
					<option value=""><?php esc_html_e('Select type', 'classified-listing'); ?></option>
					<?php
					$listing_types = Functions::get_listing_types();
					if (!empty($listing_types)) {
						foreach ($listing_types as $key => $listing_type) {
							?>
							<option
								value="<?php echo esc_attr($key) ?>" <?php echo isset($_GET['filters']['ad_type']) && trim($_GET['filters']['ad_type']) == $key ? ' selected' : null ?>><?php echo esc_html($listing_type) ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>
		<?php endif; ?>

		<?php if ($can_search_by_price) : ?>
			<!-- Price fields -->
			<div class="form-group">
				<label><?php esc_html_e('Price Range', 'classified-listing'); ?></label>
				<div class="row">
					<div class="col-md-6 col-xs-6">
						<input type="text" name="filters[price][min]" class="form-control"
							   placeholder="<?php esc_attr_e('min', 'classified-listing'); ?>"
							   value="<?php if (isset($_GET['filters']['price'])) {
								   echo esc_attr($_GET['filters']['price']['min']);
							   } ?>">
					</div>
					<div class="col-md-6 col-xs-6">
						<input type="text" name="filters[price][max]" class="form-control"
							   placeholder="<?php esc_attr_e('max', 'classified-listing'); ?>"
							   value="<?php if (isset($_GET['filters']['price'])) {
								   echo esc_attr($_GET['filters']['price']['max']);
							   } ?>">
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php do_action('rtcl_widget_search_vertical_form', $can_search_by_location, $can_search_by_category) ?>
		<!-- Action buttons -->
		<button type="submit"
				class="btn btn-primary"><?php esc_html_e('Search Listings', 'classified-listing'); ?></button>
		<a href="<?php echo get_permalink(); ?>"
		   class="btn btn-danger my-3"><?php esc_html_e('Reset', 'classified-listing'); ?></a>
	</form>
</div>