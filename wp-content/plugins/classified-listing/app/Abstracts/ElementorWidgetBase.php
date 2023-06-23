<?php
/**
 * Elementor addons base.
 *
 * @package  Classifid-listing
 * @subpackage Classifid-listing/Abstracts
 */

namespace Rtcl\Abstracts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
/**
 * Abstract ElementorWidgetBase Class
 *
 * Implemented by classes using using for elementor addons development.
 *
 * @version  1.0.0
 * @package  Classifid-listing/Abstracts
 */
abstract class ElementorWidgetBase extends Widget_Base {
	/**
	 * Widget Title.
	 *
	 * @var String
	 */
	public $rtcl_name;
	/**
	 * Widget name.
	 *
	 * @var String
	 */
	public $rtcl_base;
	/**
	 * Widget categories.
	 *
	 * @var String
	 */
	public $rtcl_category;

	/**
	 * Widget translate.
	 *
	 * @var String
	 */
	// Todo: fix issue 
	//public $rtcl_translate;
	/**
	 * Widget icon class
	 *
	 * @var String
	 */
	public $rtcl_icon;
	/**
	 * Plugin dirname
	 *
	 * @var String
	 */
	public $rtcl_dir;
	/**
	 * Undocumented function
	 *
	 * @param array $data default data.
	 * @param array $args default arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_category = 'rtcl-elementor-widgets'; // Category /@dev.
		$this->rtcl_icon     = 'rtcl-el-custom';
		$this->rtcl_dir      = dirname( RTCL_PLUGIN_FILE );
		parent::__construct( $data, $args );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_style' ] );
	}

	/**
	 * Set Query controls
	 *
	 * @return array
	 */
	abstract public function widget_general_fields() : array;
	/**
	 * Set Style controls
	 *
	 * @return array
	 */
	abstract public function widget_style_fields() : array;
	/**
	 * Elementor controls marge all settings
	 *
	 * @return array
	 */
	public function widget_fields() {
		$fields = array_merge(
			$this->widget_general_fields(),
			$this->pro_notice_fields(),
			$this->widget_style_fields()
		);
		return apply_filters( 'rtcl_widget_fields_' . $this->rtcl_base, $fields, $this );
	}
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
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_pro_sec',
				'label' => '<span style="color: #f54">' . esc_html__( 'Go Premium for More Features', 'classified-listing' ) . '</span>',
			],
			[
				'id'   => 'rtcl_get_pro_version',
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div class="elementor-nerd-box">
						<div class="elementor-nerd-box-title" style="margin-top: 0; margin-bottom: 20px;">Unlock more possibilities</div>
						<div class="elementor-nerd-box-message"><span class="pro-feature" style="font-size: 13px;"> Get the  <a href="https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/" target="_blank" style="color: #f54">Pro version</a> for more stunning layouts and customization options.</span></div>
						<a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-button-go-pro"
							href="https://www.radiustheme.com/downloads/classified-listing-pro-wordpress/"
							target="_blank">
							Get Pro </a>
						</div>',
			],
			[
				'mode' => 'section_end',
			],

		];
		return $fields;
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return $this->rtcl_base;
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function editor_style() {
		$img = RTCL_URL . '/assets/images/icon-20x20.png';
		$css = '
			.elementor-control .elementor-control-title {text-transform: capitalize; }
			.elementor-element .icon .rtcl-el-custom{content: url( ' . $img . ');width: 20px;}
			.select2-container--default .select2-selection--single {min-width: 126px !important; min-height: 30px !important;}
			.elementor-control .rtcl-elementor-group-heading {
				font-weight: bold;
				border-left: 4px solid #93003c;
				padding: 10px;
				background: #f1f1f1;
				color: #495157;
			}
		';
		wp_add_inline_style( 'elementor-editor', $css );
	}
	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return $this->rtcl_name;
	}
	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return $this->rtcl_icon;
	}
	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ $this->rtcl_category ];
	}
	/**
	 * Pro label.
	 *
	 * @return String
	 */
	public function get_pro_label() {
		if ( ! rtcl()->has_pro() ) {
			return ' <span style="color:red">(Pro)</span>';
		}
		return '';
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

		$fields = apply_filters( 'rtcl_el_widgets_fields/' . $this->rtcl_base , $fields, $this );

		// Array Value key as value will best.
		foreach ( $fields as $field ) {
			if ( isset( $field['mode'] ) && 'section_start' === $field['mode'] ) {
				$id = $field['id'];
				unset( $field['id'] );
				unset( $field['mode'] );
				$this->start_controls_section( $id, $field );
			} elseif ( isset( $field['mode'] ) && 'section_end' === $field['mode'] ) {
				$this->end_controls_section();
			} elseif ( isset( $field['mode'] ) && 'tabs_start' === $field['mode'] ) {
				$id = $field['id'];
				unset( $field['id'] );
				unset( $field['mode'] );
				$this->start_controls_tabs( $id );
			} elseif ( isset( $field['mode'] ) && 'tabs_end' === $field['mode'] ) {
				$this->end_controls_tabs();
			} elseif ( isset( $field['mode'] ) && 'tab_start' === $field['mode'] ) {
				$id = $field['id'];
				unset( $field['id'] );
				unset( $field['mode'] );
				$this->start_controls_tab( $id, $field );
			} elseif ( isset( $field['mode'] ) && 'tab_end' === $field['mode'] ) {
				$this->end_controls_tab();
			} elseif ( isset( $field['mode'] ) && 'group' === $field['mode'] ) {
				$type          = $field['type'];
				$field['name'] = $field['id'];
				unset( $field['mode'] );
				unset( $field['type'] );
				unset( $field['id'] );
				$this->add_group_control( $type, $field );
			} elseif ( isset( $field['mode'] ) && 'responsive' === $field['mode'] ) {
				$id = $field['id'];
				unset( $field['id'] );
				unset( $field['mode'] );
				$this->add_responsive_control( $id, $field );
			} elseif ( isset( $field['mode'] ) && 'repeater' === $field['mode'] ) {
				$repeater       = new Repeater();
				$repeter_fields = $field['fields'];
				foreach ( $repeter_fields as $key => $value ) {
					$repeater->add_control(
						$key,
						$value
					);
				}
				$field['fields'] = $repeater->get_controls();
				$id              = $field['id'];
				$this->add_control( $id, $field );
			} else {
				$id = $field['id'];
				unset( $field['id'] );
				$this->add_control( $id, $field );
			}
		}
	}

	/**
	 * Alignment settings snipate
	 *
	 * @return array
	 */
	public function alignment_options() {
		return [
			'left'   => [
				'title' => esc_html__( 'Left', 'classified-listing' ),
				'icon'  => 'eicon-text-align-left',
			],
			'center' => [
				'title' => esc_html__( 'Center', 'classified-listing' ),
				'icon'  => 'eicon-text-align-center',
			],
			'right'  => [
				'title' => esc_html__( 'Right', 'classified-listing' ),
				'icon'  => 'eicon-text-align-right',
			],
		];
	}

	/**
	 * Option New Field
	 *
	 * @param [type] $field_id Potision Before option id .
	 * @param [type] $new_option new option.
	 * @param [type] $fields main array.
	 * @return array
	 */
	public function insert_new_controls( $field_id, $new_option, $fields, $is_after = false ) {
		$match = array_filter(
			$fields,
			function( $ar ) use ( $field_id ) {
				if ( isset( $ar['id'] ) ) {
					return $field_id === $ar['id'];
				}
				return false;
			}
		);
		$index = array_key_first( $match );
		if ( $index ) {
			if ( $is_after ) {
				$index += 1;
			}
			$fields = array_merge(
				array_slice( $fields, 0, $index ),
				$new_option,
				array_slice( $fields, $index )
			);
		}
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
		$match = array_filter(
			$fields,
			function( $ar ) use ( $field_ids ) {
				if ( isset( $ar['id'] ) ) {
					return ! in_array( $ar['id'], $field_ids, true );
				}
				return true;
			}
		);
		return $match;
	}

	/**
	 * Modify some element
	 *
	 * @param array $modify_controls Modify controls.
	 * @param array $fields main array.
	 * @return array
	 */
	public function modify_controls( $modify_controls, $fields ) {
		$modifyed = array_map(
			function( $field ) use ( $modify_controls ) {
				foreach ( $modify_controls as $mod ) {
					if ( isset( $field ['id'] ) && ( $mod['id'] === $field ['id'] ) ) {
						if ( isset( $mod['unset'] ) && ! empty( $mod['unset'] ) ) {
							foreach ( $mod['unset'] as $unset ) {
								unset( $field[ $unset ] );
							}
							unset( $mod['unset'] );
						}
						return array_merge(
							$field,
							$mod
						);
					}
				}
				return $field;
			},
			$fields
		);
		return $modifyed;
	}
	/**
	 * Alignment settings snipate
	 *
	 * @return array
	 */
	public function column_number() {
		return [
			'8' => __( 'Column 8', 'classified-listing' ),
			'7' => __( 'Column 7', 'classified-listing' ),
			'6' => __( 'Column 6', 'classified-listing' ),
			'5' => __( 'Column 5', 'classified-listing' ),
			'4' => __( 'Column 4', 'classified-listing' ),
			'3' => __( 'Column 3', 'classified-listing' ),
			'2' => __( 'Column 2', 'classified-listing' ),
			'1' => __( 'Column 1', 'classified-listing' ),
		];
	}

	/**
	 * Elementor Edit mode need some extra js for isotop reinitialize
	 *
	 * @return mixed
	 */
	public function edit_mode_script() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$selector = $this->get_unique_selector() . ' .rtcl-carousel-slider';
			?>
			<script>
				jQuery('<?php echo esc_attr( $selector ); ?>').rtcl_slider();
			</script>
			<?php
		}
	}
	/**
	 * Undocumented function
	 *
	 * @param string $parent variable.
	 * @param string $taxonomy variable.
	 * @return array
	 */
	protected function taxonomy_list( $parent = 'all', $taxonomy = '' ) {
		$args = [
			'taxonomy'   => rtcl()->category,
			'fields'     => 'id=>name',
			'hide_empty' => true,
		];
		if ( ! empty( $taxonomy ) ) {
			$args['taxonomy'] = sanitize_text_field( $taxonomy );
		}
		if ( 'parent' === $parent ) {
			$args['parent'] = 0;
		}
		$terms = get_terms( $args );

		$category_dropdown = [];
		foreach ( $terms as $id => $name ) {
			$category_dropdown[ $id ] = $name;
		}
		return $category_dropdown;
	}

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	public function image_size() {
		$settings = $this->get_settings_for_display();
		// Image size id 'rtcl_thumb_image' but need to write 'rtcl_thumb_image_size'.
		$image_size = isset( $settings['rtcl_thumb_image_size'] ) ? $settings['rtcl_thumb_image_size'] : 'rtcl-thumbnail';
		if ( 'custom' === $image_size ) {
			$image_size = isset( $settings['rtcl_thumb_image_custom_dimension'] ) ? $settings['rtcl_thumb_image_custom_dimension'] : [ 400, 280 ];
			if ( isset( $image_size['height'] ) && isset( $image_size['width'] ) ) {
				$image_size = [
					$image_size['width'],
					$image_size['height'],
					true,
				];
			}
		}
		return $image_size;
	}
}
