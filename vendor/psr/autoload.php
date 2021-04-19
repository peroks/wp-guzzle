<?php

spl_autoload_register( function ( $name ) {
	if ( strpos( $name, 'Psr\\' ) === 0 ) {
		if ( strpos( $name, 'Psr\\Http\\Client\\' ) === 0 ) {
			include 'http-client' . DIRECTORY_SEPARATOR . substr( $name, 16 ) . '.php';
		} elseif ( strpos( $name, 'Psr\\Http\\Message\\' ) === 0 ) {
			include 'http-message' . DIRECTORY_SEPARATOR . substr( $name, 17 ) . '.php';
		} elseif ( strpos( $name, 'Psr\\SimpleCache\\' ) === 0 ) {
			include 'simple-cache' . DIRECTORY_SEPARATOR . substr( $name, 16 ) . '.php';
		}
	}
} );
