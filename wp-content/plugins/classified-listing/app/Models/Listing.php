<?php

namespace Rtcl\Models;


use DateTimeImmutable;
use Rtcl\Abstracts\Data;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;
use WP_Post;
use WP_Query;
use WP_User;

class Listing extends Data {

	protected $id;
	protected $listing;
	protected $status;
	protected $post_date;
	protected $date_created;
	protected $type;
	protected $post_content;
	protected $user_id;
	protected $moderation_settings = [];
	protected $general_settings = [];
	protected $misc_settings = [];
	protected $page_settings = [];
	protected $categories;
	protected $locations = [];
	protected $price_units = null;
	protected $price_unit;
	protected $price_type;
	protected $images = null;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'rtcl_listing';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'rtcl_listing';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'rtcl_listings';

	protected $owner = null;

	/**
	 * Stores product data.
	 *
	 * @var array
	 */
	protected $data = [
		'name'               => '',
		'slug'               => '',
		'date_created'       => null,
		'date_modified'      => null,
		'status'             => false,
		'featured'           => false,
		'description'        => '',
		'price'              => '',
		'parent_id'          => 0,
		'reviews_allowed'    => true,
		'attributes'         => [],
		'default_attributes' => [],
		'menu_order'         => 0,
		'category_ids'       => [],
		'tag_ids'            => [],
		'rating_counts'      => [],
		'average_rating'     => 0,
		'review_count'       => 0,
	];

