<?php

namespace Rtcl\Resources;

class ThemeSupportCss
{

	/**
	 * @return string css
	 */
	public static function twentyTwenty() {
		ob_start();
		?>
		.entry-content > .rtcl{max-width: 1200px !important;}
		<?php
		return ob_get_clean();
	}

	/**
	 * @return string css
	 */
	public static function divi() {
		$content_width = absint( et_get_option( 'content_width', '1080' ) );
		ob_start();
		?>
        #et-main-area .rtcl-breadcrumb {
            width: 80%;
            max-width: <?php echo esc_attr($content_width); ?>px !important;
            margin: auto;
            padding-top: 20px;
        }
        #et-main-area .rtcl-wrapper {
            width: 80%;
            max-width: <?php echo esc_attr($content_width); ?>px !important;
            margin: auto;
            min-width: auto;
            padding-top: 60px;
            padding-bottom: 60px;
        }
		<?php
		return ob_get_clean();
	}

}