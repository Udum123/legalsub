<?php

namespace Rtcl\Controllers;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Utility;
use Rtcl\Models\Listing;
use Rtcl\Resources\Options;

class BusinessHoursController
{

	/**
	 * @var int|mixed
	 */
	private static $version;
	/**
	 * @var string|void
	 */
	private static $ajaxurl;

	public static function init() {
		if (Functions::is_enable_business_hours()) {
			self::$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : RTCL_VERSION;
			self::$ajaxurl = admin_url('admin-ajax.php');
			if ($current_lang = apply_filters('rtcl_ajaxurl_current_lang', null, self::$ajaxurl)) {
				self::$ajaxurl = add_query_arg('lang', $current_lang, self::$ajaxurl);
			}
			add_filter('postbox_classes_' . rtcl()->post_type . '_rtcl_business_hours', [
				__CLASS__,
				'add_meta_box_classes'
			]);
			add_action('rtcl_listing_details_meta_box', [__CLASS__, 'add_business_hours_meta_box']);
			add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_script']);
			add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_script']);
			add_action('save_post', [__CLASS__, 'save_business_hours'], 10, 2);
			add_filter('rtcl_sanitize', [__CLASS__, 'sanitize_business_hours'], 10, 3);

			if (rtcl()->is_request('frontend')) {
				add_action("rtcl_listing_form", [__CLASS__, 'business_hours_form'], 15);
				add_action("rtcl_single_listing_business_hours", [__CLASS__, 'display_business_hours']);
			}


			add_action('rtcl_listing_form_after_save_or_update', [
				__CLASS__,
				'update_business_hours_at_save_or_update'
			], 10, 5);
		}
	}

	/**
	 * @param Listing  $listing
	 * @param          $type
	 * @param          $cat_id
	 * @param          $new_listing_status
	 * @param string[] $request_data
	 */
	static function update_business_hours_at_save_or_update($listing, $type, $cat_id, $new_listing_status, $request_data = ['data' => '']) {
		/** @var array $data */
		$data = $request_data['data'];
		if (is_a($listing, Listing::class) && isset($data['_rtcl_active_bhs']) || isset($data['_rtcl_active_special_bhs'])) {
			delete_post_meta($listing->get_id(), '_rtcl_bhs');
			delete_post_meta($listing->get_id(), '_rtcl_special_bhs');
			if (!empty($data['_rtcl_active_bhs']) && !empty($data['_rtcl_bhs']) && is_array($data['_rtcl_bhs'])) {
				$new_bhs = Functions::sanitize($data['_rtcl_bhs'], 'business_hours');
				if (!empty($new_bhs)) {
					update_post_meta($listing->get_id(), '_rtcl_bhs', $new_bhs);
				}

				if (!empty($data['_rtcl_active_special_bhs']) && !empty($data['_rtcl_special_bhs']) && is_array($data['_rtcl_special_bhs'])) {

					$new_sbhs = Functions::sanitize($data['_rtcl_special_bhs'], 'special_business_hours');
					if (!empty($new_sbhs)) {
						update_post_meta($listing->get_id(), '_rtcl_special_bhs', $new_sbhs);
					}
				}
			}
		}
	}

