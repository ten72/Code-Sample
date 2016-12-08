<?php 
	// Encodes all request parameters
	foreach( array_keys( $_REQUEST ) as $key ){
		$_REQUEST[$key] = htmlentities( $_REQUEST[$key] );
	}
	foreach( array_keys( $_POST ) as $key ){
		$_POST[$key]	= htmlentities( $_POST[$key] );
	}
	foreach( array_keys( $_GET ) as $key ){
		$_GET[$key]		= htmlentities( $_GET[$key] );
	}
	
	// Encodes the request URL
	$_SERVER[REQUEST_URI]	= urlencode( $_SERVER[REQUEST_URI] );
if($secure){
	if ($_SERVER['SERVER_PORT']!=443) 
	{ 
	$url = "https://". $_SERVER['SERVER_NAME'] . ":443".$_SERVER['REQUEST_URI']; 
	header("Location: $url"); 
	}
}else{
	if ($_SERVER['SERVER_PORT']!=80) 
	{ 
	$url = "http://". $_SERVER['SERVER_NAME'] . ":80".$_SERVER['REQUEST_URI']; 
	header("Location: $url"); 
	} 
}
?>