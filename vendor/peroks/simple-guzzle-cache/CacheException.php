<?php namespace peroks\SimpleGuzzleCache;
use Exception;

/**
 * Interface used for all types of exceptions thrown by the implementing library.
 *
 * @copyright Per Egil Roksvaag
 * @license MIT License
 */
class CacheException extends Exception implements \Psr\SimpleCache\CacheException
{
}
