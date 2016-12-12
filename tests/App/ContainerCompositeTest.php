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
 * @covers ContainerComposite
 */
class ContainerCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ContainerComposite::__construct
     * @covers ContainerComposite::add
     */
    public function testConstruct()
    {
        /** @var Interop\Container\ContainerInterface $container */
        $container = $this->prophesize(Interop\Container\ContainerInterface::class)->reveal();

        /** @var DI\FactoryInterface $factory */
        $factory = $this->prophesize(DI\FactoryInterface::class)->reveal();

        /** @var DI\InvokerInterface $invoker */
        $invoker = $this->prophesize(DI\InvokerInterface::class)->reveal();

        try {
            new ContainerComposite($container, $factory, $invoker, null);
            $this->fail();
        } catch (\InvalidArgumentException $exception) {
            $this->assertEquals(
                '4. container must implement Interop\Container\ContainerInterface or DI\FactoryInterface or DI\InvokerInterface.',
                $exception->getMessage()
            );
        }

        try {
            new ContainerComposite($container, $factory, $invoker, $container);
            $this->fail();
        } catch (\RuntimeException $exception) {
            $this->assertEquals('container already exists', $exception->getMessage());
        }

        new ContainerComposite($container, $factory, $invoker);
    }

    /**
     * @covers ContainerComposite::has
     * @covers ContainerComposite::get
     */
    public function testGetAndSet()
    {
        /** @var Interop\Container\ContainerInterface | \Prophecy\Prophecy\ObjectProphecy $c1 */
        $c1 = $this->prophesize(Interop\Container\ContainerInterface::class);
        $c1->has('unknown')->willReturn(false);
        $c1->has('service')->willReturn(true);
        $c1->get('service')->willReturn('c1');

        $composite = new ContainerComposite($c1->reveal());
        $this->assertFalse($composite->has('unknown'));
        $this->assertTrue($composite->has('service'));
        $this->assertEquals('c1', $composite->get('service'));
        try {
            $composite->get('unknown');
            $this->fail();
        } catch(DI\NotFoundException $exception) {
            $this->assertEquals('No entry for unknown.', $exception->getMessage());
        }

        /** @var Interop\Container\ContainerInterface | \Prophecy\Prophecy\ObjectProphecy $c2 */
        $c2 = $this->prophesize(Interop\Container\ContainerInterface::class);
        $c2->has('unknown')->willReturn(true);
        $c2->get('unknown')->willReturn('unknown');
        $c2->has('service')->willReturn(true);
        $c2->get('service')->willReturn('c2');

        $composite = new ContainerComposite($c1->reveal(), $c2->reveal());
        $this->assertTrue($composite->has('unknown'));
        $this->assertEquals('unknown', $composite->get('unknown'));
        $this->assertEquals('c1', $composite->get('service'));

        $composite = new ContainerComposite($c2->reveal(), $c1->reveal());
        $this->assertTrue($composite->has('unknown'));
        $this->assertEquals('unknown', $composite->get('unknown'));
        $this->assertEquals('c2', $composite->get('service'));
    }

    /**
     * @covers ContainerComposite::make
     */
    public function testMake()
    {
        /** @var DI\FactoryInterface | \Prophecy\Prophecy\ObjectProphecy $f1 */
        $f1 = $this->prophesize(DI\FactoryInterface::class);
        $f1->make('method', ['arg'])->willReturn('f1');
        $f1->make('unknown', [])->willThrow(new DI\NotFoundException());

        $composite = new ContainerComposite($f1->reveal());
        $this->assertEquals('f1', $composite->make('method', ['arg']));

        try {
            $composite->make('unknown');
            $this->fail();
        } catch(DI\NotFoundException $exception) {
            $this->assertEquals('No entry for unknown.', $exception->getMessage());
        }

        /** @var DI\FactoryInterface | \Prophecy\Prophecy\ObjectProphecy $f2 */
        $f2 = $this->prophesize(DI\FactoryInterface::class);
        $f2->make('method', ['arg'])->willReturn('f2');
        $f2->make('unknown', [])->willReturn('unknown');

        $composite = new ContainerComposite($f1->reveal(), $f2->reveal());
        $this->assertEquals('f1', $composite->make('method', ['arg']));
        $this->assertEquals('unknown', $composite->make('unknown'));

        $composite = new ContainerComposite($f2->reveal(), $f1->reveal());
        $this->assertEquals('f2', $composite->make('method', ['arg']));
        $this->assertEquals('unknown', $composite->make('unknown'));
    }

    /**
     * @covers ContainerComposite::call
     */
    public function testCall()
    {
        /** @var DI\InvokerInterface | \Prophecy\Prophecy\ObjectProphecy $i1 */
        $i1 = $this->prophesize(DI\InvokerInterface::class);
        $i1->call('method', ['arg'])->willReturn('i1');
        $i1->call('unknown', [])->willThrow(new DI\NotFoundException());

        $composite = new ContainerComposite($i1->reveal());
        $this->assertEquals('i1', $composite->call('method', ['arg']));

        try {
            $composite->call('unknown');
            $this->fail();
        } catch(DI\NotFoundException $exception) {
            $this->assertEquals('No entry for unknown.', $exception->getMessage());
        }

        /** @var DI\InvokerInterface | \Prophecy\Prophecy\ObjectProphecy $i2 */
        $i2 = $this->prophesize(DI\InvokerInterface::class);
        $i2->call('method', ['arg'])->willReturn('i2');
        $i2->call('unknown', [])->willReturn('unknown');

        $composite = new ContainerComposite($i1->reveal(), $i2->reveal());
        $this->assertEquals('i1', $composite->call('method', ['arg']));
        $this->assertEquals('unknown', $composite->call('unknown'));

        $composite = new ContainerComposite($i2->reveal(), $i1->reveal());
        $this->assertEquals('i2', $composite->call('method', ['arg']));
        $this->assertEquals('unknown', $composite->call('unknown'));
    }
}
