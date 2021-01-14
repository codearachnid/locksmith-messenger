# locksmith-messenger
WordPress Plugin to integrate gravity form notifications with geodirectory/twilio for 1-800-UNLOCKS

example integration
```
add_action( 'gform_after_submission_9', 'unlocks_lead_form_after_submission', 10, 2 );
function unlocks_lead_form_after_submission($entry, $form){
  // ensure the locksmith messenger plugin is active
  if (defined('LOCKMSG_BASENAME')){
	GFCommon::log_debug( 'notify_locksmith: $entry => ' . print_r( $entry, true ) );
	// pass the submission locksmith ID to notification system
	if( !empty( $entry['36'] ) ){
	  // use these data fields in the template for custom messaging
	  $data = array(
		'locksmith_id'=> $entry['36'],
		'date'=>$entry['date_created'],
		'request_type'=>$entry['1'],
		'request_sub_type'=>$entry['2'],
		'request_time'=>$entry['2'],
		'contact_name'=>$entry['12'],
		'contact_number'=>$entry['20'],
		'contact_address_city'=>$entry['21.3'],
		'contact_address_state'=>$entry['21.4'],
		'contact_address_zip'=>$entry['21.5'],
		'email_to'=>$entry['14']
	  );
	  locksmith_messenger_notify( $data ); 
	}
  }
}
```
