<?php
/**
 * Plugin Name: Custom Post Loop Navigation
 * Description: Adds a [custom_post_loop_navigation] shortcode that outputs Prev | Next navigation for custom post types within the same category in a loop.
 * Plugin URI: https://github.com/lajumia/speedpress
 * Version: 1.0.0
 * Author: Md Laju Miah
 * Author URI: https://www.upwork.com/freelancers/speedoptimizationspecialist
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


if (!defined('ABSPATH')) exit;

/**
 * Shortcode: [custom_post_loop_navigation]
 * Displays Prev | Next navigation for custom post types, within same category, in loop.
 */
function custom_post_loop_navigation_shortcode() {
    global $post;

    // Set your custom post type
    $post_type = get_post_type($post);

    // Get categories (you can change 'category' to custom taxonomy if needed)
    $categories = wp_get_post_terms($post->ID, 'category');

    if (empty($categories)) {
        return ''; // No category, no navigation
    }

    $category_id = $categories[0]->term_id;

    // Get all posts in this category in custom order
    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'category', // Change this to your custom taxonomy if needed
                'field'    => 'term_id',
                'terms'    => $category_id,
            ],
        ],
        'fields' => 'ids',
    ];

    $posts = get_posts($args);

    if (empty($posts) || count($posts) < 2) {
        return ''; // No navigation needed
    }

    $current_index = array_search($post->ID, $posts);
    $total_posts   = count($posts);

    // Loop logic
    $prev_index = ($current_index - 1 + $total_posts) % $total_posts;
    $next_index = ($current_index + 1) % $total_posts;

    $prev_post = get_post($posts[$prev_index]);
    $next_post = get_post($posts[$next_index]);

    // Output navigation
    $output  = '<div class="custom-loop-nav">';
    $output .= '<a href="' . esc_url(get_permalink($prev_post)) . '">← PREV</a> | ';
    $output .= '<a href="' . esc_url(get_permalink($next_post)) . '">NEXT →</a>';
    $output .= '</div>';

    return $output;
}
add_shortcode('custom_post_loop_navigation', 'custom_post_loop_navigation_shortcode');
