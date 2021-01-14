<?php
/**
 * Plugin Name: Locksmith Messenger
 * Description: Provide functionality to contact locksmiths for leads and other embedded notification means
 * Version: 1.0.0
 * Text Domain: locksmith-messenger
 * Author: Timothy Wood @codearachnid
 * Author URI: https://codearachnid.com
 * License: GPL3
 */


defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! defined( 'LOCKMSG_PLUGIN_DIR' ) ) {
	define( 'LOCKMSG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'LOCKMSG_BASENAME' ) ) {
	define( 'LOCKMSG_BASENAME', plugin_basename( __FILE__ ) );
}

require LOCKMSG_PLUGIN_DIR . 'twilio-php-main/src/Twilio/autoload.php';
require LOCKMSG_PLUGIN_DIR . 'locksmith-messenger-framework.php';
require LOCKMSG_PLUGIN_DIR . 'locksmith-messenger-admin.php';

function locksmith_messenger_notify( $data ){
  if( class_exists('LocksmithMessenger')) {
    $notify = new LocksmithMessenger();
    $notify->set_data( $data );
    if( !empty( $data['locksmith_id'] ) ){
        return $notify->send();
    }
  }
}
