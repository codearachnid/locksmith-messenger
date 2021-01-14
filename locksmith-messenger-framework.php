<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class LocksmithMessenger {
  protected $client;
  protected $outbound_number;
  protected $settings;
  private $data;

  function __construct(){
    $this->settings = get_option( 'locksmith_messenger_settings' );
    $sid = defined('WP_TWILIO_API_ACCOUNT_SID' ) ? WP_TWILIO_API_ACCOUNT_SID : $this->settings['twilio_sid'];
    $token = defined('WP_TWILIO_API_AUTHTOKEN' ) ? WP_TWILIO_API_AUTHTOKEN : $this->settings['twilio_token'];
    $this->outbound_number = $this->settings['outbound_number'];

    // Your Account SID and Auth Token from twilio.com/console
    $this->client = new Client($sid, $token);
  }
  function set_data( $data ) {
    $this->data = $data;
  }
  function send(){
    if( empty( $this->data['locksmith_id'] ) ){
      return false;
    }

    $contact = $this->lookupLocksmithPreferredContact();
    $to = $this->cleanNumber( $contact['number'] );

    if( empty( $to ) ){
      return false;
    }

    switch ($contact['type']) {
      case 'sms':
        $msg = $this->filterMessage( $this->settings['specified_sms_message'] );
        $this->sendText( $to, $msg );
        break;
      case 'call':
        $msg = $this->filterMessage( $this->settings['specified_call_message'] );
        $this->makeCall( $to, $msg );
        break;
      case 'none';
        // exit without notifying
        break;
      default:
        $msg = $this->filterMessage( $this->settings['default_call_message'] );
        $this->makeCall( $to, $msg );
        break;
    }
    return true;
  }
  function lookupLocksmithPreferredContact(){
    $response = array('type' => 'default', 'number' => null);

    $notification_type = geodir_get_post_meta($this->data['locksmith_id'], 'locksmith_notification_type', true);
    if( !empty($notification_type) ){
        $response['type'] = $notification_type;
        if( $notification_type != 'none' ){
          $response['number'] = geodir_get_post_meta($this->data['locksmith_id'], 'locksmith_notification_number', true);
        }
    } else {
      $response['number'] = geodir_get_post_meta($this->data['locksmith_id'], 'phone', true);
    }

    return $response;
  }
  function makeCall( $to, $msg ){
    $this->client->account->calls->create($to, $this->outbound_number,
              [ 'twiml' => '<Response><Say>' . $msg . '</Say></Response>']
    );
  }
  function sendText( $to, $msg ){
    $this->client->messages->create(
        $to,
        [
            'from' => $this->outbound_number,
            'body' => $msg
        ]
    );
  }
  function cleanNumber($number){

    if( empty( $number ))
      return null;

    $clean_number = trim( $number );

    if( strpos($clean_number, '+1') === 0 ){
      $clean_number = str_replace('+1', '', $clean_number);
    }

    $clean_number = str_replace(array('-', ' ', '.', ',', '(', ')', '+'), '', $clean_number);

    if( strlen($clean_number) != 10 ){
      return null;
    }
    return '+1' . $clean_number;
  }
  function filterMessage( $msg ){
    return $this->parse( $msg, $this->data );
  }

  function parse( $subject, array $variables, $escapeChar = '@', $errPlaceholder = null ) {
        $esc = preg_quote($escapeChar);
        $expr = "/
            $esc$esc(?=$esc*+{)
          | $esc{
          | {(\w+)}
        /x";

        $callback = function($match) use($variables, $escapeChar, $errPlaceholder) {
            switch ($match[0]) {
                case $escapeChar . $escapeChar:
                    return $escapeChar;
                case $escapeChar . '{':
                    return '{';
                default:
                    if (isset($variables[$match[1]])) {
                        return $variables[$match[1]];
                    }

                    return isset($errPlaceholder) ? $errPlaceholder : $match[0];
            }
        };

        return preg_replace_callback($expr, $callback, $subject);
    }
}
