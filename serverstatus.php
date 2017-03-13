<?php

// We don't want any warnings
error_reporting( 0 );

if( $fp = fsockopen( htmlspecialchars( $_GET[ 'ip' ] ), htmlspecialchars( $_GET[ 'port' ] ), $errCode, $errStr, 0.8 ) ){
	echo 'online';
	fclose( $fp );
} else {
	echo 'offline';
}

?>