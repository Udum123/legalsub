<?php

namespace Rtcl\Widgets;


class Widget {

	public static function init() {
		add_action( 'widgets_init', [ __CLASS__, 'register_widget' ] );
		add_action( 'init', [ __CLASS__, 'widget_support' ] );

	}

	static function widget_support() {
		add_filter( 'elementor/widgets/wordpress/widget_args', [
			__CLASS__,
			'elementor_wordpress_widget_support'
		], 10, 2 );
	}

	public static function register_widget() {

		if ( ! is_registered_sidebar( 'rtcl-archive-sidebar' ) ) {
			register_sidebar( [
				'name'          => apply_filters( 'rtcl_archive_sidebar_title', esc_html__( 'Classified Listing - Archive Sidebar', 'classified-listing' ) ),
				'id'            => 'rtcl-archive-sidebar',
				'description'   => esc_html__( 'Add widgets on listing archive page', 'classified-listing' ),
				'before_widget' => '<div class="widget rtcl-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="rtcl-widget-heading"><h3>',
				'after_title'   => '</h3></div>',
			] );
		}

		if ( ! is_registered_sidebar( 'rtcl-single-sidebar' ) ) {
			register_sidebar( [
				'name'          => apply_filters( 'rtcl_single_sidebar_title', esc_html__( 'Classified Listing - Single Sidebar', 'classified-listing' ) ),
				'id'            => 'rtcl-single-sidebar',
				'description'   => esc_html__( 'Add widgets on listing single page', 'classified-listing' ),
				'before_widget' => '<div class="widget rtcl-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="rtcl-widget-heading"><h3>',
				'after_title'   => '</h3></div>',
			] );
		}

		register_widget( Categories::class );
		register_widget( Filter::class );
		register_widget( Search::class );
		register_widget( Listings::class );

	}

	public static function elementor_wordpress_widget_support( $default_widget_args, $object ) {
		if ( false !== strpos( $object->get_widget_instance()->id_base, 'rtcl-widget-' ) ) {
			$default_widget_args['before_widget'] = sprintf( '<div id="%1$s" class="widget %2$s">', $object->get_widget_instance()->id_base, $object->get_widget_instance()->widget_options['classname'] );
			$default_widget_args['after_widget']  = '</div>';
		}

		return $default_widget_args;
	}

}