	/**
	 * Get the product if ID is passed, otherwise the product is new and empty.
	 * This class should NOT be instantiated, but the get_listing() function
	 * should be used. It is possible, but the get_listing() is preferred.
	 *
	 * @param int|Listing|object $listing Listing to init.
	 *
	 * @throws \Exception
	 */
	function __construct( $listing = 0 ) {
		parent::__construct( $listing );
		if ( is_numeric( $listing ) && $listing > 0 ) {
			$this->set_id( $listing );
		} elseif ( $listing instanceof self ) {
			$this->set_id( absint( $listing->get_id() ) );
		} elseif ( ! empty( $listing->ID ) ) {
			$this->set_id( absint( $listing->ID ) );
		} elseif ( rtcl()->post_type === get_post_type() && $listing_id = get_the_ID() ) {
			$this->set_id( $listing_id );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = DataStore::load( 'listing' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}

		$listing = get_post( $listing );
		if ( is_object( $listing ) && $listing->post_type == rtcl()->post_type ) {
			$this->listing      = $listing;
			$this->id           = $listing->ID;
			$this->status       = $listing->post_status;
			$this->post_date    = $listing->post_date;
			$this->post_content = $listing->post_content;
			$this->user_id      = $listing->post_author;
			$this->type         = get_post_meta( $this->id, 'ad_type', true );
			$this->categories   = wp_get_object_terms( $this->id, rtcl()->category );
			$this->setTermsOrder();
			if ( 'local' === Functions::location_type() ) {
				$this->locations = wp_get_object_terms( $this->id, rtcl()->location );
				$this->setTermsOrder( 'location' );
			}

		}

	}

	/**
	 * By default wp_get_object_terms get all the terms as order by name , need to order them by ancestor order
	 *
	 * @param string $target_term
	 */
	private function setTermsOrder( $target_term = 'category' ) {
		$target   = 'locations';
		$taxonomy = rtcl()->location;
		if ( $target_term === 'category' ) {
			$target   = 'categories';
			$taxonomy = rtcl()->category;
		}

		$raw_terms = $this->$target;

		if ( $raw_terms_length = count( $raw_terms ) ) {
			$term_ancestors = [];
			$last_term      = 0;
			foreach ( $raw_terms as $index => $raw_term ) {
				$ancestors = get_ancestors( $raw_term->term_id, $taxonomy );
				if ( ! empty( $ancestors ) && count( $ancestors ) >= count( $term_ancestors ) ) {
					$term_ancestors = $ancestors;
					$last_term      = $raw_term->term_id;
				}
			}
			if ( ! empty( $term_ancestors ) && $last_term ) {
				$term_ancestors   = array_reverse( $term_ancestors );
				$term_ancestors[] = $last_term;
				$terms            = [];
				foreach ( $term_ancestors as $term_ancestor ) {
					foreach ( $raw_terms as $k => $raw_term ) {
						if ( $raw_term->term_id === $term_ancestor ) {
							$terms[] = $raw_term;
							unset( $raw_terms[ $k ] );
							break;
						}
					}
				}
			}
			$this->$target = ! empty( $terms ) ? $terms : $this->$target;
		}

	}


	/**
	 * Course is exists if the post is not empty
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->get_id() && rtcl()->post_type === get_post_type( $this->get_id() );
	}

	/**
	 * Set rating counts. Read only.
	 *
	 * @param array $counts Product rating counts.
	 */
	public function set_rating_counts( $counts ) {
		$this->set_prop( 'rating_counts', array_filter( array_map( 'absint', (array) $counts ) ) );
	}


	/**
	 * Set average rating. Read only.
	 *
	 * @param float $average Product average rating.
	 */
	public function set_average_rating( $average ) {
		$this->set_prop( 'average_rating', Functions::format_decimal( $average ) );
	}


	/**
	 * Set review count. Read only.
	 *
	 * @param int $count Listing review count.
	 */
	public function set_review_count( $count ) {
		$this->set_prop( 'review_count', absint( $count ) );
	}


	/**
	 * Get product name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get product slug.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'slug', $context );
	}

	/**
	 * Get product created date.
	 *
	 * @return DateTimeImmutable|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_created() {
		if ( ! $this->date_created ) {
			$this->date_created = get_post_datetime( $this->listing );
		}

		return $this->date_created;
	}


	/**
	 * Get product description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Get product short description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_short_description( $context = 'view' ) {
		return $this->get_prop( 'short_description', $context );
	}

	/**
	 * Return if reviews is allowed.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function get_reviews_allowed( $context = 'view' ) {
		return $this->get_prop( 'reviews_allowed', $context );
	}

	/**
	 * Get menu order.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return int
	 * @since 3.0.0
	 */
	public function get_menu_order( $context = 'view' ) {
		return $this->get_prop( 'menu_order', $context );
	}

	/**
	 * Returns Post Object.
	 *
	 * @return \WP_Post
	 * @since  1.0.0
	 */
	public function get_listing() {
		return $this->listing;
	}

	/**
	 * Returns the unique ID for this object.
	 *
	 * @return int
	 * @since  1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	public function get_status() {
		return $this->listing->post_status;
	}


	/**
	 * @return integer
	 */
	public function get_owner_id() {
		return absint( $this->user_id );
	}

	/**
	 * @return WP_User|false WP_User object on success, false on failure.
	 */
	public function get_owner() {
		if ( $this->owner ) {
			return $this->owner;
		}
		$user        = get_userdata( $this->user_id );
		$this->owner = $user;

		return $this->owner;
	}


	/**
	 * @return string
	 */
	public function get_owner_name() {
		$owner = $this->get_owner();
		if ( $owner ) {
			return $owner->display_name;
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function get_owner_user_name() {
		$owner = $this->get_owner();
		if ( $owner ) {
			return $owner->user_login;
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function get_owner_email() {
		$owner = $this->get_owner();
		if ( $owner ) {
			return $owner->user_email;
		}

		return '';
	}

	/**
	 *
	 */
	public function get_the_title() {
		return apply_filters( 'rtcl_listing_get_the_title', get_the_title( $this->listing ) );
	}

	/**
	 * Get Listing email
	 *
	 * @return string Listing email address
	 */
	public function get_email() {
		$email = get_post_meta( $this->id, 'email', true );

		return apply_filters( 'rtcl_listing_get_email', $email );
	}


	/**
	 *
	 */
	public function get_the_permalink() {
		return apply_filters( 'rtcl_listing_get_the_title', get_the_permalink( $this->listing ) );
	}

	/**
	 *
	 */
	public function the_title() {
		echo apply_filters( 'rtcl_listing_the_title', $this->get_the_title() );
	}

	/**
	 *
	 */
	public function the_permalink() {
		echo apply_filters( 'rtcl_listing_the_permalink', get_the_permalink( $this->listing ) );
	}

	/**
	 * @return int
	 */
	function is_featured() {
		return (bool) absint( get_post_meta( $this->id, 'featured', true ) );
	}

	/**
	 * @return boolean
	 */
	function is_buy() {
		return $this->type === 'buy';
	}

	/**
	 * @return boolean
	 */
	function is_sell() {
		return $this->type === 'sell';
	}

	/**
	 * @return boolean
	 */
	function is_exchange() {
		return $this->type === 'exchange';
	}

	/**
	 * @return boolean
	 */
	function is_job() {
		return $this->type === 'job';
	}

	/**
	 * @return boolean
	 */
	function is_to_let() {
		return $this->type === 'to_let';
	}

	/**
	 * @return bool
	 */
	function is_new() {
		$each_hours            = 60 * 60 * 24; // seconds in a day
		$s_date1               = strtotime( current_time( 'mysql' ) ); // seconds for date 1
		$s_date2               = strtotime( $this->post_date ); // seconds for date 2
		$s_date_diff           = abs( $s_date1 - $s_date2 ); // different of the two dates in seconds
		$days                  = round( $s_date_diff / $each_hours ); // divided the different with second in a day
		$new_listing_threshold = Functions::get_option_item( 'rtcl_moderation_settings', 'new_listing_threshold', 3, 'number' );
		if ( $days <= $new_listing_threshold ) {
			return true;
		}

		return false;
	}

	public function is_mark_as_sold() {
		_deprecated_function( __METHOD__, '2.0.5', '\RtclPro\Helpers\Fns::is_mark_as_sold($listing_id)' );

		return (bool) absint( get_post_meta( $this->get_id(), '_rtcl_mark_as_sold', true ) );
	}

	/**
	 * @return bool
	 */
	public function isExpired(): bool {
		return 'rtcl-expired' === $this->get_status();
	}

	/**
	 * @return bool
	 */
	function has_price_units() {
		return ! empty( $this->get_price_units() );
	}

	/**
	 * @return array|mixed
	 */
	function get_price_units() {
		if ( is_array( $this->price_units ) ) {
			return $this->price_units;
		}
		$category   = null;
		$categories = $this->get_categories();
		if ( ! empty( $categories ) ) {
			if ( count( $categories ) > 1 ) {
				foreach ( $categories as $term ) {
					if ( $term->parent ) {
						$category = $term;
					}
				}
			} else {
				$category = $categories[0];
			}
		}
		if ( $category ) {
			$this->price_units = get_term_meta( $category->term_id, '_rtcl_price_units' );
			if ( empty( $this->price_units ) ) {
				if ( $category->parent ) {
					$this->price_units = get_term_meta( $category->parent, '_rtcl_price_units' );
				}
			}
		}

		return $this->price_units;
	}

	function get_price_unit() {
		if ( $this->price_unit ) {
			return $this->price_unit;
		}

		return get_post_meta( $this->get_id(), '_rtcl_price_unit', true );
	}


	function get_pricing_type() {
		$pricing_type = get_post_meta( $this->get_id(), '_rtcl_listing_pricing', true );

		return in_array( $pricing_type, array_keys( Options::get_listing_pricing_types() ) ) ? $pricing_type : 'price';
	}

	function get_price_type() {
		if ( $this->price_type ) {
			return $this->price_type;
		}

		$price_type       = get_post_meta( $this->get_id(), 'price_type', true );
		$this->price_type = $price_type ? $price_type : 'regular';

		return $this->price_type;
	}

	function has_phone() {
		if ( get_post_meta( $this->id, 'phone', true ) ) {
			return true;
		}

		return false;

	}

	/**
	 * @return bool
	 */
	function has_location() {
		if ( "geo" === Functions::location_type() ) {
			return get_post_meta( $this->id, '_rtcl_geo_address', true );
		} else {
			return ! empty( $this->locations );
		}
	}

	function has_category() {
		return ! empty( $this->categories );
	}

	function can_edit() {
		if ( get_current_user_id() == $this->user_id && in_array( $this->status, [
				'publish',
				'draft',
				'rtcl-reviewed'
			] ) ) {
			return true;
		}

		return false;
	}

	function can_delete() {
		if ( get_current_user_id() == $this->user_id ) {
			return true;
		}

		return false;
	}


	function can_show_date() {
		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_date = ! empty( $this->moderation_settings[ $display_option ] ) && in_array( 'date', $this->moderation_settings[ $display_option ] );

		return apply_filters( 'rtcl_listing_can_show_date', $can_show_date, $this );
	}

	function can_show_category() {
		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_category = ! empty( $this->moderation_settings[ $display_option ] ) && in_array( 'category', $this->moderation_settings[ $display_option ] );

		return apply_filters( 'rtcl_listing_can_show_category', $can_show_category, $this );
	}

	function can_show_location() {
		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_location = ! empty( $this->moderation_settings[ $display_option ] ) && in_array( 'location', $this->moderation_settings[ $display_option ] );

		return apply_filters( 'rtcl_listing_can_show_location', $can_show_location, $this );
	}

	function can_show_views() {
		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_views = ! empty( $this->moderation_settings[ $display_option ] ) && in_array( 'views', $this->moderation_settings[ $display_option ] );

		return apply_filters( 'rtcl_listing_can_show_views', $can_show_views, $this );
	}

	function can_show_user() {
		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_user = ! empty( $this->moderation_settings[ $display_option ] ) && in_array( 'user', $this->moderation_settings[ $display_option ] );

		return apply_filters( 'rtcl_listing_can_show_user', $can_show_user, $this );
	}

	function can_add_user_link() {
		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_user_link = ! empty( $this->moderation_settings[ $display_option ] ) && in_array( 'user_link', $this->moderation_settings[ $display_option ] );

		return apply_filters( 'rtcl_listing_can_show_user_link', $can_show_user_link, $this );
	}

	function can_show_excerpt() {

		$this->setModerationSettings();

		$can_show_excerpt = ! empty( $this->moderation_settings['display_options'] ) && in_array( 'excerpt', $this->moderation_settings['display_options'] );

		return apply_filters( 'rtcl_listing_can_show_excerpt', $can_show_excerpt, $this );
	}

	function can_show_price() {

		$this->setModerationSettings();
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_price = ! ( ( ( ! empty( $this->moderation_settings[ $display_option ] ) && ! in_array( 'price', $this->moderation_settings[ $display_option ] ) ) || Functions::is_price_disabled() || $this->get_pricing_type() === 'disabled' ) );

		return apply_filters( 'rtcl_listing_can_show_price', $can_show_price, $this );
	}

	public function can_show_ad_type() {
		$display_option = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';

		$can_show_type = Functions::get_option_item( 'rtcl_moderation_settings', $display_option, 'ad_type', 'multi_checkbox' );

		return apply_filters( 'rtcl_listing_can_show_ad_type', $can_show_type, $display_option, $this );
	}

	function has_thumbnail() {
		return ( has_post_thumbnail( $this->id ) || ! empty( $this->get_images() ) );
	}

	function get_view_counts() {
		return absint( get_post_meta( $this->id, '_views', true ) );
	}

	function get_label_class() {
		$class = [];
		if ( $this->is_featured() ) {
			$class[] = "is-featured";
		}

		if ( $this->is_new() ) {
			$class[] = "is-new";
		}

		if ( $this->type ) {
			$class[] = "is-" . $this->type;
		}

		return apply_filters( 'rtcl_get_listing_label_class', $class, $this );
	}

	function the_label_class() {
		$classes = $this->get_label_class();
		if ( ! empty( $classes ) ) {
			echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) );
		}
	}

	function get_ad_type() {
		return $this->type;
	}

	/**
	 * @return string | null
	 * @deprecated 1.5.56 please use get_ad_type()
	 */
	function get_type() {
		_deprecated_function( __METHOD__, '1.5.56', '$this->get_ad_type()' );

		return $this->get_ad_type();
	}

	function get_post_type() {
		return $this->listing->post_type;
	}

	/**
	 * @return WP_Post
	 */
	function get_post_object() {
		return $this->listing;
	}

	/**
	 * @return string
	 */
	function badges() {
		return Functions::get_template_html( "listing/badges", [ 'listing' => $this ] );
	}

	/**
	 *
	 */
	function the_badges() {
		echo $this->badges();
	}

	/**
	 * @param bool $echo
	 *
	 * @return string|void
	 * @deprecated
	 */
	function the_labels( $echo = true ) {
		_deprecated_function( __METHOD__, '2.0.0', '$this->the_badges()' );

		if ( $echo ) {
			$this->the_badges();

			return;
		}

		return $this->badges();
	}

	/**
	 * @param string $size
	 *
	 * @return null|string
	 */
	function get_the_thumbnail( $size = 'rtcl-thumbnail' ) {
		if ( has_post_thumbnail( $this->id ) ) {
			$thumb_id = get_post_thumbnail_id( $this->id );
		} else {
			$images = $this->get_images();
			if ( ! empty( $images ) ) {
				$images   = array_slice( $images, 0, 1 );
				$thumb_id = $images[0]->ID;
			} else {
				$thumb_id = Functions::get_option_item( 'rtcl_misc_settings', 'placeholder_image', null, 'number' );
			}
		}
		if ( $thumb_id ) {
			$image = wp_get_attachment_image( $thumb_id, $size, false, [
				"class" => "rtcl-thumbnail",
				"alt"   => esc_attr( get_the_title( $thumb_id ) )
			] );
		} else {
			$fallBackSizes = apply_filters( 'rtcl_default_placeholder_thumbnail_size', Functions::get_option_item( 'rtcl_misc_settings', 'image_size_thumbnail' ) );
			$image         = sprintf( '<img src="%s" class="rtcl-thumbnail rtcl-fallback-thumbnail" alt="%s" width="%d" height="%d">', esc_url( Functions::get_default_placeholder_url() ),
				esc_attr( $this->get_the_title() ),
				! empty( $fallBackSizes['width'] ) ? absint( $fallBackSizes['width'] ) : 150,
				! empty( $fallBackSizes['height'] ) ? absint( $fallBackSizes['height'] ) : 150
			);
		}

		return apply_filters( 'rtcl_listing_get_the_thumbnail', $image, $this->id );
	}

	/**
	 *
	 * @return null|string
	 */
	function get_the_thumbnail_id() {
		if ( has_post_thumbnail( $this->id ) ) {
			$thumb_id = get_post_thumbnail_id( $this->id );
		} else {
			$images = $this->get_images();
			if ( ! empty( $images ) ) {
				$images   = array_slice( $images, 0, 1 );
				$thumb_id = $images[0]->ID;
			} else {
				$thumb_id = Functions::get_option_item( 'rtcl_misc_settings', 'placeholder_image', null, 'number' );
			}
		}

		return apply_filters( 'rtcl_listing_get_the_thumbnail_id', $thumb_id, $this->id );
	}

	/**
	 * @param string $size
	 *
	 * @return null|string
	 */
	function get_the_thumbnail_url( $size = 'rtcl-thumbnail' ) {
		$thumb_id  = $this->get_the_thumbnail_id();
		$thumb_url = Functions::get_default_placeholder_url();
		if ( $thumb_id ) {
			$image = wp_get_attachment_image_src( $thumb_id, $size );
			if ( $image ) {
				[ $thumb_url ] = $image;
			}
		}

		return apply_filters( 'rtcl_listing_get_the_thumbnail_url', $thumb_url, $this->id );
	}

	/**
	 * @param string $size
	 */
	function the_thumbnail( $size = 'rtcl-thumbnail' ) {
		echo apply_filters( 'the_thumbnail', $this->get_the_thumbnail( $size ), $this->id );
	}

	/**
	 * @param bool $gmt
	 *
	 * @return string
	 */
	function get_the_time( $gmt = false ) {
		return sprintf( __( '%s ago', 'classified-listing' ), human_time_diff( get_post_time( 'U', $gmt, $this->listing, false ), current_time( 'timestamp', $gmt ) ) );
	}

	/**
	 * @param bool $gmt
	 *
	 */
	function the_time( $gmt = false ) {
		echo $this->get_the_time( $gmt );
	}


	public function get_author_id() {
		return (int) $this->listing->post_author;
	}

	function get_author_name() {
		$authorData  = get_user_by( 'id', $this->listing->post_author );
		$author_name = '';
		if ( is_object( $authorData ) ) {
			$author[] = $authorData->first_name;
			$author[] = $authorData->last_name;
			$author   = array_filter( $author );
			if ( ! empty( $author ) ) {
				$author_name = implode( ' ', $author );
			} else {
				$author_name = $authorData->display_name;
			}
		}

		return apply_filters( 'rtcl_listing_get_author_name', $author_name, $this );
	}

	function the_author() {
		echo apply_filters( 'rtcl_listing_the_author', $this->get_author_name(), $this );
	}

	function get_the_author_url() {
		return apply_filters( 'rtcl_listing_get_author_url', get_author_posts_url( $this->get_owner_id() ), $this );
	}

	// Added By Rashid.

	/**
	 * Print author logo.
	 *
	 * @return void
	 */
	function get_author_logo() {
		$avatar_attr = [
			'class'      => 'author-avatar',
			'extra_attr' => 'data-toggle="tooltip" data-placement="right" data-original-title="' . $this->get_author_name() . '"',
		];
		$pp_id       = absint( get_user_meta( $this->get_owner_id(), '_rtcl_pp_id', true ) );
		$author_logo = sprintf(
			$pp_id ? wp_get_attachment_image(
				$pp_id,
				[
					40,
					40,
				],
				'',
				[
					'class' => 'author-avatar',
					'title' => $this->get_author_name(),
				]
			) : get_avatar( $this->get_author_id(), 40, '', __( 'Author', 'classified-listing' ), $avatar_attr )
		);

		return apply_filters( 'rtcl_listing_get_author_logo', $author_logo );
	}
	// Added By Rashid.

	/**
	 * Print author logo.
	 *
	 * @return void
	 */
	function the_author_logo() {
		echo apply_filters( 'rtcl_listing_the_author_logo', $this->get_author_logo(), $this );
	}

	function the_meta() {
		Functions::get_template( "listing/meta", [ 'listing' => $this ] );
	}

	function the_excerpt() {
		echo apply_filters( 'rtcl_listing_the_excerpt', get_the_excerpt( $this->listing ) );
	}

	function get_the_content() {
		return apply_filters( 'rtcl_listing_get_the_content', apply_filters( 'the_content', $this->post_content ) );
	}

	function the_content() {
		echo $this->get_the_content();
	}

	/**
	 * @param bool $echo
	 * @param bool $link
	 * @param      $address
	 *
	 * @return string | void
	 */
	function the_locations( $echo = true, $link = false, $address = false ) {
		$html = '';

		if ( "geo" === Functions::location_type() ) {
			$getAddress = get_post_meta( $this->id, '_rtcl_geo_address', true );
			$html       .= sprintf( "<span class='loc-address'>%s</span>", esc_textarea( $getAddress ) );
		} else {
			if ( ! empty( $this->locations ) ) {
				$loc = [];
				foreach ( $this->locations as $location ) {
					if ( $link ) {
						$loc[] = sprintf( '<a href="%s">%s</a>',
							get_term_link( $location ),
							$location->name
						);
					} else {
						$loc[] = $location->name;
					}

				}
				$loc  = array_reverse( $loc );
				$html = implode( '<span class="rtcl-delimiter">,</span> ', $loc );
			}
			$getAddress = get_post_meta( $this->id, 'address', true );
			if ( $address && $getAddress ) {
				$html .= sprintf( "<span class='loc-address'>%s</span>", esc_textarea( $getAddress ) );
			}
		}

		if ( ! $echo ) {
			return $html;
		}
		echo $html;
	}

	/**
	 * @return array|\WP_Error
	 */
	function get_locations() {
		return $this->locations;
	}

	/**
	 * @param bool $echo
	 * @param bool $link
	 *
	 * @return string
	 */
	function the_categories( $echo = true, $link = false ) {
		$html = '';

		if ( ! empty( $this->categories ) ) {
			$loc = [];
			foreach ( $this->categories as $category ) {
				if ( $link ) {
					$loc[] = sprintf( '<a href="%s">%s</a>',
						get_term_link( $category ),
						$category->name
					);
				} else {
					$loc[] = $category->name;
				}
			}
			$html = implode( ', ', $loc );
		}

		if ( ! $echo ) {
			return $html;
		}
		echo $html;
	}

	/**
	 * @return array
	 */
	function get_categories() {
		if ( ! empty( $this->categories ) && ! is_wp_error( $this->categories ) ) {
			return $this->categories;
		}

		return [];
	}

	/**
	 * @return array
	 */
	function get_datetime() {
		return get_post_datetime( $this->po );
	}

	/**
	 * @return int
	 */
	function get_last_child_category_id() {
		$category = $this->get_last_child_category();

		return $category ? $category->term_id : 0;
	}

	/**
	 * @return \WP_Term|bool|mixed
	 */
	function get_last_child_category() {
		return ! empty( $this->categories ) && is_array( $this->categories ) ? end( $this->categories ) : false;
	}

	/**
	 * @return array
	 */
	function get_category_ids() {
		if ( ! empty( $this->categories ) ) {
			return wp_list_pluck( $this->categories, 'term_id' );
		}

		return [];
	}

	/**
	 * @return array
	 */
	function get_location_ids() {
		if ( ! empty( $this->locations ) ) {
			return wp_list_pluck( $this->locations, 'term_id' );
		}

		return [];
	}

	/**
	 * @return array
	 */
	public function get_promotions() {
		$promotions = [];

		$rawPromotions = Options::get_listing_promotions();
		if ( ! empty( $rawPromotions ) ) {
			foreach ( $rawPromotions as $promo_id => $promotion ) {
				if ( $this->hasPromotion( $promo_id ) ) {
					array_push( $promotions, $promo_id );
				}
			}
		}

		return $promotions;
	}

	/**
	 * @param $promo_id
	 *
	 * @return bool
	 */
	public function hasPromotion( $promo_id ) {
		return $promo_id && get_post_meta( $this->id, $promo_id, true );
	}


	/**
	 * @return mixed|void
	 */
	public function hasAnyPromotion() {
		$promotions = Options::get_listing_promotions();
		if ( ! empty( $rawPromotions ) ) {
			foreach ( $promotions as $promo_id => $promotion ) {
				if ( $this->hasPromotion( $promo_id ) ) {
					return apply_filters( 'rtcl_listing_has_any_promotion', true, $promo_id, $promotions, $this );
				}
			}
		}

		return apply_filters( 'rtcl_listing_has_any_promotion', false, null, $promotions, $this );
	}

	/**
	 * @return array
	 */
	function get_ancestors_category_ids_with_last_child() {
		$current_category = $this->get_current_selected_category();
		if ( $current_category ) {
			$parents = get_ancestors( $current_category->term_id, rtcl()->category, 'taxonomy' );
			array_unshift( $parents, $current_category->term_id );

			return $parents;
		}

		return [];
	}

	/**
	 * @return \WP_Term|null
	 */
	function get_current_selected_category() {
		$cats = $this->get_categories();
		if ( ! empty( $cats ) ) {
			return end( $cats );
		}

		return null;
	}

	/**
	 * @return array|bool|null|object|\WP_Error
	 */
	function get_parent_category() {
		$categories = $this->get_categories();
		if ( ! empty( $categories ) ) {
			$parent = get_ancestors( $categories[0]->term_id, rtcl()->category );
			if ( empty( $parent ) ) {
				$parent[] = $categories[0]->term_id;
			}
			$parent = array_pop( $parent );
			if ( $parent ) {
				return get_term( $parent, rtcl()->category );
			}
		}

		return false;
	}


	/**
	 * @return \WP_Term|null
	 */
	function get_parent_location() {
		$locations = $this->get_locations();
		if ( ! empty( $locations ) ) {
			$location = '';
			foreach ( $locations as $location_term ) {
				if ( $location_term->parent == 0 ) {
					$location = $location_term;
					break;
				}
			}

			return $location ? $location : $locations[0];
		}

		return null;
	}

	/**
	 * Get the total amount (COUNT) of ratings, or just the count for one rating e.g. number of 5 star ratings.
	 *
	 * @param int $value Optional. Rating value to get the count for. By default returns the count of all rating
	 *                    values.
	 *
	 * @return int
	 */
	public function get_rating_count( $value = null ) {
		$counts = $this->get_rating_counts();

		if ( is_null( $value ) ) {
			return array_sum( $counts );
		} elseif ( isset( $counts[ $value ] ) ) {
			return absint( $counts[ $value ] );
		} else {
			return 0;
		}
	}


	/**
	 * Get rating count.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array of counts
	 */
	public function get_rating_counts( $context = 'view' ) {
		return $this->get_prop( 'rating_counts', $context );
	}

	/**
	 * Get average rating.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return float
	 */
	public function get_average_rating( $context = 'view' ) {
		return $this->get_prop( 'average_rating', $context );
	}

	/**
	 * Get review count.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return int
	 */
	public function get_review_count( $context = 'view' ) {
		return $this->get_prop( 'review_count', $context );
	}

	function get_price() {
		return apply_filters( 'rtcl_min_price_raw', get_post_meta( $this->id, 'price', true ) );
	}

	function get_max_price() {
		return apply_filters( 'rtcl_max_price_raw', get_post_meta( $this->id, '_rtcl_max_price', true ) );
	}

	/**
	 * @return string
	 * @deprecated
	 */
	function get_the_price() {
		_deprecated_function( __METHOD__, '2.0.0', '$this->get_formatted_price()' );

		return $this->get_formatted_price();
	}

	/**
	 * @return string
	 */
	function get_formatted_price() {
		$formatted_price = Functions::price( $this->get_price(), true );

		return apply_filters( 'rtcl_listing_get_formatted_price', $formatted_price, $this );
	}


	/**
	 * Returns the price in html format.Helpers/Functions.php
	 *
	 * @return string
	 */
	public function get_price_html() {

		$min_price = $this->get_price();
		$max_price = $this->get_max_price();
		if ( '' !== $min_price ) {
			if ( $this->get_pricing_type() === 'range' && '' !== $max_price && $min_price !== $max_price ) {
				$price = Functions::format_price_range( $min_price, $max_price, [ 'listing' => $this ] );
			} else {
				$price = Functions::price( $min_price, false, [ 'listing' => $this ] );
			}

			$is_free = 0 === $min_price && 0 === $max_price;

			if ( $is_free ) {
				$price = apply_filters( 'rtcl_free_price_html', __( 'Free!', 'classified-listing' ), $this );
			} else {
				$price = apply_filters( 'rtcl_price_html', $price . $this->get_price_suffix(), $this );
			}
		} else {
			$price = apply_filters( 'rtcl_empty_price_html', '', $this );
		}
		$price             = apply_filters( 'rtcl_price_html_before_meta', $price, $this );
		$price_meta_html   = '';
		$price_meta_html   = apply_filters( 'rtcl_price_meta_html', $price_meta_html, $price, $this );
		$price_meta_html   = $price_meta_html ? apply_filters( 'rtcl_price_meta_wrap_html', sprintf( '<span class="rtcl-price-meta">%s</span>', $price_meta_html ), $price_meta_html, $price, $this ) : null;
		$price_html_format = apply_filters( 'rtcl_get_price_html_format', '<div class="rtcl-price price-type-%1$s">%2$s%3$s</div>' );
		$price_html        = sprintf( $price_html_format, $this->get_price_type(), $price, $price_meta_html );

		return apply_filters( 'rtcl_get_price_html', $price_html, $this );
	}


	/**
	 * Get the suffix to display after prices > 0.
	 *
	 * @param string $price to calculate, left blank to just use get_price().
	 *
	 * @return string
	 */
	public function get_price_suffix( $price = '' ) {
		return $html = '';

		$suffix = get_option( 'woocommerce_price_display_suffix' );
		if ( $suffix && wc_tax_enabled() && 'taxable' === $this->get_tax_status() ) {
			if ( '' === $price ) {
				$price = $this->get_price();
			}
			$replacements = [
				'{price_including_tax}' => wc_price( wc_get_price_including_tax( $this, [
					'qty'   => $qty,
					'price' => $price
				] ) ),
				// @phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine, WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				'{price_excluding_tax}' => wc_price( wc_get_price_excluding_tax( $this, [
					'qty'   => $qty,
					'price' => $price
				] ) ),
				// @phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
			];
			$html         = str_replace( array_keys( $replacements ), array_values( $replacements ), ' <small class="woocommerce-price-suffix">' . wp_kses_post( $suffix ) . '</small>' );
		}

		return apply_filters( 'rtcl_get_price_suffix', $html, $this, $price );
	}

