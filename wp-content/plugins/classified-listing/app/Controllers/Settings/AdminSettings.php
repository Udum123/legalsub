<?php

namespace Rtcl\Controllers\Settings;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\RtclEmail;
use Rtcl\Models\SettingsAPI;
use Rtcl\Services\MaxMindDatabaseService;

class AdminSettings extends SettingsAPI
{

	protected $tabs = [];
	protected $active_tab;
	protected $current_section;
	protected $gateway_temp_desc;
	protected static $instance = null;
	protected $classMap = [
		'misc' => MiscSettingsController::class
	];
	/**
	 * @var array|mixed|void
	 */
	protected $subtabs = [];

	public function __construct() {
		$this->classMap = apply_filters('rtcl_settings_classMap', $this->classMap);
		add_action('admin_init', [$this, 'setTabs']);
		add_action('admin_init', [$this, 'save']);
		add_action('admin_menu', [$this, 'add_listing_types_menu'], 1);
		add_action('admin_menu', [$this, 'add_settings_menu'], 50);
		add_action('admin_menu', [$this, 'add_import_menu'], 60);
		add_action('admin_menu', [$this, 'add_addons_themes__menu'], 99);
		add_action('admin_init', [$this, 'preview_emails']);
		add_action('admin_init', [$this, 'generate_rest_api_key']);
		add_action('rtcl_admin_settings_groups', [$this, 'setup_settings']);
		if (!rtcl()->has_pro()) {
			add_filter('plugin_action_links_' . plugin_basename(RTCL_PLUGIN_FILE), [$this, 'get_pro_action']);
		}
		if (apply_filters('rtcl_settings_link_on_admin_bar', true)) {
			add_action('wp_before_admin_bar_render', [$this, 'add_admin_bar'], 999);
		}
	}

