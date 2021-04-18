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
			$dir = 'guzzle' . DIRECTORY_SEPARATOR . 'Cookie' . DIRECTORY_SEPARATOR;
			include $dir . substr( $name, 18 ) . '.php';
		} elseif ( strpos( $name, 'GuzzleHttp\\Exception\\' ) === 0 ) {
			$dir = 'guzzle' . DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR;
			include $dir . substr( $name, 21 ) . '.php';
		} elseif ( strpos( $name, 'GuzzleHttp\\Handler\\' ) === 0 ) {
			$dir = 'guzzle' . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR;
			include $dir . substr( $name, 19 ) . '.php';
		} elseif ( strpos( $name, 'GuzzleHttp\\Promise\\' ) === 0 ) {
			include 'promises' . DIRECTORY_SEPARATOR . substr( $name, 19 ) . '.php';
		} elseif ( strpos( $name, 'GuzzleHttp\\Psr7\\' ) === 0 ) {
			include 'psr7' . DIRECTORY_SEPARATOR . substr( $name, 16 ) . '.php';
		} else {
			include 'guzzle' . DIRECTORY_SEPARATOR . substr( $name, 11 ) . '.php';
		}
	}
} );