	static function sanitize_business_hours($sanitize_value, $raw_ohs, $type) {

		if (in_array($type, ['business_hours', 'special_business_hours'])) {
			$new_bhs = [];
			if (is_array($raw_ohs) && !empty($raw_ohs)) {
				if ("business_hours" === $type) {
					foreach (Options::get_week_days() as $day_key => $day) {
						if (!empty($raw_ohs[$day_key])) {
							$bh = $raw_ohs[$day_key];
							if (!empty($bh['open'])) {
								$new_bhs[$day_key]['open'] = true;
								if (isset($bh['times']) && is_array($bh['times']) && !empty($bh['times'])) {
									$new_times = [];
									foreach ($bh['times'] as $time) {
										if (!empty($time['start']) && !empty($time['end'])) {
											$start = Utility::formatTime($time['start'], 'H:i');
											$end = Utility::formatTime($time['end'], 'H:i');
											if ($start && $end) {
												$new_times[] = ['start' => $start, 'end' => $end];
											}
										}
									}
									if (!empty($new_times)) {
										$new_bhs[$day_key]['times'] = $new_times;
									}
								}
							} else {
								$new_bhs[$day_key]['open'] = false;
							}
						}
					}
				} else if ("special_business_hours" === $type) {
					$temp_count = 0;
					$temp_keys = [];
					foreach ($raw_ohs as $sh_key => $sbh) {
						if (!empty($sbh['date']) && !isset($temp_keys[$sbh['date']]) && $date = Utility::formatDate($sbh['date'], 'Y-m-d')) {
							$temp_keys[] = $new_bhs[$temp_count]['date'] = $date;
							if (!empty($sbh['open'])) {
								$new_bhs[$temp_count]['open'] = true;
								if (isset($sbh['times']) && is_array($sbh['times']) && !empty($sbh['times'])) {
									$new_times = [];
									foreach ($sbh['times'] as $time) {
										if (!empty($time['start']) && !empty($time['end'])) {
											$start = Utility::formatTime($time['start'], 'H:i');
											$end = Utility::formatTime($time['end'], 'H:i');
											if ($start && $end) {
												$new_times[] = ['start' => $start, 'end' => $end];
											}
										}
									}
									if (!empty($new_times)) {
										$new_bhs[$temp_count]['times'] = $new_times;
									}
								}
							} else {
								$new_bhs[$temp_count]['open'] = false;
							}
						}
						$temp_count++;
					}
				}
			}

			$sanitize_value = $new_bhs;
		}

		return $sanitize_value;
	}

