<?php

namespace Rtcl\ThemeSupports;

use Rtcl\Resources\ThemeSupportCss;

class ThemeSupports {
	/**
	 * Current Theme name
	 *
	 * @var string
	 */
	private static $current_theme = '';

	static function init() {
		self::$current_theme = get_template();
		do_action( 'rtcl_add_theme_support', self::$current_theme );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'css_theme_support' ], 99 );
	}

	static function css_theme_support() {
		if ( 'twentytwenty' === self::$current_theme ) {
			echo '<style id="rtcl-twentytwenty" media="screen">';
			echo ThemeSupportCss::twentyTwenty();
			echo '</style>';
//			wp_add_inline_style('twentytwenty-style', ThemeSupportCss::twentyTwenty());
		}

		if ( 'divi' === strtolower( self::$current_theme ) ) {
			wp_add_inline_style('rtcl-public', ThemeSupportCss::divi());
		}
	}
}