<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

/**
 * @var string
 */
const GET     = 'GET';

/**
 * @var string
 */
const POST    = 'POST';

/**
 * @var string
 */
const PUT     = 'PUT';

/**
 * @var string
 */
const PATCH   = 'PATCH';

/**
 * @var string
 */
const DELETE  = 'DELETE';

/**
 * @var string
 */
const OPTIONS = 'OPTIONS';

/**
 * Create a route definition for the given http methods.
 *
 * @param array    $methods
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function map(array $methods, $pattern, $callable)
{
    return new Route\Definition($methods, $pattern, $callable);
}

/**
 * Create a route definition for the GET method.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function get($pattern, $callable)
{
    return map([GET], $pattern, $callable);
}

/**
 * Create a route definition for the POST method.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function post($pattern, $callable)
{
    return map([POST], $pattern, $callable);
}

/**
 * Create a route definition for the PUT method.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function put($pattern, $callable)
{
    return map([PUT], $pattern, $callable);
}

/**
 * Create a route definition for the GET method.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function patch($pattern, $callable)
{
    return map([PATCH], $pattern, $callable);
}

/**
 * Create a route definition for the DELETE method.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function delete($pattern, $callable)
{
    return map([DELETE], $pattern, $callable);
}

/**
 * Create a route definition for the OPTIONS method.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function options($pattern, $callable)
{
    return map([OPTIONS], $pattern, $callable);
}

/**
 * Create a route definition for all known http methods.
 *
 * @param string   $pattern
 * @param callable $callable
 *
 * @return Route\Definition
 */
function any($pattern, $callable)
{
    return map([GET, POST, PUT, PATCH, DELETE, OPTIONS], $pattern, $callable);
}
