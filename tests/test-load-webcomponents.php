<?php
/**
 * Class LoadWebComponentsTest
 *
 * @package Load_Webcomponents
 */

/**
 * Testing Class
 */
class LoadWebComponentsTest extends WP_UnitTestCase {

	/**
	 * test of URL extraction from shortcode attribute
	 * Two possible notations: src="..." or src='...'
	 */
	 public function test_load_webcomponents_extract_url() {
		$url = load_webcomponents_extract_url_by_src('src="https://www.test.com"');
		$this->assertEquals( 'https://www.test.com', $url );
		$url = load_webcomponents_extract_url_by_src("src='https://www.test.com'");
		$this->assertEquals( 'https://www.test.com', $url );
	}

	/**
	 * test of URLs extraction from page content
	 * with setting 'load_webcomponents_allow_src' = true
	 */
	public function test_load_webcomponents_get_webcomponents_urls() {
		$content = 'Blablabla
					[load-webcomponent src="https://www.test.com"]
					[load-webcomponent src="https://www.test2.com"]
					[load-webcomponent name="wc-identifier"]
					Blablabla';
		$options = array(
			'load_webcomponents_allow_src' => true,
			'load_webcomponents_list' => array(
				array('name' => 'wc-identifier', 'url' => "https://www.test3.com")
			),
		);
		$urls = load_webcomponents_get_webcomponents_urls($content, $options);
		$this->assertContains( 'https://www.test.com', $urls );
		$this->assertContains( 'https://www.test2.com', $urls );
		$this->assertContains( 'https://www.test3.com', $urls );
	}

	/**
	 * test of URLs extraction from page content
	 * with setting 'load_webcomponents_allow_src' = false
	 */
	public function test_load_webcomponents_get_webcomponents_urls_not_allow_src() {
		$content = 'Blablabla
					[load-webcomponent src="https://www.test.com"]
					[load-webcomponent src="https://www.test2.com"]
					[load-webcomponent name="wc-identifier"]
					Blablabla';
		$options = array(
			'load_webcomponents_allow_src' => false,
			'load_webcomponents_list' => array(
				array('name' => 'wc-identifier', 'url' => "https://www.test3.com")
			),
		);
		$urls = load_webcomponents_get_webcomponents_urls($content, $options);
		$this->assertNotContains( 'https://www.test.com', $urls ); // beacause src attr not allowed
		$this->assertNotContains( 'https://www.test2.com', $urls ); // beacause src attr not allowed
		$this->assertContains( 'https://www.test3.com', $urls ); // beacause name attr always allowed
	}

}
