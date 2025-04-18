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
  */
 function custom_post_loop_navigation_shortcode() {
     global $post;
 
     if (!is_singular()) return '';
 
     // Get the current post type
     $post_type = get_post_type($post);
 
     // Get the categories (you can change 'category' to custom taxonomy if needed)
     $categories = wp_get_post_terms($post->ID, 'category');
 
     if (empty($categories)) {
         return ''; // No category found
     }
 
     // Use the first category
     $category_id = $categories[0]->term_id;
 
     // Get all posts in this category with the correct custom order
     $args = array(
         'post_type'      => $post_type,
         'posts_per_page' => -1,
         'orderby'        => 'menu_order',
         'order'          => 'ASC',
         'post_status'    => 'publish',
         'tax_query'      => array(
             array(
                 'taxonomy' => 'category', // change if needed
                 'field'    => 'term_id',
                 'terms'    => $category_id,
             ),
         ),
         'fields'         => 'ids',
     );
 
     $post_ids = get_posts($args);
 
     if (count($post_ids) < 2) {
         return ''; // No need to show navigation
     }
 
     $current_index = array_search($post->ID, $post_ids);
     $total_posts   = count($post_ids);
 
     // Loop logic
     $prev_index = ($current_index - 1 + $total_posts) % $total_posts;
     $next_index = ($current_index + 1) % $total_posts;
 
     $prev_post = get_post($post_ids[$prev_index]);
     $next_post = get_post($post_ids[$next_index]);
 
     $output  = '<div class="custom-loop-nav" style="margin-top:20px;text-align:center;">';
     $output .= '<a href="' . esc_url(get_permalink($prev_post)) . '">← PREV</a> | ';
     $output .= '<a href="' . esc_url(get_permalink($next_post)) . '">NEXT →</a>';
     $output .= '</div>';
 
     return $output;
 }
 add_shortcode('custom_post_loop_navigation', 'custom_post_loop_navigation_shortcode');