	/**
	 * @return mixed|void
	 */
	function the_price() {
		echo apply_filters( 'rtcl_listing_the_price', $this->get_formatted_price(), $this->id );
	}

	public function get_images() {
		if ( null === $this->images ) {
			$this->images = Functions::get_listing_images( $this->id );
		}

		return $this->images;
	}

	public function the_gallery() {
		if ( ! Functions::is_gallery_disabled() ) {
			$video_urls = [];
			if ( ! Functions::is_video_urls_disabled() && ! apply_filters( 'rtcl_disable_gallery_video', Functions::is_video_gallery_disabled() ) ) {
				$video_urls = get_post_meta( $this->get_id(), '_rtcl_video_urls', true );
				$video_urls = ! empty( $video_urls ) && is_array( $video_urls ) ? $video_urls : [];
			}
			Functions::get_template( "listing/gallery", [ 'images' => $this->get_images(), 'videos' => $video_urls ] );
		}
	}

	function the_custom_fields() {
		// Get custom fields
		$custom_field_ids = Functions::get_custom_field_ids( $this->get_last_child_category_id() );

		$fields = [];
		if ( ! empty( $custom_field_ids ) ) {
			$args = [
				'post_type'           => rtcl()->post_type_cf,
				'post_status'         => 'publish',
				'posts_per_page'      => - 1,
				'post__in'            => $custom_field_ids,
				'orderby'             => 'post__in',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'suppress_filters'    => false
			];

			$result = new WP_Query( $args );
			$fields = $result->posts;
		}
		Functions::get_template( "listing/custom-fields", [
			'fields'     => $fields,
			'listing_id' => $this->id
		] );
	}


