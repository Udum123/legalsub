<?php

namespace Rtcl\Models\DataStores;

use Exception;
use Rtcl\Controllers\Hooks\Comments;
use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;

class ListingDataStoreCPT extends DataStoreWP {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @since 1.0.0
     * @var array
     */
    protected $internal_meta_keys = array(
        '_rtcl_rating_count',
        '_rtcl_average_rating',
        '_rtcl_review_count'
    );

    /**
     * If we have already saved our extra data, don't do automatic / default handling.
     *
     * @var bool
     */
    protected $extra_data_saved = false;

    /**
     * Stores updated props.
     *
     * @var array
     */
    protected $updated_props = array();

    /*
    |--------------------------------------------------------------------------
    | CRUD Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Method to read a listing from the database.
     *
     * @param Listing $listing Product object.
     *
     * @throws \ReflectionException*@throws Exception
     * @throws Exception
     */
    public function read( &$listing ) {
        $listing->set_defaults();
        $post_object = get_post( $listing->get_id() );

        if ( ! $listing->exists() ) {
            throw new Exception( __( 'Invalid listing.', 'classified-listing' ) );
        }

        $listing->set_props(
            array(
                'name'              => $post_object->post_title,
                'slug'              => $post_object->post_name,
                'date_created'      => 0 < $post_object->post_date_gmt ? Functions::string_to_timestamp( $post_object->post_date_gmt ) : null,
                'date_modified'     => 0 < $post_object->post_modified_gmt ? Functions::string_to_timestamp( $post_object->post_modified_gmt ) : null,
                'status'            => $post_object->post_status,
                'description'       => $post_object->post_content,
                'short_description' => $post_object->post_excerpt,
                'parent_id'         => $post_object->post_parent,
                'menu_order'        => $post_object->menu_order,
                'reviews_allowed'   => 'open' === $post_object->comment_status,
            )
        );

        $this->read_listing_data( $listing );
        $listing->set_object_read( true );
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read product data. Can be overridden by child classes to load other props.
     *
     * @param Listing $listing Product object.
     * @since 1.0.0
     */
    protected function read_listing_data( &$listing ) {
        $id             = $listing->get_id();
        $review_count   = get_post_meta( $id, '_rtcl_review_count', true );
        $rating_counts  = get_post_meta( $id, '_rtcl_rating_count', true );
        $average_rating = get_post_meta( $id, '_rtcl_average_rating', true );

        if ( '' === $review_count ) {
            Comments::get_review_count_for_listing( $listing );
        } else {
            $listing->set_review_count( $review_count );
        }

        if ( '' === $rating_counts ) {
            Comments::get_rating_counts_for_listing( $listing );
        } else {
            $listing->set_rating_counts( $rating_counts );
        }

        if ( '' === $average_rating ) {
            Comments::get_average_rating_for_listing( $listing );
        } else {
            $listing->set_average_rating( $average_rating );
        }
    }

    /**
     * Clear any caches.
     *
     * @param Listing $listing Product object.
     * @since 1.0.0
     */
    protected function clear_caches(&$listing) // TODO Need to set this
    {
//        delete_listing_transients($listing->get_id());
//        CacheHelper::incr_cache_prefix('listing_' . $listing->get_id());
    }

    /**
     * Update a listings average rating meta.
     *
     * @since 1.0.0
     * @param Listing $listing Product object.
     */
    public function update_average_rating($listing)
    {
        update_post_meta($listing->get_id(), '_rtcl_average_rating', $listing->get_average_rating('edit'));
        self::update_visibility($listing, true);
    }

    /**
     * Update a listings review count meta.
     *
     * @since 1.0.0
     * @param Listing $listing Product object.
     */
    public function update_review_count($listing)
    {
        update_post_meta($listing->get_id(), '_rtcl_review_count', $listing->get_review_count('edit'));
    }

    /**
     * Update a listings rating counts.
     *
     * @since 1.0.0
     * @param Listing $listing Product object.
     */
    public function update_rating_counts($listing)
    {
        update_post_meta($listing->get_id(), '_rtcl_rating_count', $listing->get_rating_counts('edit'));
    }


    /**
     * Update visibility terms based on props.
     *
     * @since 1.0.0
     *
     * @param Listing $listing Product object.
     * @param bool $force Force update. Used during create.
     */
    protected function update_visibility(&$listing, $force = false)
    {
        $changes = $listing->get_changes();

        if ($force || array_intersect(array( 'average_rating'), array_keys($changes))) {
            $terms = array();

            $rating = min(5, round($listing->get_average_rating(), 0));

            if ($rating > 0) {
                $terms[] = 'rated-' . $rating;
            }

            if (!is_wp_error(wp_set_post_terms($listing->get_id(), $terms, 'listing_visibility', false))) {
                delete_transient('rtcl_featured_listings');
                do_action('rtcl_listing_set_visibility', $listing->get_id(), $listing->get_catalog_visibility());
            }
        }
    }
}