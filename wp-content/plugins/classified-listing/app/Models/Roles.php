<?php

namespace Rtcl\Models;

use WP_Roles;

class Roles
{

    /**
     * Create roles and capabilities.
     */
    public static function create_roles() {
        global $wp_roles;

        if (!class_exists('\WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        // Dummy gettext calls to get strings in the catalog.
        _x('Listing manager', 'User role', 'classified-listing');


        // Shop manager role.
        add_role(
            'rtcl_manager',
            'Listing Manager',
            array(
                'level_9'                => true,
                'level_8'                => true,
                'level_7'                => true,
                'level_6'                => true,
                'level_5'                => true,
                'level_4'                => true,
                'level_3'                => true,
                'level_2'                => true,
                'level_1'                => true,
                'level_0'                => true,
                'read'                   => true,
                'read_private_pages'     => true,
                'read_private_posts'     => true,
                'edit_posts'             => true,
                'edit_pages'             => true,
                'edit_published_posts'   => true,
                'edit_published_pages'   => true,
                'edit_private_pages'     => true,
                'edit_private_posts'     => true,
                'edit_others_posts'      => true,
                'edit_others_pages'      => true,
                'publish_posts'          => true,
                'publish_pages'          => true,
                'delete_posts'           => true,
                'delete_pages'           => true,
                'delete_private_pages'   => true,
                'delete_private_posts'   => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'delete_others_posts'    => true,
                'delete_others_pages'    => true,
                'manage_categories'      => true,
                'manage_links'           => true,
                'moderate_comments'      => true,
                'upload_files'           => true,
                'export'                 => true,
                'import'                 => true,
                'list_users'             => true,
                'edit_theme_options'     => true,
            )
        );

        $capabilities = self::get_core_caps();

        foreach ($capabilities as $cap_group) {
            foreach ($cap_group as $cap) {
                $wp_roles->add_cap('rtcl_manager', $cap);
                $wp_roles->add_cap('administrator', $cap);
            }
        }
    }

    /**
     * Remove Classified Listing roles.
     */
    public static function remove_roles() {
        global $wp_roles;

        if (!class_exists('\WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        $capabilities = self::get_core_caps();

        foreach ($capabilities as $cap_group) {
            foreach ($cap_group as $cap) {
                $wp_roles->remove_cap('rtcl_manager', $cap);
                $wp_roles->remove_cap('administrator', $cap);
            }
        }

        remove_role('rtcl_manager');
    }

    public static function add_default_caps() {
        global $wp_roles;

        if (class_exists('\WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new \WP_Roles();
            }
        }

        if (is_object($wp_roles)) {

            // Add the "administrator" capabilities
            $capabilities = self::get_core_caps();
            foreach ($capabilities as $cap_group) {
                foreach ($cap_group as $cap) {
                    $wp_roles->add_cap('administrator', $cap);
                }
            }

            // Add Default caps
            $role_caps = self::get_roles_default_caps();

            foreach ($role_caps as $role => $caps) {
                if (is_array($caps) && !empty($caps)) {
                    foreach ($caps as $cap) {
                        $wp_roles->add_cap($role, $cap);
                    }
                }
                // Add extra role caps with specific role
                do_action('rtcl_roles_add_default_caps_' . $role, $wp_roles, rtcl()->post_type);
            }

            // Add extra work when rtcl role is
            do_action('rtcl_roles_add_default_caps', $wp_roles, rtcl()->post_type);
        }
    }

    /**
     * @param array | string $roles
     */
    public static function add_core_caps($roles) {
        if (empty($roles)) {
            return;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        global $wp_roles;

        if (class_exists('\WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new \WP_Roles();
            }
        }

        if (is_object($wp_roles)) {
            foreach ($roles as $role) {
                $capabilities = self::get_core_caps();
                foreach ($capabilities as $cap_group) {
                    foreach ($cap_group as $cap) {
                        $wp_roles->add_cap($role, $cap);
                    }
                }
            }
        }
    }

    public static function remove_code_caps($roles) {
        if (empty($roles)) {
            return;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        global $wp_roles;

        if (class_exists('\WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new \WP_Roles();
            }
        }

        if (is_object($wp_roles)) {
            foreach ($roles as $role) {
                $capabilities = self::get_core_caps();
                foreach ($capabilities as $cap_group) {
                    foreach ($cap_group as $cap) {
                        $wp_roles->remove_cap($role, $cap);
                    }
                }
            }
        }
    }

    public static function get_core_caps() {

        $capabilities = array();
        $capabilities['core'] = ['manage_rtcl_options', 'manage_rtcl_reports', 'manage_rtcl_store'];
        $capability_types = ['rtcl_listing', 'rtcl_pricing', 'rtcl_payment'];

        foreach ($capability_types as $capability_type) {

            $capabilities[$capability_type] = array(
                "add_{$capability_type}",
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",
            );
        }

        return apply_filters('rtcl_roles_get_core_caps', $capabilities);
    }

    public static function get_roles_default_caps() {

        $caps = [
            'editor'      => [
                'add_' . rtcl()->post_type,
                'edit_' . rtcl()->post_type . 's',
                'edit_others_' . rtcl()->post_type . 's',
                'publish_' . rtcl()->post_type . 's',
                'read_private_' . rtcl()->post_type . 's',
                'delete_' . rtcl()->post_type . 's',
                'delete_private_' . rtcl()->post_type . 's',
                'delete_published_' . rtcl()->post_type . 's',
                'delete_others_' . rtcl()->post_type . 's',
                'edit_private_' . rtcl()->post_type . 's',
                'edit_published_' . rtcl()->post_type . 's',
            ],
            'author'      => [
                'add_' . rtcl()->post_type,
                'edit_' . rtcl()->post_type . 's',
                'publish_' . rtcl()->post_type . 's',
                'delete_' . rtcl()->post_type . 's',
                'delete_published_' . rtcl()->post_type . 's',
                'edit_published_' . rtcl()->post_type . 's',
            ],
            'contributor' => [
                'add_' . rtcl()->post_type,
                'edit_' . rtcl()->post_type . 's',
                'publish_' . rtcl()->post_type . 's',
                'delete_' . rtcl()->post_type . 's',
                'delete_published_' . rtcl()->post_type . 's',
                'edit_published_' . rtcl()->post_type . 's',
            ],
            'subscriber'  => [
                'add_' . rtcl()->post_type,
                'edit_' . rtcl()->post_type . 's',
                'publish_' . rtcl()->post_type . 's',
                'delete_' . rtcl()->post_type . 's',
                'delete_published_' . rtcl()->post_type . 's',
                'edit_published_' . rtcl()->post_type . 's',
            ]
        ];

        return apply_filters('rtcl_roles_get_roles_default_caps', $caps);
    }

    public static function get_default_caps() {
        $caps = [
            'add_' . rtcl()->post_type,
            'edit_' . rtcl()->post_type . 's',
            'publish_' . rtcl()->post_type . 's',
            'delete_' . rtcl()->post_type . 's',
            'delete_published_' . rtcl()->post_type . 's',
            'edit_published_' . rtcl()->post_type . 's',
        ];

        return apply_filters('rtcl_roles_get_default_caps', $caps);
    }

    /**
     * @param array | string $roles
     */
    public static function add_custom_caps($roles) {
        if (empty($roles)) {
            return;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        global $wp_roles;

        if (class_exists('\WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new \WP_Roles();
            }
        }

        if (is_object($wp_roles)) {
            $caps = self::get_default_caps();
            foreach ($roles as $role) {
                if (is_array($caps) && !empty($caps)) {
                    foreach ($caps as $cap) {
                        $wp_roles->add_cap($role, $cap);
                    }
                }
            }
        }

    }


    /**
     * @param array | string $roles
     */
    public static function remove_custom_caps($roles) {
        if (!$roles) {
            return;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        global $wp_roles;

        if (class_exists('\WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new \WP_Roles();
            }
        }

        if (is_object($wp_roles)) {
            $caps = self::get_default_caps();
            foreach ($roles as $role) {
                if (is_array($caps) && !empty($caps)) {
                    foreach ($caps as $cap) {
                        $wp_roles->remove_cap($role, $cap);
                    }
                }
            }
        }

    }

    public static function meta_caps($caps, $cap, $user_id, $args) {

        // If editing, deleting, or reading a listing, get the post and post type object.
        if ('edit_' . rtcl()->post_type == $cap || 'delete_' . rtcl()->post_type == $cap || 'read_' . rtcl()->post_type == $cap) {
            $post = get_post($args[0]);
            $post_type = get_post_type_object($post->post_type);

            // Set an empty array for the caps.
            $caps = array();
        }

        // If editing a listing, assign the required capability.
        if ('edit_' . rtcl()->post_type == $cap) {
            if ($user_id == $post->post_author) {
                $caps[] = $post_type->cap->edit_listings;
            } else {
                $caps[] = $post_type->cap->edit_others_listings;
            }
        } // If deleting a listing, assign the required capability.
        else if ('delete_' . rtcl()->post_type == $cap) {
            if ($user_id == $post->post_author) {
                $caps[] = $post_type->cap->delete_listings;
            } else {
                $caps[] = $post_type->cap->delete_others_listings;
            }
        } // If reading a private listing, assign the required capability.
        else if ('read_' . rtcl()->post_type == $cap) {
            if ('private' != $post->post_status) {
                $caps[] = 'read';
            } elseif ($user_id == $post->post_author) {
                $caps[] = 'read';
            } else {
                $caps[] = $post_type->cap->read_private_listings;
            }
        }

        // Return the capabilities required by the user.
        return $caps;

    }

    public static function remove_default_caps() {

        global $wp_roles;

        if (class_exists('WP_Roles')) {
            if (!isset($wp_roles)) {
                $wp_roles = new \WP_Roles();
            }
        }

        if (is_object($wp_roles)) {

            // Remove the "administrator" Capabilities
            $capabilities = self::get_core_caps();

            foreach ($capabilities as $cap_group) {
                foreach ($cap_group as $cap) {
                    $wp_roles->remove_cap('administrator', $cap);
                }
            }

            // Remove Default caps
            $role_caps = self::get_roles_default_caps();

            foreach ($role_caps as $role => $caps) {
                if (is_array($caps) && !empty($caps)) {
                    foreach ($caps as $cap) {
                        $wp_roles->remove_cap($role, $cap);
                    }
                }
                // Remove extra role caps with specific role
                do_action('rtcl_roles_remove_default_caps_' . $role, $wp_roles, rtcl()->post_type);
            }

            // Remove extra work when rtcl role is remove cap
            do_action('rtcl_roles_remove_default_caps', $wp_roles, rtcl()->post_type);
        }
    }
}