	/**
	 * @param bool $new
	 *
	 * @return AdminSettings|null
	 */
	public static function get_instance($new = false) {
		// If the single instance hasn't been set, set it now.
		if ($new || null === self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_pro_action($links) {
		$links[] = '<a target="_blank" href="' . esc_url('https://radiustheme.com/demo/wordpress/classified') . '">Demo</a>';
		$links[] = '<a target="_blank" href="' . esc_url('https://www.radiustheme.com/docs/classified-listing/') . '">Documentation</a>';
		$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;" href="' . esc_url('https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/') . '">Get Pro</a>';

		return $links;
	}

	public function add_admin_bar() {
		if (!current_user_can('manage_rtcl_options')) {
			return;
		}

		global $wp_admin_bar;
		$url = add_query_arg(['post_type' => rtcl()->post_type], admin_url('edit.php'));
		$args = [
			'id'    => rtcl()->post_type,
			'title' => esc_html__('Classified Listing', 'classified-listing'),
			'href'  => $url,
			'meta'  => ['class' => sprintf('%s-admin-toolbar', rtcl()->post_type)]
		];
		$wp_admin_bar->add_menu($args);

		$category_args = [
			'id'     => rtcl()->post_type . "-category",
			'title'  => esc_html__('Categories', 'classified-listing'),
			'href'   => add_query_arg([
				'taxonomy'  => rtcl()->category,
				'post_type' => rtcl()->post_type
			], admin_url('edit-tags.php')),
			'parent' => rtcl()->post_type,
			'meta'   => ['class' => sprintf('%s-admin-toolbar-categories', rtcl()->post_type)]
		];

		$wp_admin_bar->add_menu($category_args);

		$location_args = array(
			'id'     => rtcl()->post_type . "-location",
			'title'  => esc_html__('Locations', 'classified-listing'),
			'href'   => add_query_arg(array(
				'taxonomy'  => rtcl()->location,
				'post_type' => rtcl()->post_type
			), admin_url('edit-tags.php')),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-locations', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($location_args);

		$listing_types_args = array(
			'id'     => rtcl()->post_type . "-listing-types",
			'title'  => esc_html__('Listing Types', 'classified-listing'),
			'href'   => add_query_arg(array(
				'post_type' => rtcl()->post_type,
				'page'      => 'rtcl-listing-type'
			), admin_url('edit.php')),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-listing-types', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($listing_types_args);

		$cfg_args = array(
			'id'     => rtcl()->post_type . "-custom-fields",
			'title'  => esc_html__('Custom Fields', 'classified-listing'),
			'href'   => add_query_arg(array(
				'post_type' => rtcl()->post_type_cfg
			), admin_url('edit.php')),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-custom-fields', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($cfg_args);

		$pricing_args = array(
			'id'     => rtcl()->post_type . "-pricing",
			'title'  => esc_html__('Pricing', 'classified-listing'),
			'href'   => add_query_arg(array(
				'post_type' => rtcl()->post_type_pricing
			), admin_url('edit.php')),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-pricing', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($pricing_args);

		$payment_args = array(
			'id'     => rtcl()->post_type . "-payment",
			'title'  => esc_html__('Payment History', 'classified-listing'),
			'href'   => add_query_arg(array(
				'post_type' => rtcl()->post_type_payment
			), admin_url('edit.php')),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-payment', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($payment_args);

		$settings_args = array(
			'id'     => rtcl()->post_type . "-settings",
			'title'  => esc_html__('Settings', 'classified-listing'),
			'href'   => add_query_arg(array(
				'post_type' => rtcl()->post_type,
				'page'      => 'rtcl-settings'
			), admin_url('edit.php')),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-settings', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($settings_args);

		$settings_args = array(
			'id'     => rtcl()->post_type . "-clear-cache",
			'title'  => esc_html__('Clear all cache', 'classified-listing'),
			'href'   => add_query_arg([
				rtcl()->nonceId    => wp_create_nonce(rtcl()->nonceText),
				'clear_rtcl_cache' => ''
			], Link::get_current_url()),
			'parent' => rtcl()->post_type,
			'meta'   => array(
				'class' => sprintf('%s-admin-toolbar-settings', rtcl()->post_type)
			)
		);

		$wp_admin_bar->add_menu($settings_args);

		do_action('rtcl_admin_bar_menu', $wp_admin_bar, rtcl()->post_type);
	}


	public function add_listing_types_menu() {
		add_submenu_page(
			'edit.php?post_type=' . rtcl()->post_type,
			__('Listing Types', 'classified-listing'),
			__('Listing Types', 'classified-listing'),
			'manage_rtcl_options',
			'rtcl-listing-type',
			array($this, 'display_listing_type')
		);
	}

	public function add_import_menu() {
		add_submenu_page(
			'edit.php?post_type=' . rtcl()->post_type,
			__('Import', 'classified-listing'),
			__('Import', 'classified-listing'),
			'manage_rtcl_reports',
			'rtcl-import-export',
			array($this, 'display_import_export')
		);
	}

	public function add_addons_themes__menu() {
		add_submenu_page(
			'edit.php?post_type=' . rtcl()->post_type,
			__('Get Extensions', 'classified-listing'),
			__('<span>Themes & Extensions</span>', 'classified-listing'),
			'manage_options',
			'rtcl-extension',
			[$this, 'display_extension_view']
		);
	}

	public function add_settings_menu() {

		add_submenu_page(
			'edit.php?post_type=' . rtcl()->post_type,
			__('Settings', 'classified-listing'),
			__('Settings', 'classified-listing'),
			'manage_rtcl_options',
			'rtcl-settings',
			[$this, 'display_settings_form']
		);

	}

	function display_listing_type() {
		require_once RTCL_PATH . 'views/settings/listing-type.php';
	}

	function display_settings_form() {
		require_once RTCL_PATH . 'views/settings/admin-settings-display.php';
	}

	function display_import_export() {
		require_once RTCL_PATH . 'views/settings/import-export.php';
	}

	function display_extension_view() {
		require_once RTCL_PATH . 'views/settings/extensions/extension.php';
	}

	function setup_settings() {
		if ($this->active_tab == 'payment' && $this->current_section && array_key_exists($this->current_section, $this->subtabs)) {
			$gateway = Functions::get_payment_gateway($this->current_section);
			if ($gateway) {
				$gateway->init_form_fields();
				$gateway->option = $this->option;
				$this->form_fields = $gateway->form_fields;
			}
		} else {
			$this->set_fields();
		}

		$this->admin_options();
	}

	function set_fields() {
		$field = [];
		if ($this->active_tab && $this->current_section && array_key_exists($this->active_tab, $this->tabs) && array_key_exists($this->current_section, $this->subtabs)) {
			$file_name = RTCL_PATH . "views/settings/{$this->active_tab}-{$this->current_section}-settings.php";
		} else {
			$file_name = RTCL_PATH . "views/settings/{$this->active_tab}-settings.php";
		}
		if (file_exists($file_name)) {
			$field = include($file_name);
		}

		$this->form_fields = apply_filters('rtcl_settings_option_fields', $field, $this->active_tab, $this->current_section);
	}

	protected function add_subsections() {
		if (!$this->active_tab) {
			return;
		}
		if (method_exists($this, $this->active_tab . '_add_subsections')) {
			$this->{$this->active_tab . '_add_subsections'}();
		} else {
			$sub_sections = apply_filters('rtcl_' . $this->active_tab . '_sub_sections', []);
			if (is_array($sub_sections) && !empty($sub_sections)) {
				$this->subtabs = $sub_sections;
			}
		}
	}

	protected function general_add_subsections() {
		$sub_sections = [
			''          => esc_html__("General", 'classified-listing'),
			'directory' => esc_html__("Directory", 'classified-listing')
		];
		$sub_sections = apply_filters('rtcl_general_sub_sections', $sub_sections);
		$this->subtabs = $sub_sections;
	}

	protected function payment_add_subsections() {
		$sections = ['' => esc_html__("Checkout option", 'classified-listing')];
		$payment_gateways = rtcl()->payment_gateways();
		foreach ($payment_gateways as $gateway) {
			$title = empty($gateway->method_title) ? ucfirst($gateway->id) : $gateway->method_title;
			$sections[strtolower($gateway->id)] = esc_html($title);
		}
		$this->subtabs = $sections;
	}

	public function payment_sub_section_section_callback() {
		echo "<p>" . wp_kses($this->gateway_temp_desc, ['a' => array('href' => array(), 'title' => array())]) . "</p>";
	}

	public function save() {
		if ('POST' !== $_SERVER['REQUEST_METHOD']
			|| !isset($_REQUEST['post_type'])
			|| !isset($_REQUEST['page'])
			|| (isset($_REQUEST['post_type']) && rtcl()->post_type !== $_REQUEST['post_type'])
			|| (isset($_REQUEST['rtcl_settings']) && 'rtcl_settings' !== $_REQUEST['rtcl_settings'])
		) {
			return;
		}
		if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'rtcl-settings')) {
			die(__('Action failed. Please refresh the page and retry.', 'classified-listing'));
		}
		if ($this->active_tab === 'payment' && $this->current_section && array_key_exists($this->current_section, $this->subtabs)) {
			$gateway = Functions::get_payment_gateway($this->current_section);
			if ($gateway) {
				$gateway->init_form_fields();
				$gateway->option = $this->option;
				$this->form_fields = $gateway->form_fields;
			}
		} else {
			$this->set_fields();
		}
		$this->process_admin_options();

		self::add_message(__('Your settings have been saved.', 'classified-listing'));

		// Clear any unwanted data and flush rules.
		update_option('rtcl_queue_flush_rewrite_rules', 'yes');
		rtcl()->query->init_query_vars();
		rtcl()->query->add_endpoints();

		do_action('rtcl_admin_settings_saved', $this->option, $this);
	}

	function setTabs() {
		$this->tabs = [
			'general'    => esc_html__('General', 'classified-listing'),
			'moderation' => esc_html__('Moderation', 'classified-listing'),
			'payment'    => esc_html__('Payment', 'classified-listing'),
			'email'      => esc_html__('Email', 'classified-listing'),
			'account'    => esc_html__('Account & Policy', 'classified-listing'),
			'style'      => esc_html__('Style', 'classified-listing'),
			'misc'       => esc_html__('Misc', 'classified-listing'),
			'advanced'   => esc_html__('Advanced', 'classified-listing'),
			'tools'      => esc_html__('Tools', 'classified-listing')
		];
		// Hook to register custom tabs
		$this->tabs = apply_filters('rtcl_register_settings_tabs', $this->tabs);

		// Find the active tab
		$this->option = $this->active_tab = !empty($_GET['tab']) && array_key_exists($_GET['tab'], $this->tabs) ? trim($_GET['tab']) : 'general';
		$this->add_subsections();

		if (!empty($this->subtabs)) {
			$this->current_section = !empty($_GET['section']) && array_key_exists($_GET['section'], $this->subtabs) ? trim($_GET['section']) : '';
			$this->option = $this->current_section ? $this->option . '_' . $this->current_section : $this->active_tab;
			$this->option .= "_settings";
			if ($this->active_tab === 'payment' && $this->current_section) {
				$this->option = str_replace("_settings", "", $this->option);
			}
		} else {
			$this->option = $this->option . "_settings";
		}
		if ($this->active_tab && !empty($this->classMap[$this->active_tab])) {
			new $this->classMap[$this->active_tab]($this);
		}

	}

	public function preview_emails() {
		if (isset($_GET['preview_rtcl_mail'])) {
			if (!(isset($_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'preview-mail'))) {
				die('Security check');
			}

			// load the mailer class.
			$mailer = rtcl()->mailer();

			// get the preview email subject.
			$email_heading = __('HTML email template', 'classified-listing');

			// get the preview email content.
			ob_start();
			include(RTCL_PATH . "views/html-email-template-preview.php");
			$message = ob_get_clean();

			// create a new email.
			$email = new RtclEmail();
			$email->set_heading($email_heading);

			// wrap the content with the email template and then add styles.
			$message = apply_filters('rtcl_mail_content', $email->style_inline($mailer->wrap_message($message, $email)));

			// print the preview email.
			// phpcs:ignore WordPress.Security.EscapeOutput
			echo $message;
			// phpcs:enable
			exit;
		}
	}

	public static function generate_rest_api_key() {
		if (isset($_GET['rtcl_generate_rest_api_key'])) {
			if (!(isset($_REQUEST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'rtcl_generate_rest_api_key'))) {
				Functions::add_notice(__("You are not allow to make this request.", "classified-listing"), 'error');
			} else {
				$apikey = get_option('rtcl_rest_api_key', null);
				if ($apikey && wp_is_uuid($apikey)) {
					Functions::add_notice(__("Your Rest API key already generated", "classified-listing"), 'error');
				} else {
					update_option('rtcl_rest_api_key', wp_generate_uuid4());
				}
			}
			wp_safe_redirect(admin_url('edit.php?post_type=' . rtcl()->post_type . '&page=rtcl-settings&tab=tools'));
			exit();
		}
	}

	public function maxMindDatabaseService() {
		$this->maxMindDatabaseService = apply_filters('rtcl_maxmind_geolocation_database_service', null);
		if (null === $this->maxMindDatabaseService) {
			$prefix = $this->get_option('maxmind_database_prefix');
			if (empty($prefix)) {
				$prefix = wp_generate_password(32, false);
				$this->update_option('maxmind_database_prefix', $prefix);
			}
			$this->maxMindDatabaseService = new MaxMindDatabaseService($prefix);
		}
		return $this->maxMindDatabaseService;
	}
}
