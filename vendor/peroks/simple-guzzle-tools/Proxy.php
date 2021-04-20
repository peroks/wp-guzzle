<?php namespace peroks\SimpleGuzzleTools;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

/**
 * A very simple Guzzle proxy middleware forwarding requests to another location.
 *
 * @see https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html
 *
 * @copyright Per Egil Roksvaag
 * @license MIT License
 * @version 0.1.0
 */
class Proxy
{
	/**
	 * @var object An object of configuration options.
	 */
	protected object $options;

	/**
	 * Constructor.
	 *
	 * @param array|object $options An array or object of configuration options (key/value pairs).
	 */
	public function __construct( $options = array() ) {
		$this->options = (object) $options;
	}

	/**
	 * Called by Guzzle's handler stack.
	 *
	 * @param callable $next The next handler to invoke.
	 * @return callable A function accepting a RequestInterface instance.
	 */
	public function __invoke( callable $next ) {
		return function ( RequestInterface $request, array $options ) use ( $next ) {
			$new_base = $this->options->base_uri      ?? null;
			$old_base = (string) $options['base_uri'] ?? null;

			if ( $new_base && $old_base && strcmp( $new_base, $old_base ) ) {
				$old_uri = $request->getUri();
				$new_uri = str_ireplace( $old_base, $new_base, $old_uri );
				$request = $request->withUri( new Uri( $new_uri ) );
			}

			return $next( $request, $options );
		};
	}
}