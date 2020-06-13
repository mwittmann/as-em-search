<?php
/**
 * AS EM Search
 *
 * Adds a search form for bookings and events
 *
 * @package   AS_EM_Search
 * @author    KoenG
 * @link      http://www.appsaloon.be
 * @copyright 2014 AppSaloon
 *
 * @wordpress-plugin
 * Plugin Name:       AS EM Search
 * Description:       Makes it possible to search in Events Manager bookings and events
 * Version:           1.1.3
 * Author:            KoenG
 * Text Domain:       as-em-search
 * Domain Path:       /languages
 * Author URI:        http://www.appsaloon.be
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

add_filter('em_create_events_submenu', 'as_em_search_admin_menu');

function as_em_search_admin_menu($plugin_pages){
    // first check if current user has custom capability 'as_em_search'
    if (current_user_can( 'as_em_search' )) {
        $plugin_pages['search_form'] =
        add_submenu_page(
            'edit.php?post_type='.EM_POST_TYPE_EVENT,
            __('Search bookings','as-em-search'),
            __('Search bookings','as-em-search'),
            'as_em_search',
            "events-manager-search-form",
            'as_em_search_show_form'
        );
        return $plugin_pages;
    }
    // fall back to the default 'list_users' capability
    $plugin_pages['search_form'] =
        add_submenu_page(
            'edit.php?post_type='.EM_POST_TYPE_EVENT,
            __('Search bookings','as-em-search'),
            __('Search bookings','as-em-search'),
            'list_users',
            "events-manager-search-form",
            'as_em_search_show_form'
        );
    return $plugin_pages;
}

function as_em_search_show_form(){
    include_once __DIR__ . '/search-form.php';
}

function as_em_search( $search_term ) {
    global $wpdb;
    $table_name = EM_BOOKINGS_TABLE;
    $events_tbl = EM_EVENTS_TABLE;
    $blog_id = get_current_blog_id();
    $sql = <<<EOD
SELECT b.booking_id, b.event_id, b.booking_spaces, booking_status, b.booking_meta, b.booking_date 
    FROM $table_name b
    INNER JOIN $events_tbl e on b.event_id = e.event_id
    WHERE b.booking_meta LIKE %s 
    AND e.blog_id = %d
    ORDER BY e.event_start desc, b.event_id desc, b.booking_date desc
    LIMIT 101
EOD;
    $query = $wpdb->prepare($sql, '%' . $search_term . '%', $blog_id);

    return $wpdb->get_results($query) ;
}

function as_em_get_event_info( $event_id ) {
    global $wpdb;

    $query = $wpdb->prepare('SELECT event_name, event_start_date, event_start_time, event_start FROM ' . EM_EVENTS_TABLE . ' WHERE event_id = %d', $event_id);
    $event = $wpdb->get_row($query, ARRAY_A);

    return $event;
}

function as_em_search_load_plugin_textdomain() {
    load_plugin_textdomain( 'as-em-search', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'as_em_search_load_plugin_textdomain' );