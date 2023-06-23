<?php
namespace Rtcl\Helpers;


class Helper {

	/**
	 * Helper singleton
	 *
	 * @var Helper
	 */
	private static $_instance = null;

	/**
	 * Helper data container
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Singleton, returns Helper instance
	 *
	 * @return Helper
	 */
	public static function instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Returns adverts saved data
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( isset( $this->_data[ $key ] ) ) {
			return $this->_data[ $key ];
		} else {
			return $default;
		}
	}

	/**
	 * Sets adverts option
	 *
	 * @param string $key
	 * @param mixed $data
	 */
	public function set( $key, $data ) {
		$this->_data[ $key ] = $data;
	}
}