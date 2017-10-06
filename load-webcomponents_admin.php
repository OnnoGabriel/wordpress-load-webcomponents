<?php

/**
 * initialize settings
 */
function load_webcomponents_settings_init() {
  // register new setting: load_webcomponents_options (array)
  register_setting( 'load_webcomponents', 'load_webcomponents_options' );

  // register the main settings section
  add_settings_section(
    'load_webcomponents_section_main',
    __( 'Load Webcomponents', 'load-webcomponents' ),
    'load_webcomponents_settings_section_main_cb',
    'wporg'
  );


  // register a new field in the "wporg_section_developers" section, inside the "wporg" page
//   add_settings_field(
//   'wporg_field_pill', // as of WP 4.6 this value is used only internally
//   // use $args' label_for to populate the id inside the callback
//   __( 'Pill', 'wporg' ),
//   'wporg_field_pill_cb',
//   'wporg',
//   'wporg_section_developers',
//   [
//   'label_for' => 'wporg_field_pill',
//   'class' => 'wporg_row',
//   'wporg_custom_data' => 'custom',
//   ]
// );
}

/**
 * register load_webcomponents_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'load_webcomponents_settings_init' );



// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function load_webcomponents_settings_section_main_cb( $args ) {
 ?>
 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'wporg' ); ?></p>
 <?php
}



/**
 * top level menu
 */
function load_webcomponents_options_page() {
  // add top level menu page
  add_options_page(
  'Load Webcomponents',
  'Load Webcomponents',
  'manage_options',
  'load_webcomponents',
  'load_webcomponents_options_page_html'
);
}

/**
 * register our wporg_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'load_webcomponents_options_page' );

/**
 * top level menu:
 * callback functions
 */
function load_webcomponents_options_page_html() {
 // check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }

 // add error/update messages

 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
 }

 // show error/update messages
 settings_errors( 'wporg_messages' );
 ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post">
 <?php
 // output security fields for the registered setting "wporg"
 settings_fields( 'wporg' );
 // output setting sections and their fields
 // (sections are registered for "wporg", each field is registered to a specific section)
 do_settings_sections( 'wporg' );
 // output save settings button
 submit_button( 'Save Settings' );
 ?>
 </form>
 </div>
 <?php
}
