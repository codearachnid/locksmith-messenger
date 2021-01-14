<?php


function locksmith_messenger_add_settings_page() {
    add_options_page( 'Locksmith Messenger', 'Locksmith Messenger', 'manage_options', 'locksmith-messenger', 'locksmith_messenger_register_settings_page' );
}
add_action( 'admin_menu', 'locksmith_messenger_add_settings_page' );

function locksmith_messenger_register_settings_page() {
    ?>
    <h2>Locksmith Messenger Settings</h2>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'locksmith_messenger_settings' );
        do_settings_sections( 'locksmith_messenger_settings' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

// locksmith_messenger_register_settings
function locksmith_messenger_register_settings() {
    register_setting( 'locksmith_messenger_settings', 'locksmith_messenger_settings', 'locksmith_messenger_register_settings_validate' );
    add_settings_section( 'locksmith_messenger_settings_twilio', 'Twilio API Settings', 'locksmith_messenger_settings_twilio_header', 'locksmith_messenger_settings' );
    add_settings_field( 'locksmith_messenger_plugin_setting_sid', 'Twilio SID', 'locksmith_messenger_twilio_sid', 'locksmith_messenger_settings', 'locksmith_messenger_settings_twilio' );
    add_settings_field( 'locksmith_messenger_plugin_setting_token', 'Twilio Token', 'locksmith_messenger_twilio_token', 'locksmith_messenger_settings', 'locksmith_messenger_settings_twilio' );
    add_settings_field( 'locksmith_messenger_plugin_setting_outbound_number', 'Outbound Phone Number', 'locksmith_messenger_twilio_outbound', 'locksmith_messenger_settings', 'locksmith_messenger_settings_twilio' );
    add_settings_section( 'locksmith_messenger_settings_messaging', 'Customize Messaging', 'locksmith_messenger_settings_messaging_header', 'locksmith_messenger_settings' );
    add_settings_field( 'locksmith_messenger_plugin_setting_default_call_message', 'Default Call Message', 'locksmith_messenger_default_call_message', 'locksmith_messenger_settings', 'locksmith_messenger_settings_messaging' );
    add_settings_field( 'locksmith_messenger_plugin_setting_specified_call_message', 'Specified Call Message', 'locksmith_messenger_specified_call_message', 'locksmith_messenger_settings', 'locksmith_messenger_settings_messaging' );
    add_settings_field( 'locksmith_messenger_plugin_setting_specified_sms_message', 'Specified SMS Message', 'locksmith_messenger_specified_sms_message', 'locksmith_messenger_settings', 'locksmith_messenger_settings_messaging' );
}
add_action( 'admin_init', 'locksmith_messenger_register_settings' );

function locksmith_messenger_register_settings_validate($input){
  return $input;
}

function locksmith_messenger_settings_twilio_header() {
    echo '<p>If <code>`WP_TWILIO_API_ACCOUNT_SID`</code> or <code>`WP_TWILIO_API_AUTHTOKEN`</code> are set in the code then it will override what you have set in the DB.</p>';
}

function locksmith_messenger_twilio_sid() {
    if (defined( 'WP_TWILIO_API_ACCOUNT_SID' ) ){
      echo '<input id="locksmith_messenger_setting_api_key" name="locksmith_messenger_settings[twilio_sid]" type="text" disabled>';
    } else {
      $options = get_option( 'locksmith_messenger_settings' );
      echo '<input id="locksmith_messenger_setting_api_key" name="locksmith_messenger_settings[twilio_sid]" type="text" value="'.  esc_attr( $options['twilio_sid'] ) . '" />';
    }
}

function locksmith_messenger_twilio_token() {
  if (defined( 'WP_TWILIO_API_AUTHTOKEN' ) ){
    echo '<input id="locksmith_messenger_setting_results_limit" name="locksmith_messenger_settings[twilio_token]" type="text" disabled >';
  } else {
    $options = get_option( 'locksmith_messenger_settings' );
    echo '<input id="locksmith_messenger_setting_results_limit" name="locksmith_messenger_settings[twilio_token]" type="text" value="'. esc_attr( $options['twilio_token'] ) . '" />';
  }
}

function locksmith_messenger_twilio_outbound() {
    $options = get_option( 'locksmith_messenger_settings' );
    echo '<input id="locksmith_messenger_setting_outbound_number" name="locksmith_messenger_settings[outbound_number]" type="text" value="'. esc_attr( $options['outbound_number'] ) . '" />';
}


function locksmith_messenger_settings_messaging_header() {
    ?><p>Use the following codes to register the dynamic submitted fields on the messages sent:</p>
    <ul>
      <li><code>{locksmith_id}</code> </li>
      <li><code>{locksmith_name}</code> </li>
  		<li><code>{date}</code> </li>
  		<li><code>{request_type}</code> </li>
  		<li><code>{request_sub_type}</code> </li>
  		<li><code>{request_time}</code> </li>
  		<li><code>{contact_name}</code> </li>
  		<li><code>{contact_number}</code> </li>
  		<li><code>{contact_address_city}</code> </li>
  		<li><code>{contact_address_state}</code> </li>
  		<li><code>{contact_address_zip}</code> </li>
  		<li><code>{email_to}</code> </li>
    </ul><?php
}

function locksmith_messenger_default_call_message(){
  $options = get_option( 'locksmith_messenger_settings' );
  echo '<textarea id="locksmith_messenger_setting_default_call_message" name="locksmith_messenger_settings[default_call_message]">'. esc_attr( $options['default_call_message'] ) . '</textarea>';
}
function locksmith_messenger_specified_call_message(){
  $options = get_option( 'locksmith_messenger_settings' );
  echo '<textarea id="locksmith_messenger_setting_specified_call_message" name="locksmith_messenger_settings[specified_call_message]">'. esc_attr( $options['specified_call_message'] ) . '</textarea>';
}
function locksmith_messenger_specified_sms_message(){
  $options = get_option( 'locksmith_messenger_settings' );
  echo '<textarea id="locksmith_messenger_setting_specified_sms_message" name="locksmith_messenger_settings[specified_sms_message]">'. esc_attr( $options['specified_sms_message'] ) . '</textarea>';
}
