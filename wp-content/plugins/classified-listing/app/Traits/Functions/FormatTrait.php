<?php


namespace Rtcl\Traits\Functions;

trait FormatTrait {
	/**
	 * Converts php DateTime format to Javascript Moment format.
	 *
	 * @param string $phpFormat
	 *
	 * @return string
	 */
	public static function dateFormatPHPToMoment( $phpFormat ) {
		$replacements = [
			'A' => 'A',      // for the sake of escaping below
			'a' => 'a',      // for the sake of escaping below
			'B' => '',       // Swatch internet time (.beats), no equivalent
			'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
			'D' => 'ddd',
			'd' => 'DD',
			'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
			'F' => 'MMMM',
			'G' => 'H',
			'g' => 'h',
			'H' => 'HH',
			'h' => 'hh',
			'I' => '',       // Daylight Saving Time? => moment().isDST();
			'i' => 'mm',
			'j' => 'D',
			'L' => '',       // Leap year? => moment().isLeapYear();
			'l' => 'dddd',
			'M' => 'MMM',
			'm' => 'MM',
			'N' => 'E',
			'n' => 'M',
			'O' => 'ZZ',
			'o' => 'YYYY',
			'P' => 'Z',
			'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
			'S' => 'o',
			's' => 'ss',
			'T' => 'z',      // deprecated since version 1.6.0 of moment.js
			't' => '',       // days in the month => moment().daysInMonth();
			'U' => 'X',
			'u' => 'SSSSSS', // microseconds
			'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
			'W' => 'W',      // for the sake of escaping below
			'w' => 'e',
			'Y' => 'YYYY',
			'y' => 'YY',
			'Z' => '',       // time zone offset in minutes => moment().zone();
			'z' => 'DDD',
		];

		// Converts escaped characters.
		foreach ( $replacements as $from => $to ) {
			$replacements[ '\\' . $from ] = '[' . $from . ']';
		}

		return strtr( $phpFormat, $replacements );
	}

	public static function dateFormatPHPTojQueryUI( $string ) {

		$map = [
			// PHP Date format character => jQueryUI Datepicker/DateTimepicker format character.
			// Day.
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week.
			'W' => '',
			// Month.
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year.
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time.
			'a' => 'tt',
			'A' => 'TT',
			'B' => '',
			'g' => 'h',
			'G' => 'H',
			'h' => 'hh',
			'H' => 'HH',
			'i' => 'mm',
			's' => 'ss',
			'u' => 'c',
		];

		$format   = '';
		$escaping = false;

		for ( $i = 0; $i < strlen( $string ); $i ++ ) {

			$char = $string[ $i ];

			// PHP date format escaping character.
			if ( $char === '\\' ) {

				$i ++;

				if ( $escaping ) {

					$format .= $string[ $i ];

				} else {

					$format .= '\'' . $string[ $i ];
				}

				$escaping = true;

			} else {

				if ( $escaping ) {

					$format   .= '\'';
					$escaping = false;
				}

				if ( isset( $map[ $char ] ) ) {

					$format .= $map[ $char ];

				} else {

					$format .= $char;
				}
			}
		}

		//If the escaping is still open, make sure to close it. So formatting like this will work: `H\h i\m\i\n`.
		if ( $escaping ) {
			$format .= '\'';
		}

		return $format;
	}

	/**
	 * Merge two arrays.
	 *
	 * @param array $a1 First array to merge.
	 * @param array $a2 Second array to merge.
	 *
	 * @return array
	 */
	static function array_overlay( $a1, $a2 ) {
		foreach ( $a1 as $k => $v ) {
			if ( ! array_key_exists( $k, $a2 ) ) {
				continue;
			}
			if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
				$a1[ $k ] = self::array_overlay( $v, $a2[ $k ] );
			} else {
				$a1[ $k ] = $a2[ $k ];
			}
		}

		return $a1;
	}

	/**
	 * Wrapper for mb_strtoupper which see's if supported first.
	 *
	 * @param string $string String to format.
	 *
	 * @return string
	 */
	static function strtoupper( $string ) {
		return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string ) : strtoupper( $string );
	}
}
