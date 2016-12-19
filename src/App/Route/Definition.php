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
 * Definition for a simple route.
 */
class Definition implements DefinitionInterface
{
    /**
     * @var string[]
     */
    private $methods;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @param string[] $methods
     * @param string   $pattern
     * @param callable $callable
     */
    public function __construct(array $methods, $pattern, callable $callable)
    {
        $this->methods  = $methods;
        $this->pattern  = $pattern;
        $this->callable = $callable;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(Slim\App $app)
    {
        return $app->map($this->methods, $this->pattern, $this->callable);
    }
}