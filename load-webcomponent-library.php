<?php
/**
 * Plugin Name:     Load Webcomponent Library
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          Onno Gabriel/DataCodeDesign
 * Author URI:      http://www.datacodedesign.de
 * Text Domain:     load-webcomponent-library
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Load_Webcomponent_Library
 */

add_action('wp_head', 'load_webcomponent_scripts');

function load_webcomponent_scripts() {
  global $post;

  if( ! is_a( $post, 'WP_Post' ) ) {
    return false;
  }

  if ( false === strpos( $post->post_content, '[' ) ) {
   return false;
  }

  if ( shortcode_exists( 'load-webcomponent' ) ) {
    preg_match_all( '/' . get_shortcode_regex() . '/', $post->post_content, $matches, PREG_SET_ORDER );
    if ( empty( $matches ) ) {
      return false;
    }

    foreach ( $matches as $shortcode ) {
      if ( 'load-webcomponent' === $shortcode[2] ) {
        $src = '';
        $i = 3;
        while (isset($shortcode[$i]) && ! empty($shortcode[$i])) {
          preg_match('/src\=[\"\'](.*?)[\"\']/', $shortcode[$i], $att_match);

          if ( ! empty($att_match) && ! empty($att_match[1])) {
            $src = esc_url( $att_match[1] );
          }
          $i++;
        }

        if (! empty($src)) {
          echo "<script async defer src='" . $src . "'></script>\n";
        }

      }
    }
  }

}

add_shortcode( 'load-webcomponent', 'load_webcomponent_dummy' );

function load_webcomponent_dummy( $atts ) {}
