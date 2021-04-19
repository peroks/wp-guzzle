<?php

if ( empty( function_exists( 'GuzzleHttp\describe_type' ) ) ) {
	include_once 'guzzle' . DIRECTORY_SEPARATOR . 'functions.php';
}
if ( empty( function_exists( 'GuzzleHttp\Promise\promise_for' ) ) ) {
	include_once 'promises' . DIRECTORY_SEPARATOR . 'functions.php';
}
if ( empty( function_exists( 'GuzzleHttp\Psr7\str' ) ) ) {
	include_once 'psr7' . DIRECTORY_SEPARATOR . 'functions.php';
}

spl_autoload_register( function ( $name ) {
	if ( strpos( $name, 'GuzzleHttp\\' ) === 0 ) {
		if ( strpos( $name, 'GuzzleHttp\\Cookie\\' ) === 0 ) {
			$path = array( 'guzzle', 'Cookie', substr( $name, 18 ) . '.php' );
		} elseif ( strpos( $name, 'GuzzleHttp\\Exception\\' ) === 0 ) {
			$path = array( 'guzzle', 'Exception', substr( $name, 21 ) . '.php' );
		} elseif ( strpos( $name, 'GuzzleHttp\\Handler\\' ) === 0 ) {
			$path = array( 'guzzle', 'Handler', substr( $name, 19 ) . '.php' );
		} elseif ( strpos( $name, 'GuzzleHttp\\Promise\\' ) === 0 ) {
			$path = array( 'promises', substr( $name, 19 ) . '.php' );
		} elseif ( strpos( $name, 'GuzzleHttp\\Psr7\\' ) === 0 ) {
			$path = array( 'psr7', substr( $name, 16 ) . '.php' );
		} else {
			$path = array( 'guzzle', substr( $name, 11 ) . '.php' );
		}
		include join( DIRECTORY_SEPARATOR, $path );
	}
} );