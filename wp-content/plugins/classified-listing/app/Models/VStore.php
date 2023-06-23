<?php

namespace Rtcl\Models;

class VStore {

	private array $data = [];

	public function __construct( $id = '', $data = '' ) {
		if ( empty( $id ) ) {
			return;
		}

		$this->add( $id, $data );
	}


	/**
	 * Adds an error or appends an additional message to an existing error.
	 *
	 * @param string|int $id Data code.
	 * @param mixed $data Optional data.
	 *
	 * @since 2.1.0
	 *
	 */
	public function add( $id, $data = '' ) {
		$this->data[ $id ] = $data;

		/**
		 * @param string|int $id Error code.
		 * @param mixed $data Error data. Might be empty.
		 * @param VStore $wp_error The WP_Error object.
		 */
		do_action( 'rtcl_vstore_added', $id, $data, $this );
	}

	/**
	 * @return bool
	 */
	public function has_data(): bool {
		if ( ! empty( $this->data ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return array|int[]|string[]
	 */
	public function get_ids(): array {
		if ( ! $this->has_data() ) {
			return [];
		}

		return array_keys( $this->data );
	}

	public function get_id() {
		$ids = $this->get_ids();

		if ( empty( $ids ) ) {
			return '';
		}

		return $ids[0];
	}

	/**
	 * @param string|null $id
	 *
	 * @return mixed null will return when not exist
	 */
	public function get( string $id = null ) {
		if ( empty( $id ) ) {
			$id = $this->get_id();
		}
		if ( empty( $id ) ) {
			return null;
		}

		return $this->data[ $id ] ?? null;

	}

	/**
	 * @return array
	 */
	public function get_all_data(): array {
		return $this->data;
	}


}