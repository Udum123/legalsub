<?php

namespace Rtcl\Helpers;

use Rtcl\Helpers\BlockFileSystem;

/**
 * Class BlockFns
 *
 * @package Rtcl\Helpers
 */
class BlockFns
{
	/**
	 * Store Json variable
	 * @var instance
	 */
	public static $icon_json;

	/**
	 * Get Json Data.
	 * 
	 * @return Array
	 */
	public static function block_load_font_awesome_icons()
	{
		$json_file = RTCL_URL . "/assets/block-icon/RTCLGBIcon.json";

		if (empty($json_file)) {
			return array();
		}

		// Function has already run.
		if (null !== self::$icon_json) {
			return self::$icon_json;
		}
		$str   = BlockFileSystem::get_instance()->get_filesystem()->get_contents($json_file);
		self::$icon_json = json_decode($str, true);
		return self::$icon_json;
	}

	/**
	 * Generate SVG.
	 * 
	 * @param  array $icon Decoded fontawesome json file data.
	 */
	public static function render_svg_html($icon)
	{
		$icon = str_replace('far', '', $icon);
		$icon = str_replace('fas', '', $icon);
		$icon = str_replace('fab', '', $icon);
		$icon = str_replace('fa-', '', $icon);
		$icon = str_replace('fa', '', $icon);
		$icon = sanitize_text_field(esc_attr($icon));

		$json = self::block_load_font_awesome_icons();

		$path = isset($json[$icon]['svg']['brands']) ? $json[$icon]['svg']['brands']['path'] : $json[$icon]['svg']['solid']['path'];
		$view = isset($json[$icon]['svg']['brands']) ? $json[$icon]['svg']['brands']['viewBox'] : $json[$icon]['svg']['solid']['viewBox'];

		if ($view) {
			$view = implode(' ', $view);
		}
?>
		<svg xmlns="https://www.w3.org/2000/svg" viewBox="<?php echo esc_html($view); ?>">
			<path d="<?php echo esc_html($path); ?>"></path>
		</svg>
<?php
	}

	/**
	 * wp_kses allowed html.
	 * 
	 * @return Array
	 */
	public static function kses_allowed_svg()
	{
		$defaults = wp_kses_allowed_html('post');
		$svg_args = array(
			'svg'   => array(
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true,
			),
			'g'     => array('fill' => true),
			'title' => array('title' => true),
			'path'  => array('d' => true, 'fill' => true,),
		);
		$allowed_html = array_merge($defaults, $svg_args);
		return $allowed_html;
	}
}
