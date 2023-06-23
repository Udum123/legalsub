<?php

namespace Rtcl\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use Rtcl\Models\Listing;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;
use Rtcl\Models\Pricing;
use Rtcl\Resources\Options;

class AppliedBothEndHooks
{

	static public function init() {

		add_action('rtcl_new_user_created', [__CLASS__, 'new_user_notification_email_admin'], 10);
		add_action('rtcl_new_user_created', [__CLASS__, 'new_user_notification_email_user'], 10, 3);
		add_action('rtcl_listing_form_after_save_or_update', [__CLASS__, 'new_post_notification_email_user_submitted'], 10, 4);
		add_action('rtcl_listing_form_after_save_or_update', [__CLASS__, 'new_post_notification_email_user_published'], 20, 4);
		add_action('rtcl_listing_form_after_save_or_update', [__CLASS__, 'new_post_notification_email_admin'], 30, 2);
		add_action('rtcl_listing_form_after_save_or_update', [__CLASS__, 'update_post_notification_email_admin'], 40, 2);

		add_filter('rtcl_my_account_endpoint', [__CLASS__, 'my_account_end_point_filter'], 10);
		add_filter('rtcl_account_menu_item_classes', [__CLASS__, 'my_account_menu_item_classes_filter_edit_account_for_wc'], 10, 3);

		add_filter('rtcl_account_menu_item_classes', [__CLASS__, 'my_account_menu_item_classes_filter_chat'], 10, 3);

		add_action('rtcl_listing_form_price_unit', [__CLASS__, 'rtcl_listing_form_price_unit_cb'], 10, 2);
		add_filter('rtcl_price_meta_html', [__CLASS__, 'add_price_unit_to_price'], 10, 3);
		add_filter('rtcl_price_meta_html', [__CLASS__, 'add_price_type_to_price'], 20, 3);

		add_filter('rtcl_checkout_validation_errors', [__CLASS__, 'add_rtcl_checkout_validation'], 10, 4);
		add_filter('rtcl_checkout_process_new_order_args', [__CLASS__, 'add_listing_id_at_regular_order'], 10, 4);

		add_action('rtcl_checkout_process_success', [__CLASS__, 'add_checkout_process_notice'], 10);

		add_filter('rtcl_listing_get_custom_field_group_ids', [__CLASS__, 'get_custom_field_group_ids'], 10, 2);
	}

	static function get_custom_field_group_ids($ids, $category_id) {
		$group_ids = is_array($ids) && !empty($ids) ? $ids : [];
		// Get category fields
		if ($category_id > 0) {

			// Get global fields
			$args = array(
				'post_type'        => rtcl()->post_type_cfg,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'suppress_filters' => false,
				'meta_query'       => array(
					array(
						'key'   => 'associate',
						'value' => 'all'
					),
				)
			);

			$group_ids = get_posts($args);

			$args = array(
				'post_type'        => rtcl()->post_type_cfg,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'suppress_filters' => false,
				'tax_query'        => array(
					array(
						'taxonomy'         => rtcl()->category,
						'field'            => 'term_id',
						'terms'            => $category_id,
						'include_children' => false,
					),
				),
				'meta_query'       => array(
					array(
						'key'   => 'associate',
						'value' => 'categories'
					),
				)
			);

			$category_groups = get_posts($args);

			$group_ids = array_merge($group_ids, $category_groups);
			$group_ids = array_unique($group_ids);

		}

		return $group_ids;
	}

	/**
	 * @param Payment $payment
	 */
	static function add_checkout_process_notice($payment) {
		if ($payment->gateway) {
			if ('paypal' === $payment->gateway->id) {
				Functions::add_notice(esc_html__("Redirecting to paypal.", "classified-listing"));
			} else if ('offline' === $payment->gateway->id) {
				Functions::add_notice(esc_html__("Payment made pending confirmation.", "classified-listing"));
			} else {
				Functions::add_notice(esc_html__("Payment successfully made.", "classified-listing"));
			}
		}
	}

