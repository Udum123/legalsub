<?php

namespace Rtcl\Controllers\Settings;

use Exception;
use Rtcl\Helpers\Functions;

class MiscSettingsController
{

	/**
	 * @var AdminSettings
	 */
	private $settingsApi;

	public function __construct($settingsApi) {
		$this->settingsApi = $settingsApi;
		add_filter('rtcl_settings_api_sanitized_fields_misc_settings', [&$this, 'minMaxLicenseKeySanitization'], 10, 2);
	}

	/**
	 * @throws Exception
	 */
	public function minMaxLicenseKeySanitization($settings) {
		if (!empty($settings['maxmind_license_key']) && !$this->validate_license_key_field($settings['maxmind_license_key'])) {
			unset($settings['maxmind_license_key']);
		}
		return $settings;
	}

	/**
	 * Checks to make sure that the license key is valid.
	 *
	 * @param $license_key
	 *
	 * @return mixed
	 * @throws Exception When the license key is invalid.
	 */
	public function validate_license_key_field($license_key) {
		$license_key = is_null($license_key) ? '' : $license_key;
		$license_key = trim(stripslashes($license_key));

		// Empty license keys have no need test downloading a database.
		if (empty($license_key)) {
			return false;
		}

		// Check the license key by attempting to download the Geolocation database.
		$tmp_database_path = $this->settingsApi->maxMindDatabaseService->download_database($license_key);
		if (is_wp_error($tmp_database_path)) {
			Functions::add_notice($tmp_database_path->get_error_message(), 'error');
			return false;
		}

		// We may as well put this archive to good use, now that we've downloaded one.
		$this->update_database($tmp_database_path);

		return $license_key;
	}

	/**
	 * Updates the database used for geolocation queries.
	 *
	 * @param string|null $new_database_path The path to the new database file. Null will fetch a new archive.
	 */
	public function update_database($new_database_path = null) {
		// Allow us to easily interact with the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		// Remove any existing archives to comply with the MaxMind TOS.
		$target_database_path = $this->settingsApi->maxMindDatabaseService->get_database_path();

		// If there's no database path, we can't store the database.
		if (empty($target_database_path)) {
			return;
		}
		// Create uploads folder if not exist.
		$path_dir = dirname($target_database_path);
		if (!$wp_filesystem->exists($path_dir)) {
			$wp_filesystem->mkdir($path_dir);
		} else {
			if ($wp_filesystem->exists($target_database_path)) {
				$wp_filesystem->delete($target_database_path);
			}
		}

		if (isset($new_database_path)) {
			$tmp_database_path = $new_database_path;
		} else {
			// We can't download a database if there's no license key configured.
			$license_key = $this->settingsApi->get_option('maxmind_license_key');
			if (empty($license_key)) {
				return;
			}

			$tmp_database_path = $this->settingsApi->maxMindDatabaseService->download_database($license_key);
			if (is_wp_error($tmp_database_path)) {
				rtcl()->logger()->notice($tmp_database_path->get_error_message(), array('source' => 'maxmind-geolocation'));
				return;
			}
		}

		// Move the new database into position.
		$wp_filesystem->move($tmp_database_path, $target_database_path, true);
		$wp_filesystem->delete(dirname($tmp_database_path));
	}
}