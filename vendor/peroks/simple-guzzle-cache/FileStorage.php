<?php namespace peroks\SimpleGuzzleCache;

use DateInterval;
use DirectoryIterator;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * A very simple PSR-16 caching implementation for storing http responsens on disk.
 *
 * @see https://www.php-fig.org/psr/psr-16/
 *
 * @copyright Per Egil Roksvaag
 * @license MIT License
 */
class FileStorage implements CacheInterface
{
	/**
	 * @var string The cache storage directory.
	 */
	protected string $dir;

	/**
	 * @var object An object of configuration options.
	 */
	protected object $options;

	/**
	 * Constructor.
	 *
	 * @param string $dir The cache storage directory.
	 * @param array|object $options An array or object of configuration options (key/value pairs).
	 */
	public function __construct( $dir, $options = array() ) {
		$this->dir     = rtrim( $dir, '/' . DIRECTORY_SEPARATOR );
		$this->options = (object) $options;
		$this->init();
	}

	/**
	 * Creates the cache storage directory if neccesary.
	 */
	public function init() {
		file_exists( $this->dir ) || mkdir( $this->dir, 0775, true );
	}

	/**
	 * Fetches a value from the cache.
	 *
	 * @param string $key The unique key of this item in the cache.
	 * @param mixed $default Default value to return if the key does not exist.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
	 * @return ResponseInterface The value of the item from the cache, or $default in case of cache miss.
	 */
	public function get( $key, $default = null ) {
		$this->validateKey( $key );
		$file = $this->dir . DIRECTORY_SEPARATOR . $key;

		if ( is_readable( $file ) && $modified = filemtime( $file ) ) {
			$content  = unserialize( file_get_contents( $file ) );
			$stream   = Utils::streamFor( $content->body );
			$response = $content->response->withBody( $stream );

			return $response->withAddedHeader( 'Cached', $modified );
		}
		return $default;
	}

	/**
	 * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
	 *
	 * @param string $key The key of the item to store.
	 * @param ResponseInterface $response The value of the item to store, must be serializable.
	 * @param DateInterval|int|null $ttl Optional. The TTL value of this item.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
	 * @return bool True on success and false on failure.
	 */
	public function set( $key, $response, $ttl = null ) {
		$this->validateKey( $key );
		$file = $this->dir . DIRECTORY_SEPARATOR . $key;
		$body = (string) $response->getBody();

		$response->getBody()->rewind();
		$content = serialize( (object) compact( 'response', 'body' ) );

		return (bool) file_put_contents( $file, $content );
	}

	/**
	 * Delete an item from the cache by its unique key.
	 *
	 * @param string $key The unique cache key of the item to delete.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
	 * @return bool True if the item was successfully removed. False if there was an error.
	 */
	public function delete( $key ) {
		$this->validateKey( $key );
		$file = $this->dir . DIRECTORY_SEPARATOR . $key;

		return is_writable( $file ) && unlink( $file );
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear() {
		foreach ( new DirectoryIterator( $this->dir ) as $file ) {
			empty( $file->isDot() ) && unlink( $file->getPathname() );
		}
		return true;
	}

	/**
	 * Obtains multiple cache items by their unique keys.
	 *
	 * @param iterable $keys A list of keys that can be obtained in a single operation.
	 * @param mixed $default Default value to return for keys that do not exist.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if $keys is neither an array nor a Traversable.
	 * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
	 */
	public function getMultiple( $keys, $default = null ) {
		$this->validateMultiple( $keys );
		$result = array();

		foreach ( $keys as $key ) {
			$result[ $key ] = $this->get( $key, $default );
		}

		return $result;
	}

	/**
	 * Persists a set of key => value pairs in the cache, with an optional TTL.
	 *
	 * @param iterable $values A list of key => value pairs for a multiple-set operation.
	 * @param DateInterval|int|null $ttl Optional. The TTL value of this item.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if $values is neither an array nor a Traversable,
	 * @return bool True on success and false on failure.
	 */
	public function setMultiple( $values, $ttl = null ) {
		$this->validateMultiple( $values );
		$result = array();

		foreach ( $values as $key => $response ) {
			$result[ $key ] = $this->set( $key, $response, $ttl );
		}

		return (bool) array_filter( $result );
	}

	/**
	 * Deletes multiple cache items in a single operation.
	 *
	 * @param iterable $keys A list of string-based keys to be deleted.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if $keys is neither an array nor a Traversable.
	 * @return bool True if the items were successfully removed. False if there was an error.
	 */
	public function deleteMultiple( $keys ) {
		$this->validateMultiple( $keys );
		$result = array();

		foreach ( $keys as $key ) {
			$result[ $key ] = $this->delete( $key );
		}

		return (bool) array_filter( $result );
	}

	/**
	 * Determines whether an item is present in the cache.
	 *
	 * NOTE: It is recommended that has() is only to be used for cache warming type purposes
	 * and not to be used within your live applications operations for get/set, as this method
	 * is subject to a race condition where your has() will return true and immediately after,
	 * another script can remove it making the state of your app out of date.
	 *
	 * @param string $key The cache item key.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
	 * @return bool
	 */
	public function has( $key ) {
		$file = $this->dir . DIRECTORY_SEPARATOR . $key;
		return is_readable( $file );
	}

	/**
	 * Validates the cache key.
	 *
	 * @param string $key A cache key.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException if the $key string is not a legal value.
	 * @return bool
	 */
	public function validateKey( $key ) {
		if ( is_string( $key ) ) {
			return true;
		}

		$message = 'The key must be a string';
		throw new InvalidArgumentException( $message );
	}

	/**
	 * Validates the multiple cache keys or values.
	 *
	 * @param iterable $list A list of string-based keys or key/value pairs.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException if $list is neither an array nor a Traversable.
	 * @return bool
	 */
	public function validateMultiple( $list ) {
		if ( is_iterable( $list ) ) {
			return true;
		}

		$message = 'The list of keys or key/value pairs must be an array or a Traversable';
		throw new InvalidArgumentException( $message );
	}
}