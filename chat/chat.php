<?php
/*////////////////////////////////////////////////
	Script made by: ikefi
		http://steamcommunity.com/id/ikefi/
////////////////////////////////////////////////*/

session_start();

require( 'config.php' );
require( 'data_handler.php' );

// The server id
if( !isset( $_GET[ 'id' ] ) ){ echo "No chat ID given."; die; }
$_SESSION[ 'chat_id' ] = str_replace( ' ', '_', strtolower( htmlspecialchars( $_GET[ 'id' ] ) ) );
$chat_id = $_SESSION[ 'chat_id' ];

// Name
if( isset( $_GET[ 'nick' ] ) ){
	$_SESSION[ 'chat_nick' ] = htmlspecialchars( $_GET[ 'nick' ] );
}
elseif( !isset( $_SESSION[ 'chat_nick' ] ) ){
	$_SESSION[ 'chat_nick' ] = "USER_" . mt_rand( 1000, 9999 );
}

// SteamID
if( isset( $_GET[ 'steamid' ] ) ){
	$_SESSION[ 'chat_steamid' ] = htmlspecialchars( $_GET[ 'steamid' ] );
}
elseif( !isset( $_SESSION[ 'chat_steamid' ] ) ){
	$_SESSION[ 'chat_steamid' ] = "unknown";
}

// Color
if( isset( $_GET[ 'color' ] ) ){
	$_SESSION[ 'chat_color' ] = htmlspecialchars( $_GET[ 'color' ] );
}
elseif( !isset( $_SESSION[ 'chat_color' ] ) ){
	$_SESSION[ 'chat_color' ] = "C8C8C8";
}

// Is user an admin
if( isset( $_GET[ 'admin' ] ) ){
	$_SESSION[ 'chat_admin' ] = htmlspecialchars( $_GET[ 'admin' ] ) === "true" ? 1 : 0;
}
elseif( !isset( $_SESSION[ 'chat_admin' ] ) ){
	$_SESSION[ 'chat_admin' ] = 0;
}

// Get the data
$data = get_data( $chat_id );
	
	$last_message = isset( $data->last_message ) ? $data->last_message : 0;
	
	// Clear the log file if the last connection time exceeded x amount of seconds
	if( time() - $last_message > $clear_log_time ){
		$data->last_message = time();
		$data->message_id = 0;
		$data->chat_log = "";
	}

// Save the data
save_data( $data, $chat_id );
?>
<!DOCTYPE HTML>
<html>
<head>
	<link type="text/css" rel="stylesheet" href="style/style.min.css" />
</head>
<body>
	<div class="chat-lines">
		<div id="loading">
			<img src="images/loading.gif"/>
		</div>
	</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="js/script.min.js"></script>
</body>
</html>