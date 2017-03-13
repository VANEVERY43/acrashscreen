<?php
/*////////////////////////////////////////////////
	Script made by: ikefi
		http://steamcommunity.com/id/ikefi/
////////////////////////////////////////////////*/

function get_data( $chat_id ){
	if( $file = @fopen( "./data/data_$chat_id.json", 'r' ) ){
		$data = json_decode( stream_get_contents( $file ) );
	fclose( $file );}
	if( !isset( $data ) ){ $data = new stdClass; }
	return $data;
}

function save_data( $data, $chat_id ){
	if( $file = fopen( "./data/data_$chat_id.json", 'w' ) ){
		fwrite( $file, json_encode( $data ) );
	fclose( $file );}
}
?>