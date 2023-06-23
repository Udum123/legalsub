<?php

namespace Rtcl\Controllers\Admin\Meta;


use Rtcl\Resources\Options;

class SaveCFGData {
	public function __construct() {
		add_action( 'save_post', [ $this, 'save_cfg_data' ], 10, 1 );
		add_action( 'before_delete_post', [ $this, 'before_cfg_delete_post' ] );
	}

	public function save_cfg_data( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		$post_type = get_post_type( $post_id );
		if ( rtcl()->post_type_cfg != $post_type ) {
			return;
		}

		if ( isset( $_POST['associate'] ) ) {
			$associate = ! empty( $_POST['associate'] ) && $_POST['associate'] == 'categories' ? 'categories' : 'all';
			update_post_meta( $post_id, 'associate', $associate );
		}


		$fields = ! empty( $_REQUEST['rtcl']['fields'] ) ? $_REQUEST['rtcl']['fields'] : [];
		if ( ! empty( $fields ) ) {

			$i = 0;
			foreach ( $fields as $field_id => $field ) {
				$type    = get_post_meta( $field_id, "_type", true );
				$type    = array_key_exists( $type, Options::get_custom_field_list() ) ? $type : 'text';
				$options = Options::get_custom_field_list()[ $type ]['options'];
				if ( is_array( $options ) && ! empty( $options ) ) {


					foreach ( $options as $meta_key => $opt ) {
						$value = null;
						if ( $meta_key == '_options' ) {
							$optValue            = [];
							$optValue['default'] = null;
							$default             = ! empty( $field[ $meta_key ]['default'] ) && ! is_array( $field[ $meta_key ]['default'] ) ? esc_attr( $field[ $meta_key ]['default'] ) : null;
							if ( $opt['type'] == 'checkbox' ) {
								$default = ! empty( $field[ $meta_key ]['default'] ) && is_array( $field[ $meta_key ]['default'] ) ? $field[ $meta_key ]['default'] : [];
							}
							if ( ! empty( $field[ $meta_key ]['choices'] ) ) {
								foreach ( $field[ $meta_key ]['choices'] as $choiceID => $choice ) {
									if ( ! empty( $choice['value'] ) ) {
										$ct                         = ! empty( $choice['title'] ) ? esc_attr( $choice['title'] ) : null;
										$cv                         = ! empty( $choice['value'] ) ? sanitize_title( esc_attr( $choice['value'] ) ) : sanitize_title( $ct );
										$ct                         = $ct ? $ct : $cv;
										$optValue['choices'][ $cv ] = $ct;
										if ( $type == 'checkbox' ) {
											if ( is_array( $default ) && ! empty( $default ) && in_array( $choiceID,
													$default ) ) {
												$optValue['default'][] = $cv;
											}
										} elseif ( $type == 'select' || $type == 'radio' ) {
											if ( $default && $default == $choiceID ) {
												$optValue['default'] = $cv;
											}
										}
									}
								}
							}
							$value = $optValue;
						} else if ( $meta_key == '_conditional_logic' ) {
							$value   = 0;
							$groups  = ! empty( $field[ $meta_key ] ) ? $field[ $meta_key ] : [];
							$nGroups = [];
							if ( ! empty( $groups ) && is_array( $groups ) ) {
								foreach ( $groups as $group ) {
									if ( empty( $group ) || ! is_array( $group ) ) {
										continue;
									}
									$nRules = [];
									foreach ( $group as $rule ) {
										if ( ! empty( $rule ) && is_array( $rule ) ) {
											$rule = wp_parse_args( $rule, [
												'field'    => '',
												'operator' => '',
												'value'    => '',
											] );
											if ( $rule['field'] || $rule['operator'] || $rule['value'] ) {
												$nRules[] = $rule;
											}
										}
									}
									if ( ! empty( $nRules ) ) {
										$nGroups[] = $nRules;
									}
								}
							}
							if ( ! empty( $nGroups ) ) {
								$value = $nGroups;
							}
						} else {
							$value = ! empty( $field[ $meta_key ] ) ? esc_attr( $field[ $meta_key ] ) : null;
						}
						update_post_meta( $field_id, $meta_key, $value );
					}
					$arg = [ 'ID' => $field_id, 'menu_order' => $i ];
					if ( get_post_status( $field_id ) != 'publish' ) {
						$arg['post_status'] = 'publish';
					}
					remove_action( 'save_post', [ $this, 'save_cfg_data' ], 10 );
					wp_update_post( $arg );
					add_action( 'save_post', [ $this, 'save_cfg_data' ], 10 );
					$i ++;
				}
			}

		}
	}

	public function before_cfg_delete_post( $post_id ) {
		global $post_type;

		if ( rtcl()->post_type_cfg != $post_type ) {
			return;
		}

		$args              = [
			'post_type'           => rtcl()->post_type_cf,
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post_parent'         => $post_id,
			'suppress_filters'    => false
		];
		$custom_fields_ids = get_posts( apply_filters( 'rtcl_before_cfg_delete_post_args', $args ) );

		if ( ! empty( $custom_fields_ids ) ) {
			foreach ( $custom_fields_ids as $custom_fields_id ) {
				wp_delete_post( $custom_fields_id, true );
			}
		}
	}
}