	function the_actions() {
		$the_actions = [
			'can_add_favourites' => Functions::get_option_item( 'rtcl_moderation_settings', 'has_favourites', '', 'checkbox' ) ? true : false,
			'can_report_abuse'   => Functions::get_option_item( 'rtcl_moderation_settings', 'has_report_abuse', '', 'checkbox' ) ? true : false,
			'social'             => $this->the_social_share( false ),
			'listing_id'         => $this->id
		];
		$ths_actions = apply_filters( 'rtcl_listing_the_actions', $the_actions );
		Functions::get_template( "listing/actions", $ths_actions );
	}

	/**
	 *
	 * @return null|string
	 */
	function get_the_social_share() {
		$this->setMiscSettings();
		$html = '';
		if ( ! empty( $this->misc_settings['social_services'] ) ) {
			$social_share = apply_filters( 'rtcl_social_share_data', [
				'misc_settings' => $this->misc_settings,
				'title'         => $this->get_the_title(),
				'url'           => rawurldecode( $this->get_the_permalink() ),
				'thumbnail'     => $this->get_the_thumbnail_url()
			] );
			$html         = Functions::get_template_html( "listing/social-share", $social_share );
		}

		return $html;
	}

	/**
	 * @param bool $echo
	 *
	 * @return null|string
	 */
	function the_social_share( $echo = true ) {
		global $post;
		$html = null;

		$this->setMiscSettings();
		$this->setPageSettings();
		$page = 'none';

		$is_single_listing = rtcl()->post_type == $post->post_type;
		if ( apply_filters( 'rtcl_listing_is_social_share_for_single', $is_single_listing, $post ) ) {
			$page = 'listing';
		}

		if ( $post->ID == $this->page_settings['listings'] ) {
			$page = 'listings';
		}

		if ( ! empty( $this->misc_settings['social_pages'] ) && in_array( $page, $this->misc_settings['social_pages'] ) ) {

			// Get current page URL
			$url = get_permalink( $post );// Link::get_current_url();

			// Get current page title
			$title = get_the_title();

			if ( get_query_var( 'rtcl_location' ) || get_query_var( 'rtcl_category' ) ) {

				$title = Functions::get_single_term_title();

			}

			$title = str_replace( ' ', '%20', $title );

			// Get Post Thumbnail
			$thumbnail = '';

			if ( 'listing' == $page ) {
				$images = get_post_meta( $post->ID, 'images', true );

				if ( ! empty( $images ) ) {
					$image_attributes = wp_get_attachment_image_src( $images[0], 'full' );
					$thumbnail        = is_array( $image_attributes ) ? $image_attributes[0] : '';
				}
			} else {
				$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$thumbnail        = is_array( $image_attributes ) ? $image_attributes[0] : '';
			}
			if ( ! empty( $this->misc_settings['social_services'] ) ) {
				$html = Functions::get_template_html( "listing/social-share", [
					'misc_settings' => $this->misc_settings,
					'title'         => $title,
					'url'           => rawurldecode( $url ),
					'thumbnail'     => $thumbnail
				] );
			}
		}
		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}


