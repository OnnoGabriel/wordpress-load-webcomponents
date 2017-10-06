<?php
/**
 * Class LoadWebComponentsTest
 *
 * @package Load_Webcomponents
 */

/**
 * Sample test case.
 */
class LoadWebComponentsTest extends WP_UnitTestCase {

	/**
	 * test of URL extraction from shortcode attribute
	 * Two possible notations: src="..." or src='...'
	 */
	function test_load_webcomponents_extract_url() {
		$url = load_webcomponents_extract_url('src="https://www.test.com"');
		$this->assertEquals( 'https://www.test.com', $url );
		$url = load_webcomponents_extract_url("src='https://www.test.com'");
		$this->assertEquals( 'https://www.test.com', $url );
	}

	/**
	 * test of URLs extraction from page content
	 */
	function test_load_webcomponents_get_webcomponents_urls() {
		$content = 'Blablabla
								[load-webcomponent src="https://www.test.com"]
								[load-webcomponent src="https://www.test2.com"]
								Blablabla';
		$urls = load_webcomponents_get_webcomponents_urls($content);
		$this->assertContains( 'https://www.test.com', $urls );
		$this->assertContains( 'https://www.test2.com', $urls );
	}

}
