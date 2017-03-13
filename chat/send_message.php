<?php
/*////////////////////////////////////////////////
	Script made by: ikefi
		http://steamcommunity.com/id/ikefi/
////////////////////////////////////////////////*/

session_start();

require( 'config.php' );
require( 'data_handler.php' );

if( !isset( $_SESSION[ 'chat_nick' ] ) ){ die; }

$message = isset( $_POST[ 'message' ] ) ?  $_POST[ 'message' ] : ( isset( $_GET[ 'message' ] ) ? $_GET[ 'message' ] : "" );
if( !$message ){ die; } // Don't do anything if there's no message or if the message is empty
$message = preg_replace('/\s+/', ' ', str_replace( "\\", "\\\\", htmlspecialchars( substr( $message, 0, $message_character_limit ) ) ) );
if( $message == "" || $message == " " ){ die; } // Don't do anything if the message is empty or contains only spaces

$player_nick = $_SESSION[ 'chat_nick' ];
$player_steamid = $_SESSION[ 'chat_steamid' ];
$player_color = $_SESSION[ 'chat_color' ];
$player_admin = $_SESSION[ 'chat_admin' ];
$chat_id = $_SESSION[ 'chat_id' ];

// Get the data
$data = get_data( $chat_id );
	
	// Set last message
	$data->last_message = time();
	
	// Remove first message, if it reached the log limit
	if( substr_count( $data->chat_log, PHP_EOL ) >= $logging_limit ){
		$data->chat_log = substr( $data->chat_log, strpos( $data->chat_log, PHP_EOL ) + 2 );
	}
	
	// Add one to message_id
	$data->message_id++;
	
	// Create json string
	$json_message = 
		'{"id":' . $data->message_id
		. ',"time":' . time()
		. ',"name":"' . $player_nick
		. '","steamid":"' . $player_steamid
		. '","color":"'. $player_color
		. '","admin":' . $player_admin
		. ',"msg":"' . $message
		. '"}' . PHP_EOL;
	
	// Add message to the log
	$data->chat_log .= $json_message;
	
// Save the data
save_data( $data, $chat_id );
?>