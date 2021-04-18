<?php

spl_autoload_register( function ( $name ) {
	if ( strpos( $name, 'Psr\\Http\\Client\\' ) === 0 ) {
		include 'http-client' . DIRECTORY_SEPARATOR . substr( $name, 16 ) . '.php';
	} elseif ( strpos( $name, 'Psr\\Http\\Message\\' ) === 0 ) {
		include 'http-message' . DIRECTORY_SEPARATOR . substr( $name, 17 ) . '.php';
	}
} );
