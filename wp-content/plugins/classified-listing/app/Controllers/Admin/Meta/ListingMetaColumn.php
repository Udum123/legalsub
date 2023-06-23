<?php

namespace Rtcl\Controllers\Admin\Meta;


use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class ListingMetaColumn
{

	public function __construct() {
		add_action('manage_edit-' . rtcl()->post_type . '_columns', [$this, 'listing_get_columns']);
		add_action('manage_' . rtcl()->post_type . '_posts_custom_column', [$this, 'listing_column_content'], 10, 2);
		add_action('restrict_manage_posts', [$this, 'restrict_manage_posts']);
		add_action('before_delete_post', [$this, 'before_delete_post']);
		add_action('parse_query', [$this, 'parse_query']);

	}

	function listing_get_columns($columns) {
		$featured_label = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_featured_label' );
		$top_label  	= Functions::get_option_item( 'rtcl_moderation_settings', 'listing_top_label' );
		
		$new_columns = array(
			'views'       => esc_html__('Views', 'classified-listing'),
			'featured'    => $featured_label ?: esc_html__( "Featured", "classified-listing" ),
			'_top'        => $top_label ?: esc_html__( "Top", "classified-listing-pro" ),
			'posted_date' => esc_html__('Posted Date', 'classified-listing'),
			'expiry_date' => esc_html__('Expires on', 'classified-listing'),
			'status'      => esc_html__('Status', 'classified-listing')
		);

		unset($columns['date']);

		$taxonomy_column = 'taxonomy-' . rtcl()->location;
		if (!array_key_exists($taxonomy_column, $columns)) {
			$taxonomy_column = 'taxonomy-' . rtcl()->category;
		}

		return Functions::array_insert_after($taxonomy_column, $columns, $new_columns);
	}

	function listing_column_content($column, $post_id) {

		switch ($column) {
			case 'views' :
				echo absint(get_post_meta($post_id, '_views', true));
				break;
			case 'featured' :
				$value = get_post_meta($post_id, 'featured', true);
				echo '<span class="rtcl-tick-cross">' . ($value == 1 ? '&#x2713;' : '&#x2717;') . '</span>';
				break;
			case '_top' :
				$value = get_post_meta($post_id, '_top', true);
				echo '<span class="rtcl-tick-cross">' . ($value == 1 ? '&#x2713;' : '&#x2717;') . '</span>';
				break;
			case 'posted_date' :
				printf(_x('%s ago', '%s = human-readable time difference', 'classified-listing'),
					human_time_diff(get_the_time('U', $post_id), current_time('timestamp')));
				break;
			case 'expiry_date' :
				$never_expires = get_post_meta($post_id, 'never_expires', true);

				if (!empty($never_expires)) {
					esc_html_e('Never Expires', 'classified-listing');
				} else {
					$expiry_date = get_post_meta($post_id, 'expiry_date', true);

					if (!empty($expiry_date)) {
						echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'),
							strtotime($expiry_date));
					} else {
						echo '-';
					}
				}
				break;
			case 'status' :
				$listing_status = get_post_meta($post_id, 'listing_status', true);
				$listing_status = (empty($listing_status) || 'post_status' == $listing_status) ? get_post_status($post_id) : $listing_status;
				$status_list = Options::get_status_list();
				echo !empty($status_list[$listing_status]) ? $status_list[$listing_status] : "-";
				break;
		}
	}

	public function restrict_manage_posts() {

		global $typenow, $wp_query;

		if (rtcl()->post_type == $typenow) {
			$location_name = '';
			$location_id = '';
			$category_name = '';
			$category_id = '';

			if (!empty($_GET['_rtcl_location'])) {
				$location_id = absint($_GET['_rtcl_location']);
				$location = get_term_by('id', $location_id, rtcl()->location);
				$location_name = $location ? $location->name : '';
			}
			if (!empty($_GET['_rtcl_category'])) {
				$category_id = absint($_GET['_rtcl_category']);
				$category = get_term_by('id', $category_id, rtcl()->category);
				$category_name = $category ? $category->name : '';
			}

			?>
			<select class="rtcl-ajax-select" name="_rtcl_location"
					data-type="location"
					data-placeholder="<?php esc_attr_e('Filter by location', 'classified-listing'); ?>"
					data-allow_clear="true">
				<option value="<?php echo esc_attr($location_id); ?>" selected="selected">
					<?php echo $location_name; ?>
				<option>
			</select>
			<select class="rtcl-ajax-select" name="_rtcl_category"
					data-type="category"
					data-placeholder="<?php esc_attr_e('Filter by category', 'classified-listing'); ?>"
					data-allow_clear="true">
				<option value="<?php echo esc_attr($category_id); ?>" selected="selected">
					<?php echo $category_name; ?>
				<option>
			</select>
			<?php
			// Restrict by featured
			if (!Functions::is_payment_disabled()) {
				$promotions = Options::get_listing_promotions();
				$_promotion = isset($_GET['promotion']) ? sanitize_key($_GET['promotion']) : null;
				echo '<select name="promotion">';
				printf('<option value="%s"%s>%s</option>', "", selected(null, $_promotion, false),
					esc_html__("All Promotions", 'classified-listing'));
				foreach ($promotions as $p_key => $promotion) {
					printf('<option value="%s"%s>%s</option>', $p_key, selected($p_key, $_promotion, false),
						$promotion);
				}
				echo '</select>';

			}
			$stat = isset($_GET['post_status']) ? $_GET['post_status'] : "all";
			if ("trash" !== $stat) {
				echo '<select name="post_status">';
				$status_list = Options::get_status_list(true);
				printf('<option value="%s">%s</option>', 'all',
					__("All Status", 'classified-listing'));
				foreach ($status_list as $key => $status) {
					$slt = $key == $stat ? " selected" : null;
					printf('<option value="%s"%s>%s</option>', $key, $slt, $status);
				}
				echo '</select>';
			}

		}

	}

	/**
	 * @param $post_id
	 *
	 * @return mixed|void
	 */
	function before_delete_post($post_id) {
		if (rtcl()->post_type !== get_post_type($post_id)) {
			return;
		}

//        $check = apply_filters('rtcl_before_delete_listing_attachment_check', false, $post_id, $post_type);
//        if (false !== $check) {
//            return $check;
//        }

		$children = get_children(apply_filters('rtcl_before_delete_listing_attachment_query_args', [
			'post_parent'    => $post_id,
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'inherit',
		], $post_id));

		if (!empty($children)) {
			foreach ($children as $child) {
				wp_delete_attachment($child->ID, true);
			}
		}

		if (Functions::is_enable_favourite()) {
			global $wpdb;
			$results = $wpdb->get_results($wpdb->prepare("SELECT 
                                                                user_id 
                                                            FROM 
                                                                `{$wpdb->usermeta}`
                                                            WHERE 
                                                                  `meta_key` = 'rtcl_favourites' AND meta_value LIKE %s", '%' . $wpdb->esc_like($post_id) . '%'));

			if (!empty($results)) {
				foreach ($results as $favUser) {
					$favIds = get_user_meta($favUser->user_id, 'rtcl_favourites', true);
					if (!empty($favIds) && is_array($favIds) && ($key = array_search($post_id, $favIds)) !== false) {
						unset($favIds[$key]);
						if (empty($favIds)) {
							delete_user_meta($favUser->user_id, 'rtcl_favourites');
						} else {
							update_user_meta($favUser->user_id, 'rtcl_favourites', $favIds);
						}
					}
				}
			}

		}

		do_action('rtcl_before_delete_listing', $post_id);
	}

	public function parse_query($query) {

		global $pagenow, $post_type;

		if ('edit.php' == $pagenow && rtcl()->post_type == $post_type) {

			$tax_query = [];
			// Convert location id to taxonomy term in query
			if (isset($_REQUEST['_rtcl_location']) && $location_id = Functions::clean(wp_unslash($_REQUEST['_rtcl_location']))) {
				$tax_query[] = [
					'taxonomy' => rtcl()->location,
					'field'    => 'ID',
					'terms'    => array($location_id)
				];
			}

			// Convert category id to taxonomy term in query
			if (isset($_REQUEST['_rtcl_category']) && $category_id = Functions::clean(wp_unslash($_REQUEST['_rtcl_category']))) {
				$tax_query[] = [
					'taxonomy' => rtcl()->category,
					'field'    => 'ID',
					'terms'    => array($category_id)
				];
			}
			if (!empty($tax_query)) {
				$query_tax_query = $query->get('tax_query');
				$query_tax_query = is_array($query_tax_query) ? $query_tax_query : [];
				$query_tax_query['relation'] = 'AND';
				$query->set('tax_query', array_merge($query_tax_query, $tax_query));
			}


			// Set featured meta in query
			if (isset($_GET['promotion']) && in_array($_GET['promotion'], array_keys(Options::get_listing_promotions()), true)) {
				$query->query_vars['meta_key'] = sanitize_key($_GET['promotion']);
				$query->query_vars['meta_value'] = 1;
			}

		}

	}

}