<?php
/**
 * Rtcl License class file.
 *
 * @package classified-listing
 */

namespace Rtcl\Models;

use Rtcl\Helpers\Functions;
use stdClass;

/**
 * RtclLicense
 */
class RtclLicense {


	/** @var string */
	private $api_url = 'https://www.radiustheme.com';
	/** @var string */
	private $author = 'RadiusTheme';
	/** @var string */
	private $key_name = '';
	/** @var string */
	private $status_name = '';
	/** @var string */
	private $action_name = '';
	/** @var int */
	private $product_id = '';
	/** @var array */
	private $api_data = array();
	/** @var array */
	private $settings = array();
	/** @var string */
	private $name = '';
	/** @var string */
	private $slug = '';
	/** @var string */
	private $version = '';
	/** @var bol */
	private $wp_override = false;
	/** @var string */
	private $cache_key = '';
	/** @var string */
	private $beta = '';

	private $license_key = '';

	/**
	 * Class constructor.
	 *
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array $_api_data Optional data to send with API calls.
	 *
	 * @uses hook()
	 *
	 * @uses plugin_basename()
	 */
	public function __construct( $_plugin_file, $_api_data = array(), $settings = array() ) {

		global $edd_plugin_data;

		$_api_data = wp_parse_args(
			$_api_data,
			array(
				'key_name'    => '',
				'status_name' => '',
				'action_name' => '',
				'author'      => $this->author,
				'product_id'  => '',
				'wp_override' => false,
				'version'     => '',
				'api_url'     => $this->api_url,
				'beta'        => false,
			)
		);
		if ( ! Functions::check_license() || empty( $_api_data['product_id'] ) || empty( $_api_data['key_name'] ) || empty( $_api_data['status_name'] ) || empty( $_api_data['action_name'] ) ) {
			return;
		}
		$this->settings = wp_parse_args(
			$settings,
			array(
				'position_key' => 'licensing_section',
				'title'        => '',
			)
		);

		$this->key_name            = $_api_data['key_name'];
		$this->status_name         = $_api_data['status_name'];
		$this->action_name         = $_api_data['action_name'];
		$this->product_id          = $_api_data['product_id'];
		$settings                  = Functions::get_option( 'rtcl_tools_settings' );
		$this->api_data            = $_api_data;
		$this->api_data['license'] = ! empty( $settings[ $this->key_name ] ) ? trim( $settings[ $this->key_name ] ) : null;
		$this->api_data['status']  = ! empty( $settings[ $this->status_name ] ) && $settings[ $this->status_name ] === 'valid';
		$this->api_url             = trailingslashit( $_api_data['api_url'] );
		$this->name                = plugin_basename( $_plugin_file );
		$this->slug                = basename( $_plugin_file, '.php' );
		$this->version             = $_api_data['version'];
		$this->wp_override         = (bool) $_api_data['wp_override'];
		$this->beta                = ! empty( $_api_data['beta'] );
		$this->cache_key           = md5( serialize( $this->slug . $this->api_data['license'] . $this->beta ) );

		$edd_plugin_data[ $this->slug ] = $this->api_data;

		// Set up hooks.
		$this->init();

	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @return void
	 * @uses add_filter()
	 */
	public function init() {
		add_action( 'rtcl_admin_settings_saved', array( $this, 'update_licensing_status' ) );
		add_action( 'wp_ajax_' . $this->action_name, array( &$this, 'manage_licensing' ) );
		add_filter( 'rtcl_tools_settings_options', array( &$this, 'add_tools_licensing_options' ), 15 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row' );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification_key_check' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
	}

	function update_licensing_status( $action ) {
		if ( 'tools_settings' == $action ) {
			$settings    = Functions::get_option( 'rtcl_tools_settings' );
			$license_key = ! empty( $settings[ $this->key_name ] ) ? trim( $settings[ $this->key_name ] ) : null;
			$status      = ( ! empty( $settings[ $this->status_name ] ) && $settings[ $this->status_name ] === 'valid' ) ? true : false;
			if ( $license_key && ! $status ) {
				$api_params = array(
					'edd_action' => 'activate_license',
					'license'    => $license_key,
					'item_id'    => $this->product_id,
					'url'        => home_url(),
				);
				$response   = wp_remote_post(
					$this->api_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					)
				);
				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					if ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) {
						$message = $response->get_error_message();
					} elseif ( isset( $response['response'] ) && is_array( $response['response'] ) && isset( $response['response']['message'] ) ) {
						$message = 'sss' . $response['response']['message'];
					} else {
						$message = esc_html__( 'An error occurred, please try again.', 'classified-listing' );
					}
					Functions::add_notice( $message ? $message : esc_html__( 'Error to activation license', 'classified-listing' ), 'error' );
				} else {
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( false === $license_data->success ) {
						switch ( $license_data->error ) {
							case 'expired':
								$message = sprintf(
									esc_html__( 'Your license key expired on %s.', 'classified-listing' ),
									date_i18n(
										get_option( 'date_format' ),
										strtotime( $license_data->expires, current_time( 'timestamp' ) )
									)
								);
								break;
							case 'revoked':
								$message = esc_html__( 'Your license key has been disabled.', 'classified-listing' );
								break;
							case 'missing':
								$message = esc_html__( 'Invalid license.', 'classified-listing' );
								break;
							case 'invalid':
							case 'site_inactive':
								$message = esc_html__( 'Your license is not active for this URL.', 'classified-listing' );
								break;
							case 'item_name_mismatch':
								$message = esc_html__( "This appears to be an invalid license key for {$this->settings['title']}.", 'classified-listing' );
								break;
							case 'no_activations_left':
								$message = esc_html__( 'Your license key has reached its activation limit.', 'classified-listing' );
								break;
							default:
								$message = esc_html__( 'An error occurred, please try again.', 'classified-listing' );
								break;
						}
					}
					// Check if anything passed on a message constituting a failure
					if ( empty( $message ) && $license_data->license === 'valid' ) {
						$settings[ $this->status_name ] = $license_data->license;
						update_option( 'rtcl_tools_settings', $settings );
						Functions::add_notice( esc_html__( 'Successfully activated', 'classified-listing' ), 'success' );
					} else {
						Functions::add_notice( $message ? $message : esc_html__( 'Error to activation license', 'classified-listing' ), 'error' );
					}
				}
			} elseif ( ! $license_key && ! $status ) {
				unset( $settings[ $this->key_name ] );
				update_option( 'rtcl_tools_settings', $settings );
			}
		}
	}

	public function manage_licensing() {
		$error       = true;
		$type        = $value = $data = $message = null;
		$settings    = Functions::get_option( 'rtcl_tools_settings' );
		$license_key = ! empty( $settings[ $this->key_name ] ) ? trim( $settings[ $this->key_name ] ) : null;
		if ( ! empty( $_REQUEST['type'] ) && $_REQUEST['type'] == 'license_activate' ) {
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license_key,
				'item_id'    => $this->product_id,
				'url'        => home_url(),
			);
			$response   = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$err     = $response->get_error_message();
				$message = ( is_wp_error( $response ) && ! empty( $err ) ) ? $err : esc_html__( 'An error occurred, please try again.', 'classified-listing' );
			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( false === $license_data->success ) {
					switch ( $license_data->error ) {
						case 'expired':
							$message = sprintf(
								esc_html__( 'Your license key expired on %s.', 'classified-listing' ),
								date_i18n(
									get_option( 'date_format' ),
									strtotime( $license_data->expires, current_time( 'timestamp' ) )
								)
							);
							break;
						case 'revoked':
							$message = esc_html__( 'Your license key has been disabled.', 'classified-listing' );
							break;
						case 'missing':
							$message = esc_html__( 'Invalid license.', 'classified-listing' );
							break;
						case 'invalid':
						case 'site_inactive':
							$message = esc_html__( 'Your license is not active for this URL.', 'classified-listing' );
							break;
						case 'item_name_mismatch':
							$message = esc_html__( "This appears to be an invalid license key for {$this->settings['title']}.", 'classified-listing' );
							break;
						case 'no_activations_left':
							$message = esc_html__( 'Your license key has reached its activation limit.', 'classified-listing' );
							break;
						default:
							$message = esc_html__( 'An error occurred, please try again.', 'classified-listing' );
							break;
					}
				}
				// Check if anything passed on a message constituting a failure
				if ( empty( $message ) ) {
					$settings[ $this->status_name ] = $license_data->license;
					update_option( 'rtcl_tools_settings', $settings );
					$error   = false;
					$type    = 'license_deactivate';
					$message = esc_html__( 'License successfully activated', 'classified-listing' );
					$value   = esc_html__( 'Deactivate License', 'classified-listing' );
				}
			}
		}
		if ( ! empty( $_REQUEST['type'] ) && $_REQUEST['type'] == 'license_deactivate' ) {
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license_key,
				'item_id'    => $this->product_id,
				'url'        => home_url(),
			);
			$response   = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$err     = $response->get_error_message();
				$message = ( is_wp_error( $response ) && ! empty( $err ) ) ? $err : esc_html__( 'An error occurred, please try again.', 'classified-listing' );
			} else {
				unset( $settings[ $this->status_name ] );
				update_option( 'rtcl_tools_settings', $settings );
				$error   = false;
				$type    = 'license_activate';
				$message = esc_html__( 'License successfully deactivated', 'classified-listing' );
				$value   = esc_html__( 'Activate License', 'classified-listing' );
			}
		}
		$response = array(
			'error' => $error,
			'msg'   => $message,
			'type'  => $type,
			'value' => $value,
			'data'  => $data,
		);
		wp_send_json( $response );
	}

	public function add_tools_licensing_options( $options ) {
		if ( empty( $this->settings['title'] ) ) {
			return;
		}
		$position = array_search( $this->settings['position_key'], array_keys( $options ) );
		if ( $position > - 1 ) {
			$settings       = Functions::get_option( 'rtcl_tools_settings' );
			$status         = ! empty( $settings[ $this->status_name ] ) && $settings[ $this->status_name ] === 'valid';
			$license_status = ! empty( $settings[ $this->key_name ] ) ? sprintf(
				"<span class='license-status'>%s</span>",
				$status ? "<span data-action='" . $this->action_name . "' class='button-secondary rt-licensing-btn danger license_deactivate'>" . esc_html__( 'Deactivate License', 'classified-listing' ) . '</span>'
					: "<span data-action='" . $this->action_name . "' class='button-secondary rt-licensing-btn button-primary license_activate'>" . esc_html__( 'Activate License', 'classified-listing' ) . '</span>'
			) : ' ';
			$option         = array(
				$this->key_name => array(
					'title'         => $this->settings['title'],
					'type'          => 'text',
					'wrapper_class' => 'rtcl-license-wrapper',
					'description'   => $license_status,
				),
			);
			Functions::array_insert( $options, $position, $option );
		}

		return $options;
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @param array $_transient_data Update array build by WordPress.
	 *
	 * @return array Modified update array with custom plugin data.
	 * @uses api_request()
	 */
	public function check_update( $_transient_data ) {

		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass();
		}

		if ( 'plugins.php' == $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			return $_transient_data;
		}

		$version_info = false;//$this->get_cached_version_info();

		if ( false === $version_info ) {
			$version_info = $this->api_request(
				'plugin_latest_version',
				array(
					'slug' => $this->slug,
					'beta' => $this->beta,
				)
			);

			$this->set_version_info_cache( $version_info );

		}

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$_transient_data->response[ $this->name ] = $version_info;

			}

			$_transient_data->last_checked           = current_time( 'timestamp' );
			$_transient_data->checked[ $this->name ] = $this->version;

		}

		return $_transient_data;
	}

	public function show_update_notification_key_check( $file, $plugin ) {

		if ( is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );

		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$version_info = $this->get_cached_version_info();

			if ( false === $version_info ) {
				$version_info = $this->api_request(
					'plugin_latest_version',
					array(
						'slug' => $this->slug,
						'beta' => $this->beta,
					)
				);

				$this->set_version_info_cache( $version_info );
			}

			if ( ! is_object( $version_info ) ) {
				return;
			}

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked           = current_time( 'timestamp' );
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		$status = ! empty( $this->api_data['status'] );
		if ( ! $this->api_data['license'] || ! $status ) {
			echo '<tr class="plugin-update-tr" id="' . $this->slug . '-update" data-slug="' . $this->slug . '" data-plugin="' . $this->slug . '/' . $file . '">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div class="update-message notice inline notice-warning notice-alt"><p><strong>' . esc_html__( 'Please enter valid license key for automatic updates.', 'classified-listing' ) . '</strong> <a href="' . admin_url( 'edit.php?post_type=rtcl_listing&page=rtcl-settings&tab=tools' ) . '">' . esc_html__( 'Click here', 'classified-listing' ) . '</a></p>';
			echo '</div></td></tr>';
		} else {
			if ( empty( $version_info->new_version ) && empty( $version_info->stable_version ) && empty( $version_info->sections ) && empty( $version_info->license_check ) && isset( $version_info->msg ) ) {
				echo '<tr class="plugin-update-tr" id="' . $this->slug . '-update" data-slug="' . $this->slug . '" data-plugin="' . $this->slug . '/' . $file . '">';
				echo '<td colspan="3" class="plugin-update colspanchange">';
				echo '<div class="update-message notice inline notice-warning notice-alt"><p>' . $version_info->msg . '. <strong>' . esc_html__( 'Please enter valid license key for automatic updates.', 'classified-listing' ) . '</strong><a href="' . admin_url( 'edit.php?post_type=rtcl_listing&page=rtcl-settings&tab=tools' ) . '">' . esc_html__( 'Click here', 'classified-listing' ) . '</a></p>';
				echo '</div></td></tr>';
			}
		}

	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string $file
	 * @param array $plugin
	 */
	public function show_update_notification( $file, $plugin ) {

		if ( is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );

		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$version_info = $this->get_cached_version_info();

			if ( false === $version_info ) {
				$version_info = $this->api_request(
					'plugin_latest_version',
					array(
						'slug' => $this->slug,
						'beta' => $this->beta,
					)
				);

				$this->set_version_info_cache( $version_info );
			}

			if ( ! is_object( $version_info ) ) {
				return;
			}

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked           = current_time( 'timestamp' );
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			// <tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
			echo '<tr class="plugin-update-tr" id="' . $this->slug . '-update" data-slug="' . $this->slug . '" data-plugin="' . $this->slug . '/' . $file . '">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div class="update-message notice inline notice-warning notice-alt">';

			$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					__( 'There is a new version of %1$s available. %2$sView version %3$s details%4$s.', 'classified-listing' ),
					esc_html( $version_info->name ),
					'<a target="_blank" class="thickbox" href="' . esc_url( $changelog_link ) . '">',
					esc_html( $version_info->new_version ),
					'</a>'
				);
			} else {
				printf(
					__( 'There is a new version of %1$s available. %2$sView version %3$s details%4$s or %5$supdate now%6$s.', 'classified-listing' ),
					esc_html( $version_info->name ),
					'<a target="_blank" class="thickbox" href="' . esc_url( $changelog_link ) . '">',
					esc_html( $version_info->new_version ),
					'</a>',
					'<a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) ) . '">',
					'</a>'
				);
			}

			do_action( "in_plugin_update_message-{$file}", $plugin, $version_info );

			echo '</div></td></tr>';
		}
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @param mixed $_data
	 * @param string $_action
	 * @param object $_args
	 *
	 * @return object $_data
	 * @uses api_request()
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

		if ( $_action != 'plugin_information' ) {

			return $_data;

		}

		if ( ! isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {

			return $_data;

		}

		$to_send = array(
			'slug'   => $this->slug,
			'is_ssl' => is_ssl(),
			'fields' => array(
				'banners' => array(),
				'reviews' => false,
			),
		);

		$cache_key = 'edd_api_request_' . md5( serialize( $this->slug . $this->api_data['license'] . $this->beta ) );

		// Get the transient where we store the api request for this plugin for 24 hours
		$edd_api_request_transient = $this->get_cached_version_info( $cache_key );

		// If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
		if ( empty( $edd_api_request_transient ) ) {

			$api_response = $this->api_request( 'plugin_information', $to_send );

			// Expires in 3 hours
			$this->set_version_info_cache( $api_response, $cache_key );

			if ( false !== $api_response ) {
				$_data = $api_response;
			}
		} else {
			$_data = $edd_api_request_transient;
		}

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->sections ) && ! is_array( $_data->sections ) ) {
			$new_sections = array();
			foreach ( $_data->sections as $key => $value ) {
				$new_sections[ $key ] = $value;
			}

			$_data->sections = $new_sections;
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->banners ) && ! is_array( $_data->banners ) ) {
			$new_banners = array();
			foreach ( $_data->banners as $key => $value ) {
				$new_banners[ $key ] = $value;
			}

			$_data->banners = $new_banners;
		}

		return $_data;
	}

	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array $args
	 * @param string $url
	 *
	 * @return array $array
	 */
	public function http_request_args( $args, $url ) {

		$verify_ssl = $this->verify_ssl();
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
			$args['sslverify'] = $verify_ssl;
		}

		return $args;

	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @param string $_action The requested action.
	 * @param array $_data Parameters for the API action.
	 *
	 * @return false|object
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version;

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug ) {
			return;
		}

		if ( $this->api_url == trailingslashit( home_url() ) ) {
			return false; // Don't allow a plugin to ping itself
		}

		$api_params = array(
			'edd_action' => 'get_version',
			'license'    => ! empty( $data['license'] ) ? $data['license'] : '',
			'item_id'    => $this->product_id,
			'version'    => isset( $data['version'] ) ? $data['version'] : false,
			'slug'       => $this->slug,
			'author'     => $data['author'],
			'url'        => home_url(),
			'beta'       => ! empty( $data['beta'] ),
		);

		$verify_ssl = $this->verify_ssl();
		$request    = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		}

		if ( $request && isset( $request->sections ) ) {
			$request->sections = maybe_unserialize( $request->sections );
		} else {
			$request = false;
		}

		if ( $request && isset( $request->banners ) ) {
			$request->banners = maybe_unserialize( $request->banners );
		}

		if ( ! empty( $request->sections ) ) {
			foreach ( $request->sections as $key => $section ) {
				$request->$key = (array) $section;
			}
		}

		return $request;
	}

	public function show_changelog() {

		global $edd_plugin_data;

		if ( empty( $_REQUEST['edd_sl_action'] ) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action'] ) {
			return;
		}

		if ( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to install plugin updates', 'classified-listing' ), esc_html__( 'Error', 'classified-listing' ), array( 'response' => 403 ) );
		}

		$data         = $edd_plugin_data[ $_REQUEST['slug'] ];
		$beta         = ! empty( $data['beta'] );
		$cache_key    = md5( 'edd_plugin_' . sanitize_key( $_REQUEST['plugin'] ) . '_' . $beta . '_version_info' );
		$version_info = $this->get_cached_version_info( $cache_key );

		if ( false === $version_info ) {

			$api_params = array(
				'edd_action' => 'get_version',
				'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
				'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
				'slug'       => $_REQUEST['slug'],
				'author'     => $data['author'],
				'url'        => home_url(),
				'beta'       => ! empty( $data['beta'] ),
			);

			$verify_ssl = $this->verify_ssl();
			$request    = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => $verify_ssl,
					'body'      => $api_params,
				)
			);

			if ( ! is_wp_error( $request ) ) {
				$version_info = json_decode( wp_remote_retrieve_body( $request ) );
			}

			if ( ! empty( $version_info ) && isset( $version_info->sections ) ) {
				$version_info->sections = maybe_unserialize( $version_info->sections );
			} else {
				$version_info = false;
			}

			if ( ! empty( $version_info ) ) {
				foreach ( $version_info->sections as $key => $section ) {
					$version_info->$key = (array) $section;
				}
			}

			$this->set_version_info_cache( $version_info, $cache_key );

		}

		if ( ! empty( $version_info ) && isset( $version_info->sections['changelog'] ) ) {
			echo '<div style="background:#fff;padding:10px;">' . $version_info->sections['changelog'] . '</div>';
		}

		exit;
	}

	public function get_cached_version_info( $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		return json_decode( $cache['value'] );

	}

	public function set_version_info_cache( $value = '', $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+3 hours', current_time( 'timestamp' ) ),
			'value'   => json_encode( $value ),
		);

		update_option( $cache_key, $data, 'no' );

	}

	/**
	 * Returns if the SSL of the store should be verified.
	 *
	 * @return bool
	 * @since  1.6.13
	 */
	private function verify_ssl() {
		return (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true, $this );
	}
}
