<?php
/*////////////////////////////////////////////////
	Script made by: ikefi
		http://steamcommunity.com/id/ikefi/
////////////////////////////////////////////////*/

// This is not being used

$emoticons_twitch_global = file_get_contents( 'https://twitchemotes.com/api_cache/v2/global.json' );
$emoticons_twitch_subscribers = file_get_contents( 'https://twitchemotes.com/api_cache/v2/subscriber.json' );
$emoticons_betterttv = file_get_contents( 'https://api.betterttv.net/emotes' );

$emoticon_sets = [];

// Twitch global emotes
$json = json_decode( $emoticons_twitch_global );
$array = [ 'url' => $json->template->small, 'emotes' => [] ];
foreach( $json->emotes as $code=>$emote ){
	$array[ 'emotes' ][ $code ] = $emote->image_id . "";
}
$emoticon_sets[ 'twitch_global' ] = $array;

// Twitch subscriber emotes
$json = json_decode( $emoticons_twitch_subscribers );
$array = [ 'url' => $json->template->small, 'emotes' => [] ];
foreach( $json->channels as $channel ){
	foreach( $channel->emotes as $emote ){
		$array[ 'emotes' ][ $emote->code ] = $emote->image_id . "";
	}
}
$emoticon_sets[ 'twitch_subscribers' ] = $array;

// Betterttv emotes
$json = json_decode( $emoticons_betterttv );
$array = [ 'url' => '//cdn.betterttv.net/emote/{image_id}/1x', 'emotes' => [] ];
foreach( $json->emotes as $emote ){
	$array[ 'emotes' ][ $emote->regex ] = preg_replace( array( "|//cdn.betterttv.net/emote/|", "|/1x|" ), "", $emote->url );
}
$emoticon_sets[ 'betterttv' ] = $array;


$emoticon_sets_json = stripslashes( json_encode( $emoticon_sets ) );

$file = fopen( "./data/emoticons.json", 'w' );
	fwrite( $file, $emoticon_sets_json );
fclose( $file );

?>