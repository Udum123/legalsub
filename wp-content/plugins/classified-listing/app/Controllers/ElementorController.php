<?php
/**
 * Main Elementor ElementorMainController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since    1.0.0
 */

namespace Rtcl\Controllers;

use Elementor\Plugin;
use Rtcl\Controllers\Elementor\Controls\ImageSelectorControl;
use Rtcl\Controllers\Elementor\Hooks\ELFilterHooks;
use Rtcl\Controllers\Elementor\Widgets;
use Rtcl\Controllers\Hooks\TemplateHooks;
use Rtcl\Controllers\Hooks\TemplateLoader;

/**
 * Main Elementor ElementorMainController Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
class ElementorController {
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public static function init() {
		add_action('elementor/init', [__CLASS__, 'elementor_init']);
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since  1.0.0
	 */
	public static function elementor_init() {
		
		// Add Plugin actions.
		if (version_compare( ELEMENTOR_VERSION , '3.5.0', '<')) {
			$widgets_registered = 'elementor/widgets/widgets_registered';
		}else{
			$widgets_registered = 'elementor/widgets/register';
		}
		
		add_action( $widgets_registered, [__CLASS__, 'init_widgets']);
		add_action('elementor/controls/controls_registered', [__CLASS__, 'init_controls']);
		add_action('elementor/elements/categories_registered', [__CLASS__, 'add_elementor_widget_categories']);
		// This code only for elementor editor.
		add_action(
			$widgets_registered,
			function () {
				if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
					add_action('rtcl_single_listing_business_hours', [BusinessHoursController::class, 'display_business_hours']);
					TemplateHooks::init();
					add_action('init', [TemplateLoader::class, 'init']);
				}
			}
		);

		// Filter hooks.
		ELFilterHooks::init();
	}

	/**
	 * Register Category
	 *
	 * @param [type] $elements_manager Category hooks
	 *
	 * @return void
	 */
	public static function add_elementor_widget_categories($elements_manager) {
		$categories['rtcl-elementor-widgets'] = [
			'title' => __('Classified Listing', 'classified-listing'),
			'icon'  => 'fa fa-plug',
		];
		$categories                           = apply_filters('rtcl_elementor_widgets_category_lists', $categories);
		$other_categories                     = $elements_manager->get_categories();
		$categories                           = array_merge(
			array_slice($other_categories, 0, 1),
			$categories,
			array_slice($other_categories, 1)
		);
		$set_categories                       = function ($categories) {
			$this->categories = $categories;
		};

		$set_categories->call($elements_manager, $categories);
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since  1.0.0
	 */
	public static function init_widgets() {
		$class_list = [
			Widgets\ListingCategoryBox::class,
			Widgets\ListingItems::class,
			Widgets\SingleLocation::class,
			Widgets\AllLocations::class,
			Widgets\HeaderButton::class,
			Widgets\ListingSearch::class
		];
		$class_list = array_filter(apply_filters('rtcl_el_widget_for_classified_listing', $class_list));
		// Register widget.
		if (!empty($class_list)) {
			foreach ($class_list as $widget) {
				if (!is_object($widget)) { // Makes compatible with the first version.
					$widget = new $widget();
				}
				Plugin::instance()->widgets_manager->register($widget);
			}
		}
	}

	/**
	 * Init Controls
	 *
	 * Include controls files and register them
	 *
	 * @since  1.0.0
	 */
	public static function init_controls() {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		$controls_manager->register(new ImageSelectorControl());
	}
}
