<?php

/**
 * Initialize settings at admin_init hook
 */
function load_webcomponents_settings_init() {
  	// Register settings
	register_setting(
		'load_webcomponents_settings',
		'load_webcomponents_settings',
		array(
			'sanitize_callback' => 'load_webcomponents_settings_sanitize_cb'
		)
	);

	// Register section "Common Settings"
	add_settings_section(
		'load_webcomponents_settings_section_common',
		__('Settings', 'load_webcomponents'),
		'load_webcomponents_settings_section_common_cb',
		'load_webcomponents_settings_page'
	);

	// Register field "Enable web components loading"
	add_settings_field(
		'load_webcomponents_setting_enable_loading',
		__( 'Enable loading', 'load_webcomponents' ),
		'load_webcomponents_setting_enable_loading_callback',
		'load_webcomponents_settings_page',
		'load_webcomponents_settings_section_common',
		[
			'label_for' => 'load_webcomponents_enable_loading',
		]
	);

	// Register field "Allow src attribute"
	add_settings_field(
		'load_webcomponents_setting_allow_src',
		__( 'Allow src attribute', 'load_webcomponents' ),
		'load_webcomponents_setting_allow_src_callback',
		'load_webcomponents_settings_page',
		'load_webcomponents_settings_section_common',
		[
			'label_for' => 'load_webcomponents_allow_src',
		]
	);

	// Register field "Web components list"
	add_settings_field(
		'load_webcomponents_setting_list',
		__( 'List of available web components', 'load_webcomponents' ),
		'load_webcomponents_setting_list_callback',
		'load_webcomponents_settings_page',
		'load_webcomponents_settings_section_common',
		[
			'label_for' => 'load_webcomponents_list',
		]
	);

}
add_action( 'admin_init', 'load_webcomponents_settings_init' );



// Callback function for section "Common Settings"
function load_webcomponents_settings_section_common_cb( $args ) {}

// Callback function for section "Web Components List"
function load_webcomponents_settings_section_list_cb( $args ) {}

/**
 * Call back function for sanitization of settings data
 */
function load_webcomponents_settings_sanitize_cb($data) {
	// Sanitize "enable loading"
	if (isset($data['load_webcomponents_enable_loading']) && 1 != $data['load_webcomponents_enable_loading']) {
		add_settings_error(
			'requiredEnableLoadingCheckbox',
			'empty',
			__('Incorrent option.', 'load_webcomponents'),
			'error'
		);
		unset( $data['load_webcomponents_enable_loading'] );
	}

	// Sanitize "allow src attribute"
	if (isset($data['load_webcomponents_allow_src']) && 1 != $data['load_webcomponents_allow_src']) {
		add_settings_error(
			'requiredAllowSrcCheckbox',
			'empty',
			__('Incorrent option.', 'load_webcomponents'),
			'error'
		);
		unset( $data['load_webcomponents_allow_src'] );
	}

	// Sanitize "allow src attribute"
	if (! isset( $data['load_webcomponents_list'] ) || ! is_array( $data['load_webcomponents_list']  ) ) {
		$data['load_webcomponents_list']  = array();
	}
	else {
		$num_items = count( $data['load_webcomponents_list'] );
		$items_removed_count = 0;
		$error_set = array();
		foreach ( $data['load_webcomponents_list'] as $index => $item ) {

			// Sanitize Name
			$data['load_webcomponents_list'][ $index ]['name'] = esc_html( $item['name'] );

			// Sanitize URL
			$data['load_webcomponents_list'][ $index ]['url'] = esc_url_raw( $item['url'] );

			// Check if name AND url is set
			if (   ! isset( $item['name'] ) || empty( $item['name'] )
				|| ! isset( $item['url'] )	|| empty( $item['url'] )
		  	   ) {

  		  		unset( $data['load_webcomponents_list'][ $index ] );

				// Old items
				if ( $index+1 != $num_items ) {
					$items_removed_count ++;
					add_settings_error(
						'itemRemovedFromList',
						'empty',
						__('Item removed from list.', 'load_webcomponents'),
						'updated'
					);
				}
				// New item
				else {
					if ( ! empty( $item['name'] ) || ! empty($item['url'] ) ) {
						add_settings_error(
							'requiredNameAndUrl',
							'empty',
							__('Please enter name <em>and</em> URL for the new web component.', 'load_webcomponents'),
							'error'
						);
					}
				}
		    }

			// Check if name is unique
			$unique = true;
			foreach ( $data['load_webcomponents_list'] as $index2 => $item2 ) {
				if ( isset( $item['name'] ) && isset( $item2['name'] ) && ! empty ( $item['name'] )
				&& $index != $index2 && $item['name'] == $item2['name'] ) {
					$error_id = 'name_' . esc_html( $item['name'] ) . '_not_unique';
					if ( ! array_key_exists($error_id, $error_set) ) {
						add_settings_error(
							$error_id,
							'empty',
							sprintf( __('Name "%s" is not unique!', 'load_webcomponents'), esc_html( $item['name'] ) ),
							'error'
						);
						$error_set[ $error_id ] = 1;
					}
				}
			}

		}
	}
	return $data;
}

/**
 * Callback function for field "Enable loading"
 */
