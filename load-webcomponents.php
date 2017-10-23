<?php
/**
 * Plugin Name:     Load Web Components
 * Plugin URI:      https://github.com/OnnoGeorg/wordpress-load-webcomponents
 * Description:     Loads web components triggered by shortcodes in posts and pages
 * Author:          Onno Gabriel
 * Author URI:      https://www.datacodedesign.de
 * Text Domain:     load_webcomponents
 * Domain Path:     /languages
 * Version:         0.1.1
 *
 * @package         Load_Webcomponents
 */

 // If this file is accessed diretcly, then abort.
 if ( ! defined( 'ABSPATH' ) ) {
     exit;
 }

/***
 * Plugin administration
 */
if ( is_admin() ) {

	function load_webcomponents_load_textdomain() {
		load_plugin_textdomain(
			'load_webcomponents',
			false,
			basename( dirname( __FILE__ ) ) . '/languages'
		);
	}
	add_action( 'init', 'load_webcomponents_load_textdomain' );

	// Add settings link to plugins list
	function load_webcomponents_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=load_webcomponents_settings_page">' . __( 'Settings' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'load_webcomponents_settings_link' );

	// Admin settings page
	require_once( dirname( __FILE__ ) . '/load-webcomponents_admin.php' );

	// Return (no web components in the amdin area needed)
	return;
}


/***
 * Dummy shortcode addition.
 * The short codes is not processed the standard way within the content,
 * but in the page header via wp_head hook.
 */
function load_webcomponents_dummy( $atts ) {}
add_shortcode( 'load-webcomponent', 'load_webcomponents_dummy' );


/***
 * Add shortcode analysis and script loader to page header
 */
function load_webcomponents_scripts() {
	global $post;

	// Not a post? => return
	if( ! is_a( $post, 'WP_Post' ) ) {
		return false;
	}

	// Load settings
	$options = get_option( 'load_webcomponents_settings' );

	// Web components disabled? => return
	if ( ! isset( $options['load_webcomponents_enable_loading'] )
	     || false == $options['load_webcomponents_enable_loading'] ) {
		return false;
	}


	// Content contains no shortcode tag? => return
	if ( false === strpos( $post->post_content, '[' ) ) {
		return false;
	}

	// Get URLs of all web components added by shortcodes
	$urls = load_webcomponents_get_webcomponents_urls( $post->post_content, $options );

	// Echo all found web component/script URLs to HTML header:
	if ( is_array( $urls ) && count ( $urls ) > 0 ) {
		foreach ( $urls as $url ) {
	  		echo "<script async defer src='" . $url . "'></script>\n";
		}
	}

}
add_action('wp_head', 'load_webcomponents_scripts');

/***
 * Extracts all URLs from [load-webcomponent] short codes within the content
 */
function load_webcomponents_get_webcomponents_urls( $content = '', $options = array() ) {
    if ($content === '') {
        return false;
    }

	preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER );
	if ( empty( $matches ) ) {
		return false;
	}

	$urls = array();

	//Go through all found shortcodes
	foreach ( $matches as $shortcode ) {

		// Shortcode "load-webcomponent"?
		if ( 'load-webcomponent' === $shortcode[2] ) {

			$url = '';
			$i = 3;
			while ( isset( $shortcode[$i] ) && ! empty( $shortcode[$i] ) ) {

				// URLs by name identifier
				$url = load_webcomponents_extract_url_by_name( $shortcode[$i], $options );

				// Not found? Try to extract URLs from src attributes
				if ( ! $url
			      && isset( $options['load_webcomponents_allow_src'] )
				  && true == $options['load_webcomponents_allow_src']
			       ) {

					$url = load_webcomponents_extract_url_by_src( $shortcode[$i] );
				}

				if ( $url !== false && ! empty( $url ) ) {
					array_push( $urls, $url );
				}

				$i++;

			}

		}

	}
	return $urls;
}


/***
 * Extracts URL from short code attribute 'name="<identifier>"'
 */
function load_webcomponents_extract_url_by_name( $string = '', $options = array() ) {
	// No name identifiers defined => return
	if (! isset( $options['load_webcomponents_list'] ) || ! is_array( $options['load_webcomponents_list'] ) ) {
		return;
	}

	// Find name attribute
	preg_match('/name\=[\"\'](.*?)[\"\']/', $string, $match);

	if ( ! empty( $match ) && ! empty( $match[1] ) ) {
		$name = esc_html( $match[1] );
		foreach( $options['load_webcomponents_list'] as $index => $item) {
			if ( $name == $item['name'] ) {
				return esc_url_raw( $item['url'] );
			}
		}
	}
	return '';

}

/***
 * Extracts URL from short code attribute 'src="<url>"'
 */
function load_webcomponents_extract_url_by_src( $string = '' ) {
	// Fine src attribute
	preg_match('/src\=[\"\'](.*?)[\"\']/', $string, $match);

	if ( ! empty( $match ) && ! empty( $match[1] ) ) {
		return esc_url( $match[1] );
	}
	return '';

}