	static function add_business_hours_meta_box($post) {
		add_meta_box(
			'rtcl_business_hours',
			esc_html__('Business Hours', 'classified-listing'),
			[__CLASS__, 'business_hours_meta_box'],
			rtcl()->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * @param \WP_Post $post
	 */
	static function business_hours_meta_box($post) {

		$bhs = get_post_meta($post->ID, '_rtcl_bhs', true);
		$bhs = !empty($bhs) && is_array($bhs) ? $bhs : [];
		$special_bhs = get_post_meta($post->ID, '_rtcl_special_bhs', true);
		$special_bhs = is_array($special_bhs) && !empty($special_bhs) ? $special_bhs : [];
		$weekdays = Options::get_week_days();
		$post_id = $post->ID;
		Functions::get_template('listing-form/business-hours', compact('post_id', 'bhs', 'special_bhs', 'weekdays'));
	}

	/**
	 * @param array $classes
	 *
	 * @return array
	 */
	static function add_meta_box_classes($classes = array()) {
		array_push($classes, sanitize_html_class('rtcl'));

		return $classes;
	}

	/**
	 * @param int      $post_id
	 * @param \WP_Post $post
	 *
	 * @return mixed|void
	 */
	public static function save_business_hours($post_id, $post) {

		if (!isset($_POST['post_type'])) {
			return $post_id;
		}

		if (rtcl()->post_type != $post->post_type) {
			return $post_id;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// Check the logged in user has permission to edit this post
		if (!current_user_can('edit_' . rtcl()->post_type, $post_id)) {
			return $post_id;
		}

		if (!Functions::verify_nonce()) {
			return $post_id;
		}

		if (isset($_POST['_rtcl_active_bhs']) || isset($_POST['_rtcl_active_special_bhs'])) {
			delete_post_meta($post_id, '_rtcl_bhs');
			delete_post_meta($post_id, '_rtcl_special_bhs');
			if (!empty($_POST['_rtcl_active_bhs']) && !empty($_POST['_rtcl_bhs']) && is_array($_POST['_rtcl_bhs'])) {
				$new_bhs = Functions::sanitize($_POST['_rtcl_bhs'], 'business_hours');
				if (!empty($new_bhs)) {
					update_post_meta($post_id, '_rtcl_bhs', $new_bhs);
				}

				if (!empty($_POST['_rtcl_active_special_bhs']) && !empty($_POST['_rtcl_special_bhs']) && is_array($_POST['_rtcl_special_bhs'])) {

					$new_shs = Functions::sanitize($_POST['_rtcl_special_bhs'], 'special_business_hours');
					if (!empty($new_shs)) {
						update_post_meta($post_id, '_rtcl_special_bhs', $new_shs);
					}
				}
			}
		}

	}

	public static function register_script() {
		wp_register_script('rtcl-business-hours', rtcl()->get_assets_uri("js/business-hours.min.js"), [
			'jquery',
			'rtcl-common',
			'daterangepicker'
		], self::$version);
		$business_hours_localize = apply_filters('rtcl_business_hours_localize_options', [
			'ajaxurl'       => self::$ajaxurl,
			"lang"          => [
				'server_error' => esc_html__("Server Error!!", "classified-listing"),
				'confirm'      => esc_html__("Are you sure to delete?", "classified-listing"),

			],
			'timePicker'    => [
				'startDate' => '09:00 AM',
				'locale'    => [
					"format"      => Utility::dateFormatPHPToMoment(Functions::time_format()),
					"applyLabel"  => esc_html__("Apply", "classified-listing"),
					"cancelLabel" => esc_html__("Clear", "classified-listing")
				]
			],
			'timePickerEnd' => [
				'startDate' => '05:00 PM',
			],
			'datePicker'    => [
				"format"      => Utility::dateFormatPHPToMoment(Functions::date_format()),
				"applyLabel"  => esc_html__("Apply", "classified-listing"),
				"cancelLabel" => esc_html__("Clear", "classified-listing")
			]
		]);
		wp_localize_script('rtcl-business-hours', 'rtcl_business_hours', $business_hours_localize);
	}


	public static function enqueue_script() {
		if (Functions::is_listing_form_page()) {
			self::register_script();
			wp_enqueue_script('rtcl-business-hours');
		}
	}

	public static function enqueue_admin_script() {

		global $pagenow, $post_type;
		// validate page
		if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php'))) {
			return;
		}
		if (rtcl()->post_type !== $post_type) {
			return;
		}
		self::register_script();
		wp_enqueue_script('rtcl-business-hours');
	}

	public static function business_hours_form($post_id) {
		$bhs = get_post_meta($post_id, '_rtcl_bhs', true);
		$bhs = !empty($bhs) && is_array($bhs) ? $bhs : [];
		$special_bhs = get_post_meta($post_id, '_rtcl_special_bhs', true);
		$special_bhs = is_array($special_bhs) && !empty($special_bhs) ? $special_bhs : [];
		$weekdays = Options::get_week_days();

		Functions::get_template('listing-form/business-hours', compact('bhs', 'special_bhs', 'weekdays'));
	}


	/**
	 * Whether or not the business is currently open or not.
	 *
	 * @param array $business_hours business hours []
	 *
	 * @return bool
	 */
	public static function openStatus($business_hours) {
		$current_dayKey = absint(date('w', current_time('timestamp'))); // Modified By Rashid
		$dayData = !empty($business_hours[$current_dayKey]) && is_array($business_hours[$current_dayKey]) ? $business_hours[$current_dayKey] : [];
		if (!empty($dayData)) {
			if (empty($dayData['open'])) {
				return false;
			}
			if (empty($dayData['times']) || !is_array($dayData['times'])) {
				return 1;
			}
			$timePeriods = $dayData['times'];
			// If there are open and close hours recorded for the day, loop thru the open periods.
			foreach ($timePeriods as $periodIndex => $timePeriod) {
				if (self::openPeriod($timePeriod) && self::isOpen($timePeriod['start'], $timePeriod['end'])) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Whether or not there are any open hours during the week.
	 *
	 * @param array $days
	 *
	 * @return boolean
	 * @since 1.0
	 *
	 */
	public static function hasOpenHours($days) {

		foreach ($days as $key => $day) {

			if (self::openToday($day)) return TRUE;
		}

		return FALSE;
	}


	/**
	 * Whether or not the day has any open periods.
	 *
	 * @param array $day
	 *
	 * @return bool
	 * @since 1.0
	 *
	 */
	public static function openToday($day) {
		if (empty($day['open'])) {
			return false;
		}
		if (empty($day['times']) || !is_array($day['times'])) {
			return 1;
		}

		foreach ($day['times'] as $timePeriod) {
			if (self::openPeriod($timePeriod)) return TRUE;
		}

		return FALSE;
	}

	/**
	 * Whether or not the period is open.
	 *
	 * @param array $period ['start', 'end']
	 *
	 * @return bool
	 * @since 1.0
	 *
	 */
	public static function openPeriod($period) {

		if (empty($period)) return FALSE;

		if (!empty($period['start']) && !empty($period['end'])) return TRUE;

		return FALSE;
	}

	public static function isOpenAllDayLong($day) {

		if (!empty($day['open']) && empty($day['times'])) {
			return true;
		}

		return false;

	}


	/**
	 * Whether or not the business is open.
	 *
	 * @link  http://stackoverflow.com/a/17145145
	 *
	 * @since 1.0
	 *
	 * @param string $t1 Time open.
	 * @param string $t2 Time close.
	 * @param null   $tn Time now.
	 *
	 * @return bool
	 */
	public static function isOpen($t1, $t2, $tn = NULL) {

		$tn = is_null($tn) ? date('H:i', current_time('timestamp')) : Utility::formatTime($tn, 'H:i');

		$t1 = +str_replace(':', '', $t1);
		$t2 = +str_replace(':', '', $t2);
		$tn = +str_replace(':', '', $tn);

		if ($t2 >= $t1) {

			return $t1 <= $tn && $tn < $t2;

		} else {

			return !($t2 <= $tn && $tn < $t1);
		}

	}

	/**
	 * @param int $listing_id
	 *
	 * @return array $business_hours
	 */
	public static function get_business_hours($listing_id) {
		$bhs = get_post_meta($listing_id, '_rtcl_bhs', true);
		$business_hours = !empty($bhs) && is_array($bhs) ? $bhs : [];
		$special_bhs = get_post_meta($listing_id, '_rtcl_special_bhs', true);
		if (is_array($special_bhs) && !empty($special_bhs)) {
			$current_week_day = absint(date('w', current_time('timestamp')));
			$special_data = [];
			foreach ($special_bhs as $special_bh) {
				if (!empty($special_bh['date'])) {
					$week_day = date('w', strtotime($special_bh['date']));
					if ($week_day !== false && absint($week_day) === $current_week_day) {
						if (isset($special_bh['open'])) {
							$special_data['open'] = !empty($special_bh['open']);
							if (!empty($special_bh['times']) && is_array($special_bh['times'])) {
								$special_data['times'] = $special_bh['times'];
							}
						}
					}
				}
			}
			if (!empty($special_data)) {
				$business_hours[$current_week_day] = $special_data;
			}
		}
		return $business_hours;
	}

	public static function display_business_hours( $listing = false ) {
		if (!$listing) global $listing;
		if ( ! $listing ) return; 
		$business_hours = self::get_business_hours($listing->get_id());
		if (empty($business_hours)) return;
		$current_week_day = absint(date('w', current_time('timestamp')));
		$defaults = [
			'header'                => TRUE,
			'footer'                => FALSE,
			'day_name'              => 'full', // Valid options are 'full', 'abbrev' or 'initial'.
			'show_closed_day'       => TRUE,
			'show_closed_period'    => TRUE,
			'show_open_status'      => TRUE,
			'highlight_open_period' => TRUE,
			'open_close_separator'  => '&ndash;',
			'open_text'             => esc_html__('Open', 'classified-listing'),
			'close_text'            => esc_html__('Close', 'classified-listing'),
			'open_today_text'       => esc_html__('Open Today (24 Hours)', 'classified-listing'),
			'closed_today_text'      => esc_html__('Closed Today', 'classified-listing'),  // Modified by Rashid
			'open_status_text'      => esc_html__('We are currently open.', 'classified-listing'),
			'close_status_text'     => esc_html__('Sorry, we are currently closed.', 'classified-listing')
		];

		$options = wp_parse_args(apply_filters('rtcl_business_hours_display_options', []), $defaults);

		if (!self::hasOpenHours($business_hours)) return;
		Functions::get_template('listing/business-hours', compact('business_hours', 'options', 'current_week_day'));
	}
}
