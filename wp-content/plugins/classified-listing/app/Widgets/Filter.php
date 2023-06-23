<?php

namespace Rtcl\Widgets;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;
use Rtcl\Models\WidgetFields;
use Rtcl\Resources\Options;
use WP_Term;
use WP_Widget;

/**
 * Class Filter
 *
 * @package Rtcl\Widgets
 */
class Filter extends WP_Widget {

	protected $widget_slug;
	protected $instance;

	public function __construct() {

		$this->widget_slug = 'rtcl-widget-filter';

		parent::__construct(
			$this->widget_slug,
			esc_html__( 'Classified Listing Filter', 'classified-listing' ),
			[
				'classname'   => 'rtcl ' . $this->widget_slug . '-class',
				'description' => esc_html__( 'Classified listing Filter.', 'classified-listing' )
			]
		);
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		if ( Functions::is_enable_map() && ( is_active_widget( false, false, $this->id_base, true ) || Functions::is_active_elementor_widget( $this->id_base ) ) ) {
			wp_enqueue_script( 'rtcl-map' );
		}
		do_action( 'rtcl_widget_search_enqueue_scripts', $this );
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		if ( empty( $instance ) ) {
			$instance = $this->getDefaultValues();
		}
		$this->instance = $instance;
		global $wp;
		$queried_object = get_queried_object();
		foreach ( [ rtcl()->location, rtcl()->category ] as $taxonomy ) {
			if ( is_a( $queried_object, WP_Term::class ) && $queried_object->taxonomy === $taxonomy ) {
				$queried_object = clone $queried_object;
				unset( $queried_object->description );
				$this->instance['current_taxonomy'][ $taxonomy ] = clone $queried_object;
			} else {
				$q_term = $term = '';
				if ( isset( $wp->query_vars[ $taxonomy ] ) ) {
					$q_term = explode( '/', $wp->query_vars[ $taxonomy ] );
					$q_term = end( $q_term );
				}
				if ( $q_term && $term = get_term_by( 'slug', $q_term, $taxonomy ) ) {
					$term = clone $term;
					unset( $term->description );
				}
				$this->instance['current_taxonomy'][ $taxonomy ] = $term;
			}
		}
		$data = [
			'category_filter'       => $this->get_category_filter(),
			'location_filter'       => $this->get_location_filter(),
			'ad_type_filter'        => $this->get_ad_type_filter(),
			'price_filter'          => $this->get_price_filter(),
			'radius_search'         => $this->get_radius_search(),
			'object'                => $this,
			'template'              => "widgets/filter",
			'default_template_path' => null,
		];

		$data         = apply_filters( 'rtcl_widget_filter_values', $data, $args, $instance, $this );
		$data['data'] = $data;
		?>
		<div class="rtcl-widget-filter-wrapper <?php echo isset($instance['filter_style']) ? $instance['filter_style'] : ''; ?>">
		<?php
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		echo $args['after_widget'];
		?>
		</div>
		<?php
	}

	public function get_instance() {
		return $this->instance;
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']               = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['search_by_category']  = ! empty( $new_instance['search_by_category'] ) ? 1 : 0;
		$instance['search_by_location']  = ! empty( $new_instance['search_by_location'] ) ? 1 : 0;
		$instance['search_by_ad_type']   = ! empty( $new_instance['search_by_ad_type'] ) ? 1 : 0;
		$instance['search_by_price']     = ! empty( $new_instance['search_by_price'] ) ? 1 : 0;
		$instance['radius_search']       = ! empty( $new_instance['radius_search'] ) ? 1 : 0;
		$instance['hide_empty']          = ! empty( $new_instance['hide_empty'] ) ? 1 : 0;
		$instance['show_count']          = ! empty( $new_instance['show_count'] ) ? 1 : 0;
		$instance['ajax_load']           = ! empty( $new_instance['ajax_load'] ) ? 1 : 0;
		$instance['taxonomy_reset_link'] = ! empty( $new_instance['taxonomy_reset_link'] ) ? 1 : 0;

		return apply_filters( 'rtcl_widget_filter_update_values', $instance, $new_instance, $old_instance, $this );
	}