	/**
	 * @param \WP_Error      $errors
	 * @param array          $checkout_data
	 * @param Pricing        $pricing
	 * @param PaymentGateway $gateway
	 *
	 * @return \WP_Error
	 */
	static function add_rtcl_checkout_validation($errors, $checkout_data, $pricing, $gateway) {
		if (!$pricing || ($pricing && !is_a($pricing, Pricing::class)) || ($pricing && is_a($pricing, Pricing::class) && !$pricing->exists())) {
			$errors->add('rtcl_checkout_error_empty_pricing', __("No pricing selected to make payment.", "classified-listing"));
		}
		if (!$gateway || !is_object($gateway)) {
			$errors->add('rtcl_checkout_error_empty_payment_gateway', __("No payment Gateway selected.", "classified-listing"));
		}

		if (($pricing && 'regular' === $pricing->getType()) && (!isset($checkout_data['listing_id']) || !rtcl()->factory->get_listing($checkout_data['listing_id']))) {
			$errors->add('rtcl_checkout_error_empty_listing', __("No ad selected to make payment.", "classified-listing"));
		}

		return $errors;
	}

	/**
	 * @param array          $new_payment_args
	 * @param Pricing        $pricing
	 * @param PaymentGateway $gateway
	 * @param array          $checkout_data
	 *
	 * @return array
	 */
	static function add_listing_id_at_regular_order($new_payment_args, $pricing, $gateway, $checkout_data) {
		if ($pricing && 'regular' === $pricing->getType()) {
			$new_payment_args['meta_input']['listing_id'] = isset($checkout_data['listing_id']) ? absint($checkout_data['listing_id']) : 0;
		}

		return $new_payment_args;
	}

	/**
	 * @param string  $price_meta_html
	 * @param string  $price
	 * @param Listing $listing
	 *
	 * @return string
	 */
	public static function add_price_type_to_price($price_meta_html, $price, $listing) {
		if (is_a($listing, Listing::class)) {
			$is_single = Functions::get_option_item('rtcl_moderation_settings', 'display_options_detail', 'price_type', 'multi_checkbox');
			$is_listing = Functions::get_option_item('rtcl_moderation_settings', 'display_options', 'price_type', 'multi_checkbox');
			if (($is_single && is_singular(rtcl()->post_type)) || ($is_listing && !is_singular(rtcl()->post_type))) {
				$price_type = $listing->get_price_type();
				$price_type_html = null;
				if ($price_type == "negotiable") {
					$price_type_html = sprintf('<span class="rtcl-price-type-label rtcl-price-type-negotiable">(%s)</span>', esc_html(Text::price_type_negotiable()));
				} elseif ($price_type == "fixed") {
					$price_type_html = sprintf('<span class="rtcl-price-type-label rtcl-price-type-fixed">(%s)</span>', esc_html(Text::price_type_fixed()));
				} elseif ($price_type === "on_call") {
					$price_type_html = sprintf('<span class="rtcl-price-type-label rtcl-on_call">%s</span>', esc_html(Text::price_type_on_call()));
				}
				$price_meta_html .= apply_filters('rtcl_add_price_type_to_price', $price_type_html, $price_type, $listing);
			}
		}

		return $price_meta_html;
	}

	/**
	 * @param string  $price_meta_html
	 * @param string  $price
	 * @param Listing $listing
	 *
	 * @return string
	 */
	public static function add_price_unit_to_price($price_meta_html, $price, $listing) {
		if (is_a($listing, Listing::class) && $listing->get_price_type() !== 'on_call' && $price_unit = $listing->get_price_unit()) {
			$price_unit_html = null;
			$price_units = Options::get_price_unit_list();
			if (in_array($price_unit, array_keys($price_units))) {
				$price_unit_html = sprintf('<span class="rtcl-price-unit-label rtcl-price-unit-%s">%s</span>', $price_unit, $price_units[$price_unit]['short']);
			}
			$price_meta_html .= apply_filters('rtcl_add_price_unit_to_price', $price_unit_html, $price_unit, $listing);
		}
		return $price_meta_html;
	}


