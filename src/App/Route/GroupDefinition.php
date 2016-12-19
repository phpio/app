<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App\Route;

use Slim;

/**
 * Definition for a route group.
 */
class GroupDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var array
     */
    private $routes;

    /**
     * @param string $pattern
     * @param array  $routes
     */
    public function __construct($pattern, array $routes)
    {
        $this->pattern = $pattern;
        $this->routes  = $routes;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Slim\App $app)
    {
        $routes = $this->routes;
        return $app->group($this->pattern, function () use (&$routes, &$app) {
            foreach ($routes as $pattern => $route) {
                call_user_func(is_array($route) ? new GroupDefinition($pattern, $route) : $route, $app);
            }
        });
    }
}