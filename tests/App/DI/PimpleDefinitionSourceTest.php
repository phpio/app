<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App\DI;

use DI;
use Interop;
use Pimple;
use Slim;

/**
 * @covers PimpleDefinitionSource
 */
class PimpleDefinitionSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PimpleDefinitionSource::getDefinition
     */
    public function testGetDefinition()
    {
        $source = new PimpleDefinitionSource(new Pimple\Container([
            'value'   => true,
            'array'   => [],
            'factory' => function() {}
        ]));
        $this->assertInstanceOf(DI\Definition\ValueDefinition::class, $source->getDefinition('value'));
        $this->assertInstanceOf(DI\Definition\ArrayDefinition::class, $source->getDefinition('array'));
        $this->assertInstanceOf(DI\Definition\FactoryDefinition::class, $source->getDefinition('factory'));
        $this->assertNull($source->getDefinition('unknown'));
    }

    /**
     * @covers PimpleDefinitionSource::getDefinition
     */
    public function testBuilder()
    {
        $builder = new DI\ContainerBuilder();
        $builder->addDefinitions(new PimpleDefinitionSource(new Slim\Container()));
        $builder->addDefinitions([
            'replaceWith' => ['httpVersion' => 'version replaced'],
            'settings'    => DI\decorate(function(
                Slim\Collection $settings,
                Interop\Container\ContainerInterface $container
            ) {
                $settings->replace($container->get('replaceWith'));
                return $settings;
            }),
        ]);

        $settings = $builder->build()->get('settings');
        $this->assertEquals('version replaced', $settings['httpVersion']);
        $this->assertEquals(4096, $settings['responseChunkSize']);
    }
}
