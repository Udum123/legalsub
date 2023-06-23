<?php


namespace Rtcl\Helpers;


use Rtcl\Traits\Functions\DateTrait;
use Rtcl\Traits\Functions\FormatTrait;

class Utility {
	use FormatTrait;


	/**
	 * Format a time supplied as string to a format from a format.
	 *
	 * @param string $value
	 * @param null $to
	 * @param null $from
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function formatTime( $value, $to = null, $from = null ) {

		$to   = is_null( $to ) ? Functions::time_format() : $to;
		$from = is_null( $from ) ? Functions::time_format() : $from;

		if ( strlen( $value ) > 0 ) {

			return DateTrait::createFromFormat( $from, $value )->format( $to );

		} else {

			return $value;
		}
	}

	/**
	 * Format a time supplied as string to a format from a format.
	 *
	 * @param string $value
	 * @param null $to
	 * @param null $from
	 *
	 * @return string
	 * @since 1.0
	 *
	 */
	public static function formatDate( $value, $to = null, $from = null ) {

		$to   = is_null( $to ) ? Functions::date_format() : $to;
		$from = is_null( $from ) ? Functions::date_format() : $from;

		if ( strlen( $value ) > 0 ) {

			return DateTrait::createFromFormat( $from, $value )->format( $to );

		} else {

			return $value;
		}
	}
}
