<?php
/**
 * Class LoadWebComponentLibraryTest
 *
 * @package Load_Webcomponent_Library
 */

/**
 * Sample test case.
 */
class LoadWebComponentLibraryTest extends WP_UnitTestCase {

	/**
	 * test of URL extraction from shortcode attribute
	 * Two possible notations: src="..." or src='...'
	 */
	function test_load_webcomponent_extract_url() {
		$url = load_webcomponent_extract_url('src="https://www.test.com"');
		$this->assertEquals( 'https://www.test.com', $url );
		$url = load_webcomponent_extract_url("src='https://www.test.com'");
		$this->assertEquals( 'https://www.test.com', $url );
	}

	/**
	 * test of URLs extraction from page content
	 */
	function test_load_webcomponent_get_webcomponent_urls() {
		$content = 'Blablabla
								[load-webcomponent src="https://www.test.com"]
								[load-webcomponent src="https://www.test2.com"]
								Blablabla';
		$urls = load_webcomponent_get_webcomponent_urls($content);
		$this->assertContains( 'https://www.test.com', $urls );
		$this->assertContains( 'https://www.test2.com', $urls );
	}

}
