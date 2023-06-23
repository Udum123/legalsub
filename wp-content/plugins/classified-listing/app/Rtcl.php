<?php

require_once RTCL_PATH . 'vendor/autoload.php';

use Rtcl\Controllers\Admin\AdminController;
use Rtcl\Controllers\Admin\LicensingController;
use Rtcl\Controllers\Admin\NoticeController;
use Rtcl\Controllers\Ajax\Ajax;
use Rtcl\Controllers\BlockController;
use Rtcl\Controllers\ElementorController;
use Rtcl\Controllers\GeoQuery;
use Rtcl\Controllers\Hooks\ActionHooks;
use Rtcl\Controllers\Hooks\AdminHooks;
use Rtcl\Controllers\Hooks\AfterSetupTheme;
use Rtcl\Controllers\Hooks\AppliedBothEndHooks;
use Rtcl\Controllers\Hooks\Comments;
use Rtcl\Controllers\Hooks\FilterHooks;
use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Controllers\Hooks\TemplateLoader;
use Rtcl\Controllers\PublicAction;
use Rtcl\Controllers\Query;
use Rtcl\Controllers\RtclApi;
use Rtcl\Controllers\SessionHandler;
use Rtcl\Controllers\Shortcodes;
use Rtcl\Helpers\Cache;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Installer;
use Rtcl\Helpers\Upgrade;
use Rtcl\Interfaces\LoggerInterface;
use Rtcl\Log\Logger;
use Rtcl\Log\LogLevel;
use Rtcl\Models\Cart;
use Rtcl\Models\Checkout;
use Rtcl\Models\Countries;
use Rtcl\Models\Factory;
use Rtcl\Models\PaymentGateways;
use Rtcl\Models\RtclEmails;
use Rtcl\ThemeSupports\ThemeSupports;
use Rtcl\Traits\SingletonTrait;
use Rtcl\Widgets\Widget;

