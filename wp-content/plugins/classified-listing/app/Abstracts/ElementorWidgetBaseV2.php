<?php
/**
 * Main ElementorWidgetBase class.
 *
 * @package RadiusTheme\SB
 */

namespace Rtcl\Abstracts;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Abstract ElementorWidgetBase Class
 *
 * Implemented by classes using using for elementor addons development.
 *
 * @version  1.0.0
 * @package  RadiusTheme\SB
 */
abstract class ElementorWidgetBaseV2 extends ElementorWidgetBase {
	
	/**
	 * Elementor Promotional section controls.
	 *
	 * @return array
	 */
	/**
	 * Pro Notice controls.
	 *
	 * @return array
	 */
	public function pro_notice_fields() {
		if ( rtcl()->has_pro() ) {
			return [];
		}
		$fields = [
			'rtcl_pro_sec' => [
				'mode'  => 'section_start',
				'label' => '<span style="color: #f54">' . esc_html__( 'Go Premium for More Features', 'classified-listing' ) . '</span>',
			],
			'rtcl_get_pro_version' => [
				'type' => 'html',
				'raw'  => '<div class="elementor-nerd-box">
						<div class="elementor-nerd-box-title" style="margin-top: 0; margin-bottom: 20px;">Unlock more possibilities</div>
						<div class="elementor-nerd-box-message"><span class="pro-feature" style="font-size: 13px;"> Get the  <a href="https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/" target="_blank" style="color: #f54">Pro version</a> for more stunning layouts and customization options.</span></div>
						<a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-button-go-pro"
							href="https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/"
							target="_blank">
							Get Pro </a>
						</div>',
			],
			'rtcl_pro_sec_end' => [
				'mode' => 'section_end',
			],

		];
		return $fields;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		
		$fields = $this->widget_fields();

		$fields = apply_filters( 'rtcl/elementor/widgets/controls/' . $this->rtcl_base , $fields, $this );

        foreach ( $fields as $id => $field ) {
			// $field['classes']   = ! empty( $field['classes'] ) ? $field['classes'] . ' elementor-control-rtcl_el' : ' elementor-control-rtcl_el';
			// $field['separator'] = ! empty( $field['separator'] ) ? $field['separator'] : 'before-short';

			if ( ! empty( $field['type'] ) ) {
				$field['type'] = self::el_fields( $field['type'] );
			}

			if ( ! empty( $field['tab'] ) ) {
				$field['tab'] = self::el_tabs( $field['tab'] );
			}

			if ( isset( $field['mode'] ) && 'section_start' === $field['mode'] ) {
				unset( $field['mode'] );
				unset( $field['separator'] );

				$this->start_controls_section( $id, $field );
			} elseif ( isset( $field['mode'] ) && 'section_end' === $field['mode'] ) {
				$this->end_controls_section();
			} elseif ( isset( $field['mode'] ) && 'tabs_start' === $field['mode'] ) {
				unset( $field['mode'] );
				unset( $field['separator'] );

				$this->start_controls_tabs( $id );
			} elseif ( isset( $field['mode'] ) && 'tabs_end' === $field['mode'] ) {
				$this->end_controls_tabs();
			} elseif ( isset( $field['mode'] ) && 'tab_start' === $field['mode'] ) {
				unset( $field['mode'] );
				unset( $field['separator'] );

				$this->start_controls_tab( $id, $field );
			} elseif ( isset( $field['mode'] ) && 'tab_end' === $field['mode'] ) {
				$this->end_controls_tab();
			} elseif ( isset( $field['mode'] ) && 'group' === $field['mode'] ) {
				$type          = $field['type'];
				$field['name'] = $id;
				unset( $field['mode'] );
				unset( $field['type'] );
				$this->add_group_control( $type, $field );
			} elseif ( isset( $field['mode'] ) && 'responsive' === $field['mode'] ) {
				unset( $field['mode'] );

				$this->add_responsive_control( $id, $field );
			} elseif ( isset( $field['mode'] ) && 'repeater' === $field['mode'] ) {
				$repeater        = new Repeater();
				$repeater_fields = $field['fields'];

				foreach ( $repeater_fields as $rf_id => $value ) {
					$repeater->add_control( $rf_id, $value );
				}

				$field['fields'] = $repeater->get_controls();

				$this->add_control( $id, $field );
			} else {
				$this->add_control( $id, $field );
			}
		}
	}

