<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App\DI;

use DI;
use Pimple;

/**
 * Source wrapper for the Pimple\Container
 */
class PimpleDefinitionSource implements DI\Definition\Source\DefinitionSource
{
    /**
     * @var Pimple\Container
     */
    private $container;

    /**
     * @param Pimple\Container $container
     */
    public function __construct(Pimple\Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition($name)
    {
        if (!$this->container->offsetExists($name)) {
            return null;
        }
        $source = new DI\Definition\Source\DefinitionArray([$name => $this->container->raw($name)]);
        return $source->getDefinition($name);
    }
}
