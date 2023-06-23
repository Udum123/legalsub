<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link    https://pluginsware.com
 * @since   1.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Admin Class.
 *
 * @since 1.0.0
 */
class ACADP_Admin {

	/**
	 * Check and update plugin options to the latest version.
	 *
	 * @since 1.5.6
	 */
	public function manage_upgrades() {		
		if ( ACADP_VERSION_NUM !== get_option( 'acadp_version' ) ) {
			// Update general settings
			$general_settings = get_option( 'acadp_general_settings' );

			if ( ! isset( $general_settings['required_fields'] ) ) {			
				if ( isset( $general_settings['is_image_required'] ) && ! empty( $general_settings['is_image_required'] ) ) {
					$general_settings['required_fields'] = array( 'image' );
				} else {
					$general_settings['required_fields'] = array();
				}

				update_option( 'acadp_general_settings', $general_settings );				
			}

			// Insert listing, badges settings
			if ( false == get_option( 'acadp_listing_settings' ) ) {	
				// Insert listing settings
				$email_settings = get_option( 'acadp_email_settings' );

				$show_phone_number = 'closed';
				if ( isset( $general_settings['show_phone_number_publicly'] ) ) {
					$show_phone_number = 'open';
				}
	
				$show_email_address = 'registered';
				if ( isset( $general_settings['show_email_address_publicly'] ) ) {
					$show_email_address = 'public';
				} else {
					if ( version_compare( ACADP_VERSION_NUM, '1.7.3', '<=' ) ) {	
						if ( isset( $email_settings['show_email_address_publicly'] ) ) {					
							$show_email_address = 'public';
						}			
					}
				}
	
				$defaults = array(
					'show_phone_number'          => $show_phone_number,	
					'show_email_address'         => $show_email_address,							
					'has_contact_form'           => (int) $general_settings['has_contact_form'],
					'contact_form_require_login' => (int) $general_settings['contact_form_require_login'],
					'has_comment_form'           => (int) $general_settings['has_comment_form'],	
					'has_report_abuse'           => (int) $general_settings['has_report_abuse'],
					'has_favourites'             => (int) $general_settings['has_favourites'],
					'display_options'            => array_map( 'sanitize_text_field', $general_settings['display_options'] )
				);
	
				update_option( 'acadp_listing_settings', $defaults );	

				// Insert badges settings
				$defaults = array(
					'show_new_tag'              => (int) $general_settings['show_new_tag'],
					'new_listing_threshold'     => (int) $general_settings['new_listing_threshold'],
					'new_listing_label'         => sanitize_text_field( $general_settings['new_listing_label'] ),
					'show_popular_tag'          => (int) $general_settings['show_popular_tag'],
					'popular_listing_threshold' => (int) $general_settings['popular_listing_threshold'],
					'popular_listing_label'     => sanitize_text_field( $general_settings['popular_listing_label'] ),
					'mark_as_sold'              => 0,
					'sold_listing_label'        => __( 'Sold', 'advanced-classifieds-and-directory-pro' )
				);
	
				update_option( 'acadp_badges_settings', $defaults );
			}
	
			// Insert the misc settings
			$misc_settings = get_option( 'acadp_misc_settings' );

			if ( empty( $misc_settings ) || ( is_array( $misc_settings ) && ! isset( $misc_settings['load_bootstrap'] ) ) ) {
				if ( empty( $misc_settings ) ) {
					$misc_settings = array(
						'delete_plugin_data' => 1,
						'delete_media_files' => 1
					);	
				}
					
				if ( isset( $general_settings['load_bootstrap'] ) ) {
					$misc_settings['load_bootstrap'] = $general_settings['load_bootstrap'];
				} else {
					$misc_settings['load_bootstrap'] = array( 'css', 'javascript' );
				}

				update_option( 'acadp_misc_settings', $misc_settings );
			}
			
			// Update the map settings
			$map_settings = get_option( 'acadp_map_settings' );

			if ( ! array_key_exists( 'service', $map_settings ) ) {	
				$map_settings['service'] = 'google';	
				update_option( 'acadp_map_settings', $map_settings );				
			}

			// Insert the GDPR Compliance settings
			if ( false == get_option( 'acadp_privacy_policy' ) ) {
				$defaults = array(
					'show_privacy_policy'  => 0,
					'privacy_policy_label' => __( 'I have read and agree to the Privacy Policy', 'advanced-classifieds-and-directory-pro' ),
					'privacy_policy_text'  => ''
				);
					
				update_option( 'acadp_privacy_policy', $defaults );
			}

			if ( false == get_option( 'acadp_cookie_consent' ) ) {
				$defaults = array(
					'show_cookie_consent'  => 0,
					'consent_message'      => __( '<strong>Please accept cookies to show this content</strong>. By accepting you will be accessing content from a service provided by an external third party.', 'advanced-classifieds-and-directory-pro' ),
					'consent_button_label' => __( 'Accept', 'advanced-classifieds-and-directory-pro' )
				);
					
				update_option( 'acadp_cookie_consent', $defaults );
			}

			// Update page settings
			$page_settings = get_option( 'acadp_page_settings' );

			if ( ! array_key_exists( 'login_form', $page_settings ) ) {			
				$pages = acadp_insert_custom_pages();
				update_option( 'acadp_page_settings', $pages );				
			}
			
			// Update plugin version
			update_option( 'acadp_version', ACADP_VERSION_NUM );		
		}
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$map_settings = get_option( 'acadp_map_settings' );

		$screen = get_current_screen();

		if ( isset( $screen->id ) && ( 'toplevel_page_advanced-classifieds-and-directory-pro' === $screen->id || 'widgets' === $screen->id ) ) {
			wp_enqueue_style( 'wp-color-picker' );	
		}
		
		if ( isset( $screen->post_type ) && ( 'acadp_listings' == $screen->post_type || 'acadp_fields' == $screen->post_type ) ) {
			wp_enqueue_style( 
				ACADP_PLUGIN_NAME . '-flatpickr', 
				ACADP_PLUGIN_URL . 'vendor/flatpickr/flatpickr.min.css',  
				array(), 
				'4.6.9', 
				'all' 
			);
		}		

		if ( isset( $screen->post_type ) && 'acadp_listings' == $screen->post_type ) {
			if ( 'osm' == $map_settings['service'] ) {
				wp_enqueue_style( 
					ACADP_PLUGIN_NAME . '-map', 
					ACADP_PLUGIN_URL . 'vendor/leaflet/leaflet.css', 
					array(), 
					'1.7.1', 
					'all' 
				);
			}
		}

		wp_enqueue_style( 
			ACADP_PLUGIN_NAME, 
			ACADP_PLUGIN_URL . 'admin/css/admin.css', 
			array(), 
			ACADP_VERSION_NUM, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$map_settings = get_option( 'acadp_map_settings' );

		$screen = get_current_screen();			
		
		wp_enqueue_media();
		
		if ( isset( $screen->id ) && ( 'toplevel_page_advanced-classifieds-and-directory-pro' === $screen->id || 'widgets' === $screen->id ) ) {
			wp_enqueue_script( 'wp-color-picker' );	
		}

		if ( isset( $screen->post_type ) && ( 'acadp_listings' == $screen->post_type || 'acadp_fields' == $screen->post_type ) ) {
			wp_enqueue_script( 
				ACADP_PLUGIN_NAME . '-flatpickr', 
				ACADP_PLUGIN_URL . 'vendor/flatpickr/flatpickr.min.js', 
				array( 'jquery' ), 
				'4.6.9', 
				false 
			);
		}
			
		if ( isset( $screen->post_type ) && 'acadp_listings' == $screen->post_type ) {
			if ( 'osm' == $map_settings['service'] ) {
				wp_enqueue_script( 
					ACADP_PLUGIN_NAME . '-map', 
					ACADP_PLUGIN_URL . 'vendor/leaflet/leaflet.js', 
					array(), 
					'1.7.1', 
					false 
				);
			} else {
				$map_api_key = ! empty( $map_settings['api_key'] ) ? '&key=' . $map_settings['api_key'] : '';

				wp_enqueue_script( 
					ACADP_PLUGIN_NAME . '-map', 
					'https://maps.googleapis.com/maps/api/js?v=3.exp' . $map_api_key 
				);
			}
		}
		
		wp_enqueue_script( 
			ACADP_PLUGIN_NAME, 
			ACADP_PLUGIN_URL . 'admin/js/admin.js', 
			array( 'jquery' ), 
			ACADP_VERSION_NUM, 
			false 
		);
		
		wp_localize_script( 
			ACADP_PLUGIN_NAME, 
			'acadp', 
			array(
				'plugin_url'         => ACADP_PLUGIN_URL,
				'ajax_nonce'         => wp_create_nonce( 'acadp_ajax_nonce' ),
				'edit'               => __( 'Edit', 'advanced-classifieds-and-directory-pro' ),
				'delete_permanently' => __( 'Delete Permanently', 'advanced-classifieds-and-directory-pro' ),
				'map_service'        => $map_settings['service'],
				'zoom_level'         => $map_settings['zoom_level'],
				'i18n'               => array(
					'no_issues_slected' => __( 'Please select at least one issue.', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
	}	

	/**
	 * Manage form submissions.
	 *
	 * @since 1.7.3
	 */
	public function admin_init() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['issues'] ) && isset( $_POST['acadp_fix_issues_nonce'] ) ) {
			// Verify that the nonce is valid
    		if ( wp_verify_nonce( $_POST['acadp_fix_issues_nonce'], 'acadp_fix_issues' ) ) {
				$redirect_url = admin_url( 'admin.php?page=advanced-classifieds-and-directory-pro&tab=issues' );

				// Fix Issues
				if ( __( 'Apply Fix', 'advanced-classifieds-and-directory-pro' ) == $_POST['action']) {
					$this->fix_issues();

					$redirect_url = add_query_arg( 
						array( 
							'section' => 'found',
							'success' => 1
						), 
						$redirect_url 
					);
				}

				// Ignore Issues
				if ( __( 'Ignore', 'advanced-classifieds-and-directory-pro' ) == $_POST['action']) {
					$this->ignore_issues();

					$redirect_url = add_query_arg( 
						array( 
							'section' => 'ignored',
							'success' => 1
						), 
						$redirect_url 
					);
				}

				// Redirect
				wp_redirect( $redirect_url );
        		exit;
			}
		}		
	}
	
	/**
	 * Add plugin menu.
	 *
	 * @since 1.7.3
	 */
	public function admin_menu() {	
		add_menu_page(
            __( 'Advanced Classifieds and Directory Pro', 'advanced-classifieds-and-directory-pro' ),
            __( 'Classifieds & Directory', 'advanced-classifieds-and-directory-pro' ),
            'edit_others_acadp_listings',
            'advanced-classifieds-and-directory-pro',
            array( $this, 'display_dashboard_content' ),
            'dashicons-welcome-widgets-menus',
            5
		);	
		
		add_submenu_page(
			'advanced-classifieds-and-directory-pro',
			__( 'Advanced Classifieds and Directory Pro - Dashboard', 'advanced-classifieds-and-directory-pro' ),
			__( 'Dashboard', 'advanced-classifieds-and-directory-pro' ),
			'edit_others_acadp_listings',
			'advanced-classifieds-and-directory-pro',
			array( $this, 'display_dashboard_content' )
		);
	}

	/**
	 * Display dashboard page content.
	 *
	 * @since 1.7.3
	 */
	public function display_dashboard_content() {
		$general_settings = get_option( 'acadp_general_settings' );

		// Tabs
		$tabs = array(
			'getting-started'   => __( 'Getting Started', 'advanced-classifieds-and-directory-pro' ),
			'shortcode-builder' => __( 'Shortcode Builder', 'advanced-classifieds-and-directory-pro' ),
			'faq'               => __( 'FAQ', 'advanced-classifieds-and-directory-pro' )
		);		

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'getting-started';

		// Issues
		$issues = $this->check_issues();

		if ( count( $issues['found'] ) || 'issues' == $active_tab  ) {
			$tabs['issues'] = __( 'Issues Detected', 'advanced-classifieds-and-directory-pro' );
		}	
		
		// Validate active tab
		if ( ! in_array( $active_tab, array_keys( $tabs ) ) ) {
			$active_tab = 'getting-started';
		}

		require_once ACADP_PLUGIN_DIR . 'admin/partials/dashboard/dashboard.php';	
	}

	/**
	 * Check for new issues and return it.
	 *
	 * @since  1.7.3
	 * @return array $issues Array of detected issues.
	 */
	public function check_issues() {
		$issues = array(
			'found'   => array(),
			'ignored' => array()
		);

		$_issues = get_option( 'acadp_issues', $issues );
		$ignored = $_issues['ignored'];		

		// Check: users_cannot_register
		if ( ! get_option( 'users_can_register' ) ) { // If user registration disabled
			if ( in_array( 'users_cannot_register', $ignored ) ) { // If issue ignored by the admin
				$issues['ignored'][] = 'users_cannot_register';
			} else {
				$issues['found'][] = 'users_cannot_register';
			}
		}

		// Check: pages_misconfigured
		$page_settings = get_option( 'acadp_page_settings' );
		$pages = acadp_get_custom_pages_list();

		foreach ( $pages as $key => $page ) {
			$post_id = $page_settings[ $key ];

			$issue_detected = 0;

			if ( $post_id > 0 ) {
				$post = get_post( $post_id );

				if ( empty( $post ) || 'publish' != $post->post_status ) {
					$issue_detected = 1;
				} elseif ( ! empty( $pages[ $key ]['content'] ) && false === strpos( $post->post_content, $pages[ $key ]['content'] ) ) {
					$issue_detected = 1;				
				}
			} else {
				$issue_detected = 1;
			}

			if ( $issue_detected ) {
				if ( in_array( 'pages_misconfigured', $ignored ) ) {
					$issues['ignored'][] = 'pages_misconfigured';
				} else {
					$issues['found'][] = 'pages_misconfigured';
				}

				break;
			}			
		}		

		// Update
		update_option( 'acadp_issues', $issues );

		// Return
		return $issues;
	}	

	/**
	 * Apply fixes.
	 *
	 * @since 1.7.3
	 */
	public function fix_issues() {		
		$fixed = array();

		// Apply the fixes
		$_issues = acadp_sanitize_array( $_POST['issues'] );

		foreach ( $_issues as $issue ) {
			switch ( $issue ) {
				case 'users_cannot_register':					
					update_option( 'users_can_register', 1 );

					$fixed[] = $issue;
					break;
				case 'pages_misconfigured':	
					global $wpdb;

					$page_settings = get_option( 'acadp_page_settings' );

					$pages = acadp_get_custom_pages_list();
					$issue_detected = 0;

					foreach ( $pages as $key => $page ) {
						$post_id = $page_settings[ $key ];			
			
						if ( $post_id > 0 ) {
							$post = get_post( $post_id );
			
							if ( empty( $post ) || 'publish' != $post->post_status ) {
								$issue_detected = 1;
							} elseif ( ! empty( $pages[ $key ]['content'] ) && false === strpos( $post->post_content, $pages[ $key ]['content'] ) ) {
								$issue_detected = 1;		
							}
						} else {
							$issue_detected = 1;
						}	
						
						if ( $issue_detected ) {
							$insert_id = 0;

							if ( ! empty( $pages[ $key ]['content'] ) ) {
								$query = $wpdb->prepare(
									"SELECT ID FROM {$wpdb->posts} WHERE `post_content` LIKE %s",
									sanitize_text_field( $pages[ $key ]['content'] )
								);

								$ids = $wpdb->get_col( $query );
							} else {
								$ids = array();
							}

							if ( ! empty( $ids ) ) {
								$insert_id = $ids[0];

								if ( 'publish' != get_post_status( $insert_id ) ) {
									wp_update_post(
										array(
											'ID'          => $insert_id,
											'post_status' => 'publish'
										)
									);
								}
							} else {
								$insert_id = wp_insert_post(
									array(
										'post_title'     => $pages[ $key ]['title'],
										'post_content'   => $pages[ $key ]['content'],
										'post_status'    => 'publish',
										'post_author'    => 1,
										'post_type'      => 'page',
										'comment_status' => 'closed'
									)
								);
							}

							$page_settings[ $key ] = $insert_id;
						}
					}

					update_option( 'acadp_page_settings', $page_settings );

					$fixed[] = $issue;
					break;
			}
		}

		// Update
		$issues = get_option( 'acadp_issues', array(
			'found'   => array(),
			'ignored' => array()
		));

		foreach ( $issues['found'] as $index => $issue ) {
			if ( in_array( $issue, $fixed ) ) {
				unset( $issues['found'][ $index ] );
			}
		}

		foreach ( $issues['ignored'] as $index => $issue ) {
			if ( in_array( $issue, $fixed ) ) {
				unset( $issues['ignored'][ $index ] );
			}
		}

		update_option( 'acadp_issues', $issues );
	}

	/**
	 * Ignore issues.
	 *
	 * @since 1.7.3
	 */
	public function ignore_issues() {
		$ignored = array();

		// Ignore the issues
		$_issues = acadp_sanitize_array( $_POST['issues'] );		

		foreach ( $_issues as $issue ) {
			switch ( $issue ) {
				case 'users_cannot_register':					
				case 'pages_misconfigured':					
					$ignored[] = $issue;
					break;
			}
		}

		// Update
		$issues = get_option( 'acadp_issues', array(
			'found'   => array(),
			'ignored' => array()
		));

		foreach ( $issues['found'] as $index => $issue ) {
			if ( in_array( $issue, $ignored ) ) {
				unset( $issues['found'][ $index ] );
			}
		}

		$issues['ignored'] = array_merge( $issues['ignored'], $ignored );

		update_option( 'acadp_issues', $issues );
	}	

	/**
	 * Get details of the given issue.
	 *
	 * @since  1.7.3
	 * @param  string $issue Issue code.
	 * @return array         Issue details.
	 */
	public function get_issue_details( $issue ) {
		$issues = array(
			'users_cannot_register' => array(
				'title'       => __( 'User Account Registration Disabled', 'advanced-classifieds-and-directory-pro' ),
				'description' => __( 'User account registration is disabled on your website. You must enable this option to allow new users to register on your website and submit their listings through your site front-end.', 'advanced-classifieds-and-directory-pro' )
			),
			'pages_misconfigured' => array(
				'title'       => __( 'Pages Misconfigured', 'advanced-classifieds-and-directory-pro' ),
				'description' => sprintf(
					__( 'During activation, our plugin adds few <a href="%s" target="_blank">pages</a> dynamically on your website that are required for the internal logic of the plugin. We found some of those pages are missing, misconfigured or having a wrong shortcode.', 'advanced-classifieds-and-directory-pro' ),
					esc_url( admin_url( 'admin.php?page=acadp_settings&tab=misc&section=acadp_pages_settings' ) )
				)
			)
		);
	
		return isset( $issues[ $issue ] ) ? $issues[ $issue ] : '';
	}
	
	/**
	 * Add a post display state for special ACADP pages in the page list table.
	 *
	 * @since 2.0.0
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states( $post_states, $post ) {
		$page_settings = get_option( 'acadp_page_settings', array() );
		
		if ( isset( $page_settings['listings'] ) && $page_settings['listings'] === $post->ID ) {
			$post_states['acadp_page_for_listings'] = __( 'All listings page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['locations'] ) && $page_settings['locations'] === $post->ID ) {
			$post_states['acadp_page_for_locations'] = __( 'All locations page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['location'] ) && $page_settings['location'] === $post->ID ) {
			$post_states['acadp_page_for_location'] = __( 'Single location page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['categories'] ) && $page_settings['categories'] === $post->ID ) {
			$post_states['acadp_page_for_categories'] = __( 'All categories page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['category'] ) && $page_settings['category'] === $post->ID ) {
			$post_states['acadp_page_for_category'] = __( 'Single category page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['search'] ) && $page_settings['search'] === $post->ID ) {
			$post_states['acadp_page_for_search'] = __( 'Search results page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['user_listings'] ) && $page_settings['user_listings'] === $post->ID ) {
			$post_states['acadp_page_for_user_listings'] = __( 'User listings page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['user_dashboard'] ) && $page_settings['user_dashboard'] === $post->ID ) {
			$post_states['acadp_page_for_user_dashboard'] = __( 'User dashboard page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['listing_form'] ) && $page_settings['listing_form'] === $post->ID ) {
			$post_states['acadp_page_for_listing_form'] = __( 'Listing form page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['manage_listings'] ) && $page_settings['manage_listings'] === $post->ID ) {
			$post_states['acadp_page_for_manage_listings'] = __( 'Manage listings page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['favourite_listings'] ) && $page_settings['favourite_listings'] === $post->ID ) {
			$post_states['acadp_page_for_favourite_listings'] = __( 'Favourite listings page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['checkout'] ) && $page_settings['checkout'] === $post->ID ) {
			$post_states['acadp_page_for_checkout'] = __( 'Checkout page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['payment_receipt'] ) && $page_settings['payment_receipt'] === $post->ID ) {
			$post_states['acadp_page_for_payment_receipt'] = __( 'Payment receipt page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['payment_failure'] ) && $page_settings['payment_failure'] === $post->ID ) {
			$post_states['acadp_page_for_payment_failure'] = __( 'Payment failure page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['payment_history'] ) && $page_settings['payment_history'] === $post->ID ) {
			$post_states['acadp_page_for_payment_history'] = __( 'Payment history page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['login_form'] ) && $page_settings['login_form'] === $post->ID ) {
			$post_states['acadp_page_for_login_form'] = __( 'Login form page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['register_form'] ) && $page_settings['register_form'] === $post->ID ) {
			$post_states['acadp_page_for_register_form'] = __( 'Register form page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['user_account'] ) && $page_settings['user_account'] === $post->ID ) {
			$post_states['acadp_page_for_user_account'] = __( 'Account page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['forgot_password'] ) && $page_settings['forgot_password'] === $post->ID ) {
			$post_states['acadp_page_for_forgot_password'] = __( 'Forgot password page', 'advanced-classifieds-and-directory-pro' );
		}

		if ( isset( $page_settings['password_reset'] ) && $page_settings['password_reset'] === $post->ID ) {
			$post_states['acadp_page_for_password_reset'] = __( 'Password reset page', 'advanced-classifieds-and-directory-pro' );
		}

		return $post_states;
	}

	/**
	 * Add a settings link on the plugin listing page.
	 *
	 * @since  2.0.0
	 * @param  array  $links An array of plugin action links.
	 * @return string $links Array of filtered plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( 
			'<a href="%s">%s</a>', 
			esc_url( admin_url( 'admin.php?page=acadp_settings' ) ), 
			__( 'Settings', 'advanced-classifieds-and-directory-pro' ) 
		);

        array_unshift( $links, $settings_link );
		
    	return $links;
	}

	/**
	 * Disable Gutenberg on our custom post types.
	 *
	 * @since  1.8.0
	 * @param  bool   $use_block_editor Default status.
	 * @param  string $post_type        The post type being checked.
	 * @return bool   $use_block_editor Filtered editor status.
	 */
	public function disable_gutenberg( $use_block_editor, $post_type ) {
		if ( 'acadp_listings' == $post_type || 'acadp_fields' == $post_type || 'acadp_payments' == $post_type ) return false;
		return $use_block_editor;
	}
	
	/**
	 * Delete an attachment.
	 *
	 * @since 1.5.4
	 */
	public function ajax_callback_delete_attachment() {	
		check_ajax_referer( 'acadp_ajax_nonce', 'security' );

		$misc_settings = get_option( 'acadp_misc_settings' );

		if ( ! empty( $misc_settings['delete_media_files'] ) ) {
			if ( isset( $_POST['attachment_id'] ) ) {
				wp_delete_attachment( (int) $_POST['attachment_id'], true );
			}
		}
		
		wp_die();	
	}

}