	static function my_account_menu_item_classes_filter_edit_account_for_wc($classes, $endpoint, $query_vars) {
		if ($endpoint === 'edit-account' && Functions::is_wc_activated() && isset($query_vars['rtcl_edit_account']) && $query_vars['rtcl_edit_account'] === $endpoint && !in_array('is-active', $classes)) {
			$classes[] = 'is-active';
		}

		return $classes;
	}

	static function my_account_menu_item_classes_filter_chat($classes, $endpoint) {
		if ($endpoint === 'chat') {
			$classes[] = 'rtcl-chat-unread-count';
		}

		return $classes;
	}

	/**
	 * @param $endpoints
	 *
	 * @return mixed
	 */
	public static function my_account_end_point_filter($endpoints) {

		// Remove payment endpoint
		if (Functions::is_payment_disabled()) {
			unset($endpoints['payments']);
		}

		// Remove favourites endpoint
		if (Functions::is_favourites_disabled()) {
			unset($endpoints['favourites']);
		}

		return $endpoints;
	}

	static public function new_user_notification_email_admin($user_id) {
		if (Functions::get_option_item('rtcl_email_settings', 'notify_admin', 'register_new_user', 'multi_checkbox')) {
			rtcl()->mailer()->emails['User_New_Registration_Email_To_Admin']->trigger($user_id);
		}
	}

	static public function new_user_notification_email_user($user_id, $new_user_data, $password_generated) {
		if ($password_generated) {
			$new_user_data['password_generated'] = true;
		}
		if (Functions::get_option_item('rtcl_email_settings', 'notify_users', 'register_new_user', 'multi_checkbox')) {
			rtcl()->mailer()->emails['User_New_Registration_Email_To_User']->trigger($user_id, $new_user_data);
		}
	}

	/**
	 * @param Listing $listing
	 * @param         $type
	 */
	static public function update_post_notification_email_admin($listing, $type) {
		if (is_a($listing, Listing::class) && $type == 'update' && Functions::get_option_item('rtcl_email_settings', 'notify_admin', 'listing_edited', 'multi_checkbox')) {
			rtcl()->mailer()->emails['Listing_Update_Email_To_Admin']->trigger($listing->get_id());
		}
	}

	/**
	 * @param Listing $listing
	 * @param         $type
	 */
	static public function new_post_notification_email_admin($listing, $type) {
		if (is_a($listing, Listing::class) && $type == 'new' && Functions::get_option_item('rtcl_email_settings', 'notify_admin', 'listing_submitted', 'multi_checkbox')) {
			rtcl()->mailer()->emails['Listing_Submitted_Email_To_Admin']->trigger($listing->get_id());
		}
	}

	/**
	 * @param Listing $listing
	 * @param         $type
	 * @param         $cat_id
	 * @param         $new_listing_status
	 */
	static public function new_post_notification_email_user_submitted($listing, $type, $cat_id, $new_listing_status) {
		if (is_a($listing, Listing::class) && $type == 'new' && Functions::get_option_item('rtcl_email_settings', 'notify_users', 'listing_submitted', 'multi_checkbox') && $new_listing_status !== 'publish') {
			rtcl()->mailer()->emails['Listing_Submitted_Email_To_Owner']->trigger($listing->get_id());
		}
	}

	/**
	 * @param Listing $listing
	 * @param         $type
	 * @param         $cat_id
	 * @param         $new_listing_status
	 */
	static public function new_post_notification_email_user_published($listing, $type, $cat_id, $new_listing_status) {
		if (is_a($listing, Listing::class) && $type == 'new' && Functions::get_option_item('rtcl_email_settings', 'notify_users', 'listing_published', 'multi_checkbox') && $new_listing_status === 'publish') {
			rtcl()->mailer()->emails['Listing_Published_Email_To_Owner']->trigger($listing->get_id());
		}
	}

	/**
	 * @param     $listing Listing
	 * @param int $category_id
	 */
	static public function rtcl_listing_form_price_unit_cb($listing, $category_id = 0) {
		echo Functions::get_listing_form_price_unit_html($category_id, $listing);
	}

}
