<?php
/**
 * Plugin Name:     Load Webcomponents
 * Plugin URI:      https://www.datacodedesign.de/wordpress-plugin-load-webcomponents
 * Description:     Loads web components triggered by shortcodes in posts and pages
 * Author:          Onno Gabriel
 * Author URI:      https://www.datacodedesign.de
 * Text Domain:     load-webcomponents
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Load_Webcomponents
 */

// Plugin administration
if ( is_admin() ) {
  // Settings link to plugins list
  function load_webcomponents_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=load_webcomponents">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
  }
  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'load_webcomponents_settings_link' );
  // Admin settings page
  require_once( dirname( __FILE__ ) . '/load-webcomponents_admin_demo.php' );
}

// Dummy shortcode addition.
// Necessary to enable function shortcode_exists()
add_shortcode( 'load-webcomponent', 'load_webcomponents_dummy' );
function load_webcomponents_dummy( $atts ) {}

// Add shortcode analysis and script loader to page header
add_action('wp_head', 'load_webcomponents_scripts');

function load_webcomponents_scripts() {
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
  $urls = load_webcomponents_get_webcomponents_urls( $post->post_content );

  // Echo all script URLs:
  if ( is_array( $urls ) && count ( $urls ) > 0 ) {
    foreach ( $urls as $url ) {
      echo "<script async defer src='" . $url . "'></script>\n";
    }
  }

}

function load_webcomponents_get_webcomponents_urls( $content = '' ) {
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

        $url = load_webcomponents_extract_url( $shortcode[$i] );

        if ( $url !== false && ! empty( $url ) )
          array_push( $urls, $url );

        $i++;
      }

    }

  }

  return $urls;
}

function load_webcomponents_extract_url( $string = '' ) {

  preg_match('/src\=[\"\'](.*?)[\"\']/', $string, $match);

  if ( ! empty( $match ) && ! empty( $match[1] ) ) {
    return esc_url( $match[1] );
  }
  return '';

}
