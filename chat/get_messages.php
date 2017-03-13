<?php
/*////////////////////////////////////////////////
	Script made by: ikefi
		http://steamcommunity.com/id/ikefi/
////////////////////////////////////////////////*/

session_start();

require( 'data_handler.php' );

// Set header
header( 'Content-Type: application/json' );

if( !isset( $_POST[ 'cur_msg_id' ] ) ){ die; }
$current_message_id = $_POST[ 'cur_msg_id' ];

// Chat id
$chat_id = $_SESSION[ 'chat_id' ];

// Get the data
$data = get_data( $chat_id );

// Nothing to send if there's no messages
if( $data->chat_log == "" ){ die; }

// Nothing to send if the client already has all messages
if( $current_message_id == $data->message_id ){ die; }

// If not received a single message yet, print all messages
if( $current_message_id == 0 ){
	echo '{"messageid":' . $data->message_id . ',"chat_log":['
		. str_replace( PHP_EOL, ',', rtrim( $data->chat_log, PHP_EOL ) ) . ']}';
	die;
}

// Print the all new messages
$messages = explode( PHP_EOL, rtrim( $data->chat_log, PHP_EOL ) );
$message_count = count( $messages ); // The amount of messages we have

$start_index = $message_count - ( $data->message_id - $current_message_id ); // The amount of messages new to the client
if( $start_index < 0 ){ $start_index = 0; }

$json_string = '{"messageid":' . $data->message_id . ',"chat_log":[';
for( $i=$start_index; $i<$message_count; $i++ ){
	$json_string .= $messages[ $i ] . ',';
}
echo rtrim( $json_string, ',' ) . ']}';
?>