# Load Web Components
WordPress plugin for dynamically loading of web components and other JavaScript libraries via shortcodes within the content of a post or page.

## Installation

1. Visit “Plugins -> Add New”,
2. Search for “Load Web Components”,
3. Install the plugin and activate it from your plugins page.

## Usage

Just add a shortcode to the content of a post or a page, for which you want to load a web component or JavaScript library. The shortcode can be called with an URL or with an identifier of the component:

* `[load-webcomponent src="<url>"]`
* `[load-webcomponent name="<identifier>"]`

The corresponding URLs for each identifier can be set on the settings page. You will find the settings page in the Settings menu in your administration area.