	/**
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {
		// Parse incoming $instance into an array and merge it with $defaults
		$instance     = $this->getDefaultValues($instance);
		$fields       = Options::widget_filter_fields();
		$widgetFields = new WidgetFields( $fields, $instance, $this );
		$widgetFields->render();
	}
	
	public function getDefaultValues($instance = []){
		// Define the array of defaults
		$defaults = array(
			'title'               => esc_html__( 'Filter', 'classified-listing' ),
			'search_by_category'  => 1,
			'search_by_location'  => 1,
			'radius_search'       => 0,
			'search_by_ad_type'   => 1,
			'search_by_price'     => 1,
			'hide_empty'          => 0,
			'show_count'          => 1,
			'ajax_load'           => 1,
			'taxonomy_reset_link' => 1,
		);

		// Parse incoming $instance into an array and merge it with $defaults
		return wp_parse_args(
			(array) $instance,
			apply_filters( 'rtcl_widget_filter_default_values', $defaults, $instance, $this )
		);
	}

	public function get_category_filter() {
		if ( ! empty( $this->instance['search_by_category'] ) ) {
			$args = [
				'taxonomy' => rtcl()->category,
				'parent'   => 0,
				'instance' => $this->instance
			];

			return sprintf( '<div class="rtcl-category-filter ui-accordion-item is-open">
					                <a class="ui-accordion-title">
					                    <span>%s</span>
					                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
					                </a>
					                <div class="ui-accordion-content%s"%s>%s</div>
					            </div>',
				apply_filters( 'rtcl_widget_filter_category_title', esc_html__( "Category", "classified-listing" ) ),
				! empty( $args['instance']['ajax_load'] ) ? ' rtcl-ajax-load' : '',
				! empty( $args['instance']['ajax_load'] ) ? sprintf( ' data-settings="%s"', htmlspecialchars( wp_json_encode( $args ) ) ) : '',
				empty( $args['instance']['ajax_load'] ) ? Functions::get_sub_terms_filter_html( $args ) : ""
			);
		}
	}

	/**
	 * @return null|string
	 */
	public function get_location_filter() {

		if ( 'geo' === Functions::location_type() ) {
			return '';
		}

		if ( ! empty( $this->instance['search_by_location'] ) ) {
			$args = [
				'taxonomy' => rtcl()->location,
				'parent'   => 0,
				'instance' => $this->instance
			];

			return sprintf( '<div class="rtcl-location-filter ui-accordion-item is-open">
					                <a class="ui-accordion-title">
					                    <span>%s</span>
					                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
					                </a>
					                <div class="ui-accordion-content%s"%s>%s</div>
					            </div>',
				apply_filters( 'rtcl_widget_filter_location_title', esc_html__( "Location", "classified-listing" ) ),
				! empty( $args['instance']['ajax_load'] ) ? ' rtcl-ajax-load' : '',
				! empty( $args['instance']['ajax_load'] ) ? sprintf( ' data-settings="%s"', htmlspecialchars( wp_json_encode( $args ) ) ) : '',
				empty( $args['instance']['ajax_load'] ) ? Functions::get_sub_terms_filter_html( $args ) : ""
			);
		}
	}

	/**
	 * @return string
	 */
	public function get_ad_type_filter() {
		if ( ! empty( $this->instance['search_by_ad_type'] ) && ! Functions::is_ad_type_disabled() ) {
			$filters    = ! empty( $_GET['filters'] ) ? $_GET['filters'] : array();
			$ad_type    = ! empty( $filters['ad_type'] ) ? esc_attr( $filters['ad_type'] ) : null;
			$field_html = "<ul class='ui-link-tree is-collapsed'>";
			$ad_types   = Functions::get_listing_types();
			if ( ! empty( $ad_types ) ) {
				foreach ( $ad_types as $key => $option ) {
					$checked    = ( $ad_type == $key ) ? " checked " : '';
					$field_html .= "<li class='ui-link-tree-item ad-type-{$key}'>";
					$field_html .= "<input id='filters-ad-type-values-{$key}' name='filters[ad_type]' {$checked} value='{$key}' type='radio' class='ui-checkbox filter-submit-trigger'>";
					$field_html .= "<a href='#' class='filter-submit-trigger'>" . esc_html( $option ) . "</a>";
					$field_html .= "</li>";
				}
			}
			$field_html .= '<li class="is-opener"><span class="rtcl-more"><i class="rtcl-icon rtcl-icon-plus-circled"></i><span class="text">' . __( "Show More",
					"classified-listing" ) . '</span></span></li>';
			$field_html .= "</ul>";

			return sprintf( '<div class="rtcl-ad-type-filter ui-accordion-item is-open">
									                <a class="ui-accordion-title">
									                    <span>%s</span>
									                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
									                </a>
									                <div class="ui-accordion-content">%s</div>
									            </div>',
				apply_filters( 'rtcl_widget_filter_ad_type_title', esc_html__( "Type", "classified-listing" ) ),
				$field_html
			);
		}
	}

