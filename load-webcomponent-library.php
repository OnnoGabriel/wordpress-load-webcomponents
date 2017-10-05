<?php
/**
 * Plugin Name:     Load Webcomponent Library
 * Plugin URI:      https://www.datacodedesign.de/wordpress-plugin-öoad-webcomponent-library
 * Description:     Loads web components triggered by shortcodes in posts and pages
 * Author:          Onno Gabriel
 * Author URI:      https://www.datacodedesign.de
 * Text Domain:     load-webcomponent-library
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Load_Webcomponent_Library
 */

// Dummy shortcode addition.
// Necessary to enable function shortcode_exists()
add_shortcode( 'load-webcomponent', 'load_webcomponent_dummy' );
function load_webcomponent_dummy( $atts ) {}

// Add shortcode analysis and script loader to page header
add_action('wp_head', 'load_webcomponent_scripts');

function load_webcomponent_scripts() {
  global $post;

  // Not a post?
  if( ! is_a( $post, 'WP_Post' ) ) {
    return false;
  }

  // Content contains no shortcode tag?
  if ( false === strpos( $post->post_content, '[' ) ) {
    return false;
  }

  // Get URLs of all web components added by shortcodes
  $urls = load_webcomponent_get_webcomponent_urls( $post->post_content );

  // Echo all script URLs:
  if ( is_array( $urls ) && count ( $urls ) > 0 ) {
    foreach ( $urls as $url ) {
      echo "<script async defer src='" . $url . "'></script>\n";
    }
  }

}

function load_webcomponent_get_webcomponent_urls( $content = '' ) {
  if ($content === '')
    return false;

  preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER );
  if ( empty( $matches ) )
    return false;

  $urls = array();

  //Go through all found shortcodes
  foreach ( $matches as $shortcode ) {

    // Shortcode "load-webcomponent"?
    if ( 'load-webcomponent' === $shortcode[2] ) {

      // Extract all src attributes
      $url = '';
      $i = 3;
      while ( isset( $shortcode[$i] ) && ! empty( $shortcode[$i] ) ) {

        $url = load_webcomponent_extract_url( $shortcode[$i] );

        if ( $url !== false && ! empty( $url ) )
          array_push( $urls, $url );

        $i++;
      }

    }

  }

  return $urls;
}

function load_webcomponent_extract_url( $string = '' ) {

  preg_match('/src\=[\"\'](.*?)[\"\']/', $string, $match);

  if ( ! empty( $match ) && ! empty( $match[1] ) ) {
    return esc_url( $match[1] );
  }
  return '';

}
