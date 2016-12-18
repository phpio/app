<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

use Interop;
use Invoker;

/**
 * Include ParameterNameContainerResolver among others
 *
 * @see \DI\Container::getInvoker()
 */
class FoundHandlerInvoker implements Invoker\InvokerInterface
{
    /**
     * @var Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * @var Invoker\Invoker
     */
    private $invoker;

    /**
     * @param Interop\Container\ContainerInterface $container
     */
    public function __construct(Interop\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Invoker\Invoker
     */
    private function getInvoker()
    {
        if ($this->invoker) {
            return $this->invoker;
        }
        return $this->invoker = new Invoker\Invoker(
            new Invoker\ParameterResolver\ResolverChain([
                new Invoker\ParameterResolver\AssociativeArrayResolver(),
                new Invoker\ParameterResolver\Container\TypeHintContainerResolver($this->container),
                new Invoker\ParameterResolver\Container\ParameterNameContainerResolver($this->container),
                new Invoker\ParameterResolver\DefaultValueResolver(),
            ], $this->container)
        );
    }

    /**
     * @inheritdoc
     */
    public function call($callable, array $parameters = [])
    {
        return $this->getInvoker()->call($callable, $parameters);
    }
}