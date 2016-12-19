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
 * Class to create slim routes from route definitions.
 */
class Compiler
{
    /**
     * @var Slim\App
     */
    private $app;

    /**
     * @param Slim\App $app
     */
    public function __construct(Slim\App $app)
    {
        $this->app = $app;
    }

    /**
     * @param Definition[] $definitions
     *
     * @return Slim\Route[]
     */
    public function __invoke(array $definitions = null)
    {
        if ($definitions === null) {
            $container   = $this->app->getContainer();
            $definitions = $container->has('routes') ? $container->get('routes') : [];
        }
        $routes = [];
        foreach ($definitions as $pattern => $definition) {
            $definition       = is_array($definition) ? new GroupDefinition($pattern, $definition) : $definition;
            $routes[$pattern] = call_user_func($definition, $this->app);
        }
        return $routes;
    }
}