<?php

/**
 * Autoloader.
 *
 * @copyright Per Egil Roksvaag
 * @license MIT License
 */
spl_autoload_register( function ( $name ) {
	if ( strpos( $name, 'peroks\\SimpleGuzzleCache\\' ) === 0 ) {
		$path = array( 'simple-guzzle-cache', substr( $name, 25 ) . '.php' );
		include join( DIRECTORY_SEPARATOR, $path );
	} elseif ( strpos( $name, 'peroks\\SimpleGuzzleTools\\' ) === 0 ) {
		$path = array( 'simple-guzzle-tools', substr( $name, 25 ) . '.php' );
		include join( DIRECTORY_SEPARATOR, $path );
	}
} );