	function the_related_listings() {

		$this->setGeneralSettings();

		$category              = ! empty( $this->categories ) ? end( $this->categories )->term_id : 0;
		$related_post_per_page = apply_filters( 'rtcl_listing_related_posts_per_page', Functions::get_option_item( 'rtcl_general_settings', 'related_posts_per_page', 4, 'number' ) );
		if ( ! $related_post_per_page ) {
			return;
		}
		$query_args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $related_post_per_page,
			'post__not_in'   => [ $this->id ]
		];
		if ( $category ) {
			$this->setGeneralSettings();

			$query_args['tax_query'] = [
				[
					'taxonomy'         => rtcl()->category,
					'field'            => 'term_id',
					'terms'            => $category,
					'include_children' => isset( $this->general_settings['include_results_from'] ) && in_array( 'child_categories',
						$this->general_settings['include_results_from'] ) ? true : false
				]
			];
		}
		$rtcl_related_query = new \WP_Query( apply_filters( 'rtcl_related_listing_query_arg', $query_args ) );
		$slider_options     = apply_filters( 'rtcl_related_slider_options', [
			"rtl"          => is_rtl(),
			"spaceBetween" => 15,
			"breakpoints"  => [
				0   => [
					"slidesPerView" => 1
				],
				576 => [
					"slidesPerView" => 2
				],
				768 => [
					"slidesPerView" => 4
				]
			]
		] );
		/** Added by Rashid */
		$data = [
			'template'              => 'listing/related-listings',
			'rtcl_related_query'    => $rtcl_related_query,
			'slider_options'        => $slider_options,
			'default_template_path' => null
		];
		$data = apply_filters( 'rtcl_related_listings_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		/** Added by Rashid */
		// Functions::get_template($data['path'], compact($data['variable']));
	}