function load_webcomponents_setting_enable_loading_callback( $args ) {
	// Load settings
	$options = get_option( 'load_webcomponents_settings' );

	// Field index
	$id = esc_attr( $args['label_for'] );
	?>
	<input type="checkbox" id="<?php echo $id; ?>" name="load_webcomponents_settings[<?php echo $id; ?>]" value="1" <?php
		checked( isset($options[$id]) );
	?> />
	<p class="description">
		<?php _e('This is the main switch to (de)activate all web component loaded by this plugin.', 'load_webcomponents' ); ?>
	</p>
	<?php
}

/**
 * Callback function for field "Allow src attribute"
 */
function load_webcomponents_setting_allow_src_callback( $args ) {
	// Load settings
	$options = get_option( 'load_webcomponents_settings' );

	// Field index
	$id = esc_attr( $args['label_for'] );
	?>
	<input type="checkbox" id="<?php echo $id; ?>" name="load_webcomponents_settings[<?php echo $id; ?>]" value="1" <?php
		checked( isset($options[$id]) );
	?> />
	<p class="description">
		<?php _e('Allow to add web components via the <code>src="&lt;url&gt;"</code> attribute.', 'load_webcomponents' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e('Standard and recommended method is to identify a component by its identifier and define the URL in the list below.', 'load_webcomponents' ); ?>
	</p>
	<?php
}

/**
 * Callback function for field "Web components list"
 */
function load_webcomponents_setting_list_callback( $args ) {
	// Load settings
	$options = get_option( 'load_webcomponents_settings' );

	// Field index
	$id = esc_attr( $args['label_for'] );

	// List array
	$list = ( isset( $options[ $id ] ) && is_array( $options[ $id ] ) ) ? $options[ $id ] : array();

	?>
	<table>
	<tr>
		<th style="text-align:center;"><?php esc_html_e('#', 'load_webcomponents' ); ?></th>
		<th style="text-align:center;"><?php esc_html_e('Identifier', 'load_webcomponents' ); ?></th>
		<th style="text-align:center;"><?php esc_html_e('URL', 'load_webcomponents' ); ?></th>
	</tr>
	<?php
	$count = 0;
	foreach ( $list as $item ) {
		load_webcomponents_settings_list_item( array(
			'count'    	 => $count,
			'count_name' => $count + 1,
			'name_value' => $item['name'],
			'url_value'  => $item['url'],
		));
		$count++;
	}
	load_webcomponents_settings_list_item( array(
		'count'    	 => $count,
		'count_name' => __('New', 'load_webcomponents') . ':',
		'name_value' => '',
		'url_value'  => '',
	));
	?>
	</table>
	<p class="description">
		<?php esc_html_e('To delete an item from the list, just delete the identifier and/or URL of the item', 'load_webcomponents' ); ?>
	</p>
	<?php
}

function load_webcomponents_settings_list_item( $args ) {
	if (! isset( $args['count'] ) || ! isset( $args['name_value'] )  || ! isset( $args['url_value'] ) ) {
		return;
	}

	$count = (int) $args['count'];

	?>
	<tr>
		<td style="text-align:center;">
			<?php esc_html_e( $args['count_name'] ); ?>
		</td>
		<td>
			<input type="text" name="load_webcomponents_settings[load_webcomponents_list][<?php echo $count; ?>][name]" value="<?php esc_html_e( $args['name_value']); ?>" />
		</td>
		<td>
			<input type="text" name="load_webcomponents_settings[load_webcomponents_list][<?php echo $count; ?>][url]" value="<?php esc_html_e( $args['url_value']); ?>" size="60" />
		</td>
	</tr>
	<?php
}



/**
 * Register load_webcomponents_settings_page to the admin_menu action hook
 */
function load_webcomponents_settings_page() {
	// Add settings page
	add_options_page(
		'Load Web Components',
		'Load Web Components',
		'manage_options',
		'load_webcomponents',
		'load_webcomponents_settings_page_html'
	);
}
add_action( 'admin_menu', 'load_webcomponents_settings_page' );


/**
 * Output settings page
 */
function load_webcomponents_settings_page_html() {
	// Check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<h2><?php _e('Usage', 'load_webcomponents' ); ?></h2>
		<p><?php _e('Load a web component or JavaScript library to a post or page just by adding a shortcode to the content. There are two types of shortcodes available:', 'load_webcomponents' ); ?></p>
		<ol>
			<li><code>[load-webcompontent src="&lt;url&gt;"]</code> &minus; <?php
				printf( __('where %s is the URL of the web component or JavaScript library.', 'load_webcomponents' ), '<code>&lt;url&gt;</code>');
			?></li>
			<li><code>[load-webcompontent name="&lt;identifier&gt;"]</code> &minus; <?php
				printf( __('where %s is the name of the web component and the URL is taken from the list below.', 'load_webcomponents' ), '<code>&lt;identifier&gt;</code>');
			?></li>
		</ol>
		<p><?php _e('The type with <code>name="&lt;identifier&gt;</code> is recommended, because if the URL of a component changes, it has be changed only on this settings page, and not on each post or page using the component.', 'load_webcomponents' ); ?></p>
		<br />
		<br />

		<form action="options.php" method="post">
			<?php

			// Output security fields
			settings_fields( 'load_webcomponents_settings' );

			// Output setting sections and their fields
			do_settings_sections( 'load_webcomponents_settings_page' );

			// Output save settings button
			submit_button();

			?>
		</form>
	</div>
	<?php
}
