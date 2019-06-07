<?php
/**
 * Plugin Name: Fortune REST Example
 * Description: A Fortune post type, used as an example of REST API usage at WordCamp Asheville 2019
 * Version: 1.0
 * Author: pbaylies
 */

/**
 * Register a fortune post type, with REST API support
 *
 * Based on example at: https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-rest-api-support-for-custom-content-types/#registering-a-custom-post-type-with-rest-api-support
 */
add_action( 'init', 'wcavl_fortune_register' );
function wcavl_fortune_register() {
    $args = array(
        'public'       => true,
        'show_in_rest' => true, // this flag makes this custom post type accessible in the WordPress REST API
        'label'        => 'Fortune'
    );
    register_post_type( 'wcavl_fortune', $args );
}

/**
 * @param $atts - no attributes used
 * @return string - JavaScript code for fetching a fortune from the WordPress REST API
 */
function wcavl_fortune_show_shortcode( $atts ) {
    $post_counts = wp_count_posts( 'wcavl_fortune' );
    $total_posts = $post_counts->publish; // get the total number of published posts
    if ( $total_posts <= 0 )
        return '';
    $buf =<<<EOF
<div id="show_fortune"></div>
<script>
var xhr = new XMLHttpRequest(); // fetch a single fortune at random from the REST API, using JavaScript
xhr.open('GET', '/wordpress/wp-json/wp/v2/wcavl_fortune/?posts_per_page=1&offset=' + Math.floor(Math.random() * {$total_posts}));
xhr.onload = function() {
    if (xhr.status === 200) {
        postdata = JSON.parse(xhr.responseText);
        if (postdata) { // display the fortune in the container
            document.querySelector('#show_fortune').innerHTML = postdata[0].content.rendered;
        }
    }
};
xhr.send();
</script>
EOF;
/*
Here's the equivalent script in jQuery, if you're already using jQuery on the front end:

jQuery(document).ready($) {
    $.ajax('/wordpress/wp-json/wp/v2/wcavl_fortune/?posts_per_page=1&offset=' + Math.floor(Math.random() * {$total_posts}).done(function(postdata) {
        $('#show_fortune').html(postdata[0].content.rendered);
    });
}
*/
    return $buf;
}
add_shortcode('show_fortune', 'wcavl_fortune_show_shortcode' );