	/**
	 * @return string
	 */
	public function get_price_filter() {
		if ( ! empty( $this->instance['search_by_price'] ) ) {
			$filters    = ! empty( $_GET['filters'] ) ? $_GET['filters'] : array();
			$fMinValue  = ! empty( $filters['price']['min'] ) ? esc_attr( $filters['price']['min'] ) : null;
			$fMaxValue  = ! empty( $filters['price']['max'] ) ? esc_attr( $filters['price']['max'] ) : null;
			$field_html = sprintf( '<div class="form-group">
										<div class="price-container">
								            <div class="row">
								                <div class="col-md-6 col-6">
								                    <input type="number" name="filters[price][min]" class="form-control" placeholder="%s" value="%s">
								                </div>
								                <div class="col-md-6 col-6">
								                    <input type="number" name="filters[price][max]" class="form-control" placeholder="%s" value="%s">
								                </div>
								            </div>
								        </div>
							        </div>',
				esc_html__( 'min', 'classified-listing' ),
				$fMinValue,
				esc_html__( 'max', 'classified-listing' ),
				$fMaxValue
			);

			return sprintf( '<div class="rtcl-price-filter ui-accordion-item is-open">
									                <a class="ui-accordion-title">
									                    <span>%s</span>
									                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
									                </a>
									                <div class="ui-accordion-content">%s</div>
									            </div>',
				apply_filters( 'rtcl_widget_filter_price_title', esc_html__( "Price Range", "classified-listing" ) ),
				$field_html
			);
		}
	}

	public function get_radius_search() {
		if ( ! empty( $this->instance['radius_search'] ) ) {
			$rs_data    = Options::radius_search_options();
			$field_html = sprintf( '
                                    <div class="form-group">
                                        <div class="rtcl-geo-address-field">
                                            <input type="text" name="geo_address" autocomplete="off" value="%1$s" placeholder="%7$s" class="form-control rtcl-geo-address-input" />
                                            <i class="rtcl-get-location rtcl-icon rtcl-icon-target"></i>
                                            <input type="hidden" class="latitude" name="center_lat" value="%2$s">
                                            <input type="hidden" class="longitude" name="center_lng" value="%3$s">
                                        </div>
							        </div>
                                    <div class="form-group">
							            <div class="rtcl-range-slider-field">
							                <div class="rtcl-range-label">%8$s (<span class="rtcl-range-value">%6$d</span> %4$s)</div>
                                            <input type="range" class="form-control-range rtcl-range-slider-input" name="distance" min="0" max="%5$d" value="%6$d">
							            </div>
							        </div>',
				! empty( $_GET['geo_address'] ) ? esc_attr( $_GET['geo_address'] ) : '',
				! empty( $_GET['center_lat'] ) ? esc_attr( $_GET['center_lat'] ) : '',
				! empty( $_GET['center_lng'] ) ? esc_attr( $_GET['center_lng'] ) : '',
				in_array( $rs_data['units'], [
					'km',
					'kilometers'
				] ) ? esc_html__( "km", "classified-listing" ) : esc_html__( "Miles", "classified-listing" ),
				$rs_data['max_distance'],
				absint( ! empty( $_GET['distance'] ) ? absint( $_GET['distance'] ) : $rs_data['default_distance'] ),
				esc_html__( "Select a location", "classified-listing" ),
				esc_html__( "Radius", "classified-listing" )
			);

			return sprintf( '<div class="rtcl-radius-filter ui-accordion-item is-open">
									                <a class="ui-accordion-title">
									                    <span>%s</span>
									                    <span class="ui-accordion-icon rtcl-icon rtcl-icon-anchor"></span>
									                </a>
									                <div class="ui-accordion-content">%s</div>
									            </div>',
				apply_filters( 'rtcl_widget_filter_radius_search_title', esc_html__( "Radius Search", "classified-listing" ) ),
				$field_html
			);
		}
	}

}
