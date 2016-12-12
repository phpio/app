<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

use DI;
use Interop;

/**
 * Composites arbitrary number of containers. Searches in all containers for a service with the given name.
 */
class ContainerComposite implements Interop\Container\ContainerInterface, DI\FactoryInterface, DI\InvokerInterface
{
    /**
     * @var array[]
     */
    private $scopes = [
        Interop\Container\ContainerInterface::class => [],
        DI\FactoryInterface::class                  => [],
        DI\InvokerInterface::class                  => [],
    ];

    /**
     * @param DI\FactoryInterface|DI\InvokerInterface|Interop\Container\ContainerInterface $container
     * @param DI\FactoryInterface|DI\InvokerInterface|Interop\Container\ContainerInterface ...$container
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($container, ...$container)
    {
        foreach (func_get_args() as $i => $container) {
            $added = false;
            if ($container instanceof Interop\Container\ContainerInterface) {
                $added = $this->add(Interop\Container\ContainerInterface::class, $container);
            }
            if ($container instanceof DI\FactoryInterface) {
                $added = $this->add(DI\FactoryInterface::class, $container);
            }
            if ($container instanceof DI\InvokerInterface) {
                $added = $this->add(DI\InvokerInterface::class, $container);
            }
            if (!$added) {
                throw new \InvalidArgumentException(sprintf(
                    '%s. container must implement %s or %s or %s.',
                    $i + 1,
                    ...array_keys($this->scopes)
                ));
            }
        }
    }

    /**
     * Add container to the the scope.
     *
     * @param string                                                                       $scope
     * @param DI\FactoryInterface|DI\InvokerInterface|Interop\Container\ContainerInterface $container
     *
     * @return true
     *
     * @throws \RuntimeException
     */
    private function add($scope, $container)
    {
        if (array_search($container, $this->scopes[$scope], true) !== false) {
            throw new \RuntimeException('container already exists');
        }
        $this->scopes[$scope][] = $container;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        /** @var Interop\Container\ContainerInterface $container */
        foreach ($this->scopes[Interop\Container\ContainerInterface::class] as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }
        throw new DI\NotFoundException("No entry for {$id}.");
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        /** @var Interop\Container\ContainerInterface $container */
        foreach ($this->scopes[Interop\Container\ContainerInterface::class] as $container) {
            if ($container->has($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function make($name, array $parameters = [])
    {
        /** @var DI\FactoryInterface $container */
        foreach ($this->scopes[DI\FactoryInterface::class] as $container) {
            try {
                return $container->make($name, $parameters);
            } catch (DI\NotFoundException $ignore) {
            }
        }
        throw new DI\NotFoundException("No entry for {$name}.");
    }

    /**
     * @inheritdoc
     */
    public function call($name, array $parameters = [])
    {
        /** @var DI\InvokerInterface $container */
        foreach ($this->scopes[DI\InvokerInterface::class] as $container) {
            try {
                return $container->call($name, $parameters);
            } catch (DI\NotFoundException $ignore) {
            }
        }
        throw new DI\NotFoundException("No entry for {$name}.");
    }
}