	function user_contact_location_at_single() {
		$locations     = [];
		$location_type = Functions::location_type();
		if ( 'local' === $location_type ) {
			$is_location = apply_filters( 'rtcl_display_location_details_page', Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'location', 'multi_checkbox' ) ); // Hook Added by rashid
			if ( count( $this->locations ) && $is_location ) {
				foreach ( $this->locations as $location ) {
					$locations[] = $location->name;
				}
				$locations = array_reverse( $locations );
			}

			$address    = get_post_meta( $this->id, 'address', true );
			$zipcode    = get_post_meta( $this->id, 'zipcode', true );
			$is_address = apply_filters( 'rtcl_display_address_details_page', Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'address', 'multi_checkbox' ) ); // Hook Added by rashid

			if ( $address && $is_address ) {
				array_unshift( $locations, $address );
			}
			$is_zipcode = apply_filters( 'rtcl_display_zipcode_details_page', Functions::get_option_item( 'rtcl_moderation_settings', 'display_options_detail', 'zipcode', 'multi_checkbox' ) ); // Hook Added by rashid

			if ( $zipcode && $is_zipcode ) {
				$locations[] = $zipcode;
			}
		} else if ( 'geo' === $location_type ) {
			$locations[] = get_post_meta( $this->id, '_rtcl_geo_address', true );
		}


		return apply_filters( 'rtcl_user_contact_location_at_single', $locations, $this, $location_type );
	}

	function the_user_info() {

		$phone           = get_post_meta( $this->id, 'phone', true );
		$whatsapp_number = get_post_meta( $this->id, '_rtcl_whatsapp_number', true );
		$email           = get_post_meta( $this->id, 'email', true );
		$website         = get_post_meta( $this->id, 'website', true );

		Functions::get_template( "listing/user-information", [
			'listing'              => $this,
			'locations'            => $this->user_contact_location_at_single(),
			'phone'                => $phone,
			'whatsapp_number'      => $whatsapp_number,
			'email'                => $email,
			'has_contact_form'     => Functions::get_option_item( 'rtcl_moderation_settings', 'has_contact_form', false, 'checkbox' ),
			'website'              => $website,
			'listing_id'           => $this->id,
			'email_to_seller_form' => $this->email_to_seller_form( false )
		] );
	}

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	function email_to_seller_form( $echo = true ) {
		if ( $echo ) {
			Functions::get_template( "listing/email-to-seller-form" );
		} else {
			return Functions::get_template_html( "listing/email-to-seller-form" );
		}
	}

	public function the_map() {

	}

	public function setGeneralSettings() {
		if ( ! empty( $this->general_settings ) ) {
			$this->general_settings = Functions::get_option( 'rtcl_general_settings' );
		}
	}

	public function setModerationSettings() {
		if ( empty( $this->moderation_settings ) ) {
			$this->moderation_settings = Functions::get_option( 'rtcl_moderation_settings' );
		}
	}

	public function setMiscSettings() {
		if ( empty( $this->misc_settings ) ) {
			$this->misc_settings = Functions::get_option( 'rtcl_misc_settings' );
		}
	}

	public function setPageSettings() {
		if ( empty( $this->page_settings ) ) {
			$this->page_settings = Functions::get_page_ids();
		}
	}

	public function setSettings() {
		$this->setGeneralSettings();
		$this->setModerationSettings();
		$this->setMiscSettings();
		$this->setPageSettings();
	}

}