	/**
	 * Insert Some array element
	 *
	 * @param [type]  $key The elements will insert nearby this key.
	 * @param [type]  $main_array Original array.
	 * @param [type]  $insert_array some element will insert in original array.
	 * @param boolean $is_after array insert position base on the key.
	 *
	 * @return array
	 */
	public function insert_controls( $key, $main_array, $insert_array, $is_after = false ) {
		$index = array_search( $key, array_keys( $main_array ), true );
		if ( 'integer' === gettype( $index ) ) {
			if ( $is_after ) {
				$index ++;
			}
			$main_array = array_merge(
				array_slice( $main_array, 0, $index ),
				$insert_array,
				array_slice( $main_array, $index )
			);
		}

		return $main_array;
	}
	public function insert_new_controls( $field_id, $new_option, $fields, $is_after = false ) {
		trigger_error(
			sprintf(
				'Method %1$s is <strong>deprecated</strong> since version %2$s use. $this->insert_controls() instead',
				__METHOD__,
				'2.3.5'
			),
			E_USER_DEPRECATED
		);
	}
	/**
	 * Modify some element
	 *
	 * @param array $modify_controls Modify controls.
	 * @param array $fields main array.
	 * @return array
	 */
	public function modify_controls( $modify_controls, $fields ) {
		trigger_error(
			sprintf(
				'Method %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
				__METHOD__,
				'2.3.5'
			),
			E_USER_DEPRECATED
		);
		return $fields;
	}
	/**
	 * Remove some element
	 *
	 * @param array $field_ids fields id.
	 * @param array $fields main array.
	 * @return array
	 */
	public function remove_controls( $field_ids, $fields ) {
		trigger_error(
			sprintf(
				'Method %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
				__METHOD__,
				'2.5.0'
			),
			E_USER_DEPRECATED
		);
		return $fields;
	}

	/**
	 * Elementor Fields.
	 *
	 * @param string $type Control type.
	 *
	 * @return string
	 */
	private static function el_fields( $type ) {
		
		$controls = Controls_Manager::class;

		switch ( $type ) {
			
			case 'link':
				$type = $controls::URL;
				break;
	
			//case 'image-dimensions':
			//	$type = $controls::IMAGE_DIMENSIONS;
			//	break;
				
			case 'html':
				$type = $controls::RAW_HTML;
				break;
			
			case 'switch':
				$type = $controls::SWITCHER;
				break;
			
			case 'typography':
				$type = Group_Control_Typography::get_type();
				break;

			case 'border':
				$type = Group_Control_Border::get_type();
				break;

			case 'background':
				$type = Group_Control_Background::get_type();
				break;

			case 'box-shadow':
				$type = Group_Control_Box_Shadow::get_type();
				break;

			case 'text-shadow':
				$type = Group_Control_Text_Shadow::get_type();
				break;

			case 'text-stroke':
				$type = Group_Control_Text_Stroke::get_type();
				break;
			default: 
				$type = constant( 'Elementor\Controls_Manager::'. strtoupper( $type ) ); 
				
		}

		return $type;
	}
	/**
	 * Elementor Fields.
	 *
	 * @param string $tab Control type.
	 *
	 * @return string
	 */
	private static function el_tabs( $tab ) {
		return constant( 'Elementor\Controls_Manager::TAB_'. strtoupper( $tab ) ); 
	}
	



}