if ( ! class_exists( Rtcl::class ) ) {

	/**
	 * Classified listing main class.
	 */
	final class Rtcl {
		use SingletonTrait;

		/**
		 * Query instance.
		 *
		 * @var Query
		 */
		public $query;

		/**
		 * Factory instance.
		 *
		 * @var Factory
		 */
		public $factory;

		/**
		 * Main post_type.
		 *
		 * @var string
		 */
		public $post_type = 'rtcl_listing';

		public $post_type_cfg = 'rtcl_cfg';

		public $post_type_cf = 'rtcl_cf';

		public $post_type_payment = 'rtcl_payment';

		public $post_type_pricing = 'rtcl_pricing';

		public $category = 'rtcl_category';

		public $location = 'rtcl_location';

		public $nonceId = '__rtcl_wpnonce';

		public $nonceText = 'rtcl_nonce_secret';

		public $gallery = [];

		public $upload_directory = 'classified-listing';

		/**
		 * @var SessionHandler object
		 */
		public $session = null;

		/**
		 * @var Cart object
		 */
		public $cart = null;

		protected $api_version = 'v1';

		private $listing_types_option = 'rtcl_listing_types';

		private $cache_prefix = 'rtcl_cache';

		/**
		 * @var Countries
		 */
		public $countries = null;

		/**
		 * Auto-load in-accessible properties on demand.
		 *
		 * @param mixed $key key name
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			if ( in_array( $key, [ 'init', 'payment_gateways', 'plugins_loaded', 'mailer', 'checkout' ], true ) ) {
				return $this->{$key}();
			}
		}

		/**
		 * Classified Listing Constructor.
		 */
		protected function __init() {
			$this->define_constants();
			// Add GEO Location Query feature
			if ( apply_filters( 'rtcl_geo_query', true ) ) {
				GeoQuery::Instance();
			}
			Widget::init();
			Cache::init();
			// Add Admin hook
			if ( $this->is_request( 'admin' ) ) {
				AdminHooks::init();
				Upgrade::init();
				Comments::init();
				new NoticeController();
			}

			// add hook for both
			AppliedBothEndHooks::init();
			FilterHooks::init();
			ActionHooks::init();
			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_hook();
			}

			ThemeSupports::init();
			$this->query = new Query();
			$api         = new RtclApi();
			$api->init();
			$this->load_hooks();

			// Init Elementor
			ElementorController::init();
			// Gutenberg Block init
			new BlockController();
			new LicensingController();
		}

		public function init_hooks() {
			do_action( 'rtcl_before_init', $this );

			$this->load_language();
			$this->factory   = new Factory();
			$this->countries = new Countries();

			new AdminController();
			new Ajax();
			new PublicAction();
			if ( $this->is_request( 'frontend' ) ) {
				$this->initialize_session();
				$this->initialize_cart();
			}


			$this->load_url_message();
			Installer::init();
			do_action( 'rtcl_init', $this );
		}

		public function on_plugins_loaded() {
			do_action( 'rtcl_loaded', $this );
		}

		public function db() {
			global $wpdb;

			return $wpdb;
		}

		/**
		 * Get a shared logger instance.
		 *
		 * @param array $options
		 *
		 * @return Logger
		 *
		 * @see LoggerInterface
		 */
		public function logger( $logLevelThreshold = LogLevel::DEBUG, $options = [] ) {
			static $logger = null;
			$class = apply_filters( 'rtcl_logging_class', Logger::class );

			if ( null !== $logger && is_string( $class ) && is_a( $logger, $class ) ) {
				return $logger;
			}

			$implements = class_implements( $class );

			if ( is_array( $implements ) && in_array( LoggerInterface::class, $implements, true ) ) {
				$logger = is_object( $class ) ? $class : new $class();
			} else {
				$logger = is_a( $logger, Logger::class ) ? $logger : new Logger( $logLevelThreshold, $options );
			}

			return $logger;
		}

		/**
		 * Load Localisation files.
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 * Locales found in:
		 *      - WP_LANG_DIR/classified-listing/classified-listing-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/classified-listing-LOCALE.mo.
		 */
		public function load_language() {
			do_action( 'rtcl_set_local', null );
			$locale = determine_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'classified-listing' );
			unload_textdomain( 'classified-listing' );
			load_textdomain( 'classified-listing', WP_LANG_DIR . '/classified-listing/classified-listing-' . $locale . '.mo' );
			load_plugin_textdomain( 'classified-listing', false, plugin_basename( dirname( RTCL_PLUGIN_FILE ) ) . '/i18n/languages' );
		}

		/**
		 * Get gateways class.
		 *
		 * @return array
		 */
		public function payment_gateways() {
			return PaymentGateways::instance()->payment_gateways;
		}

		/**
		 * Email Class.
		 *
		 * @return bool|RtclEmails|SingletonTrait
		 */
		public function mailer() {
			return RtclEmails::getInstance();
		}

		/**
		 * What type of request is this?
		 *
		 * @param string $type admin, ajax, cron or frontend
		 *
		 * @return bool
		 */
		public function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name  constant name
		 * @param bool|string $value constant value
		 */
		public function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function get_template_path() {
			return apply_filters( 'rtcl_template_path', 'classified-listing/' );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RTCL_PLUGIN_FILE ) );
		}

		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Return the RTCL API URL for a given request.
		 *
		 * @param string    $request requested endpoint
		 * @param bool|null $ssl     If you should use SSL, null if should auto-detect. Default: null.
		 *
		 * @return string
		 */
		public function api_request_url( $request, $ssl = null ) {
			if ( is_null( $ssl ) ) {
				$scheme = wp_parse_url( home_url(), PHP_URL_SCHEME );
			} elseif ( $ssl ) {
				$scheme = 'https';
			} else {
				$scheme = 'http';
			}

			if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
				$api_request_url = trailingslashit( home_url( '/index.php/rtcl-api/' . $request, $scheme ) );
			} elseif ( get_option( 'permalink_structure' ) ) {
				$api_request_url = trailingslashit( home_url( '/rtcl-api/' . $request, $scheme ) );
			} else {
				$api_request_url = add_query_arg( 'rtcl-api', $request, trailingslashit( home_url( '', $scheme ) ) );
			}

			return esc_url_raw( apply_filters( 'rtcl_api_request_url', $api_request_url, $request, $ssl ) );
		}

		/**
		 * @return string
		 *
		 * @deprecated since 2.0.1  Use `RTCL_VERSION` instead
		 */
		public function version() {
			_deprecated_function( __METHOD__, '2.0.1', 'RTCL_VERSION' );

			return RTCL_VERSION;
		}

		public function get_listing_types_option_id() {
			return $this->listing_types_option;
		}

		/**
		 * @param string $api_version
		 *
		 * @return string
		 */
		public function get_api_prefix( $api_version = '' ) {
			$api_version = $api_version ?: $this->api_version;

			return 'rtcl/' . $api_version;
		}

		/**
		 * @param $file
		 *
		 * @return string
		 */
		public function get_assets_uri( $file ) {
			$file = ltrim( $file, '/' );

			return trailingslashit( RTCL_URL . '/assets' ) . $file;
		}

		public function wp_query() {
			global $wp_query;

			return $wp_query;
		}

		/**
		 * @param array|string $id
		 * @param string       $group
		 * @param string       $sub_group
		 *
		 * @return string
		 */
		public function get_transient_name( $id, $group, $sub_group = '' ) {
			$id = ! empty( $id ) && is_array( $id ) ? md5( wp_json_encode( $id ) ) : $id;
			if ( rtcl()->location === $group ) {
				$transient_name = sprintf( '%s_%s_%s_%s', $this->cache_prefix, rtcl()->location, $sub_group, $id );
			} elseif ( rtcl()->category === $group ) {
				$transient_name = sprintf( '%s_%s_%s_%s', $this->cache_prefix, rtcl()->category, $sub_group, $id );
			} else {
				$transient_name = sprintf( '%s_%s', $this->cache_prefix, microtime() );
			}

			return $transient_name . apply_filters( 'rtcl_transient_lang_prefix', '', $id, $group, $sub_group );
		}

		public function checkout() {
			return Checkout::getInstance();
		}

		/**
		 * Get cart object instance for online learning market.
		 *
		 * @return Cart
		 */
		public function get_cart() {
			$this->initialize_session();
			$this->initialize_cart();

			return $this->cart;
		}

		/**
		 * Api secret string.
		 *
		 * @return string
		 */
		public function getApiSecret() {
			return 'jdkahiu99n23948234n2329339nc248239';
		}

		/**
		 * @return string
		 */
		public function pro_tag() {
			if ( ! rtcl()->has_pro() ) {
				return '<span class="rtcl-pro">[PRO]</span>';
			}

			return '';
		}

		/**
		 * @return string
		 */
		public function getApiVersion() {
			return $this->api_version;
		}

		public function has_pro() {
			return class_exists( RtclPro::class );
		}

		private function frontend_hook() {
			TemplateHooks::init();
			add_action( 'init', [ TemplateLoader::class, 'init' ] );
		}

		private function load_hooks() {
			register_activation_hook( RTCL_PLUGIN_FILE, [ Installer::class, 'activate' ] );
			register_deactivation_hook( RTCL_PLUGIN_FILE, [ Installer::class, 'deactivate' ] );

			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ], - 1 );
			add_action( 'after_setup_theme', [ AfterSetupTheme::class, 'template_functions' ], 11 );
			add_action( 'init', [ $this, 'init_hooks' ], 0 );
			add_action( 'init', [ Shortcodes::class, 'init_short_code' ] ); // Init ShortCode.
		}

		public function initialize_session() {
			if ( ! is_a( $this->session, SessionHandler::class ) ) {
				$this->session = new SessionHandler();
				$this->session->init();
			}
		}

		public function initialize_cart() {
			$cart_class = apply_filters( 'rtcl_cart_class', Cart::class );

			if ( ! is_a( $this->cart, $cart_class ) && class_exists( $cart_class ) ) {
				$this->cart = is_callable( [ $cart_class, 'instance', ] ) ? call_user_func( [
					$cart_class,
					'instance'
				] ) : new $cart_class();
			}
		}

		private function define_constants() {

			if ( ! defined( 'RTCL_SLUG' ) ) {
				define( 'RTCL_SLUG', 'classified-listing' );
			}
			if ( ! defined( 'RTCL_SESSION_CACHE_GROUP' ) ) {
				define( 'RTCL_SESSION_CACHE_GROUP', 'rtcl_session_id' );
			}
			if ( ! defined( 'RTCL_TEMPLATE_DEBUG_MODE' ) ) {
				define( 'RTCL_TEMPLATE_DEBUG_MODE', false );
			}
			if ( ! defined( 'RTCL_ROUNDING_PRECISION' ) ) {
				define( 'RTCL_ROUNDING_PRECISION', 6 );
			}
		}

		private function load_url_message() {
			if ( isset( $_GET['rtcl-type'] ) && in_array( $_GET['rtcl-type'], [
					'success',
					'error'
				] ) && isset( $_GET['message'] ) ) {
				Functions::add_notice( trim( urldecode( $_GET['message'] ) ), trim( $_GET['rtcl-type'] ) );
			}
		}
	}

	/**
	 * @return Rtcl
	 */
	function rtcl() {
		return Rtcl::getInstance();
	}

	rtcl();
}
