<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

use DI;
use Doctrine;
use Interop;
use Phpio;
use Slim;

/**
 * @property-read string                                    $appRoot
 * @property-read string                                    $appEnv
 *
 * @see DI\ContainerBuilder Configuration properties for builder. NULL guesses default values.
 *
 * @property-read null|string                               $containerClass
 * @property-read null|bool                                 $useAutowiring
 * @property-read null|bool                                 $useAnnotations
 * @property-read null|bool                                 $ignorePhpDocErrors
 * @property-read null|false|string                         $proxyDirectory
 * @property-read null|Doctrine\Common\Cache\Cache          $definitionCache
 * @property-read null|Interop\Container\ContainerInterface $wrapperContainer
 * @property-read DI\Definition\Source\DefinitionSource[]   $sources
 */
class Kernel
{
    use Phpio\Spl\WithPropertiesReadOnlyTrait;

    /**
     * @var array
     */
    protected $properties = [
        'appRoot'            => null,
        'appEnv'             => null,

        /* @see DI\ContainerBuilder */
        'containerClass'     => null,
        'useAutowiring'      => null,
        'useAnnotations'     => null,
        'ignorePhpDocErrors' => null,
        'proxyDirectory'     => null,
        'definitionCache'    => null,
        'wrapperContainer'   => null,
        'sources'            => [],
    ];

    /**
     * @return string
     */
    protected static function getDefaultAppRoot()
    {
        // installed as composer dependency
        $appRoot = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/app';
        if (!file_exists($appRoot)) {
            // git clone
            $appRoot = dirname(dirname(__DIR__)) . '/app';
        }
        return $appRoot;
    }

    /**
     * @param array $properties
     *
     * @return static
     */
    public static function fromEnvironment(array $properties = [])
    {
        if (!isset($properties['appRoot'])) {
            $properties['appRoot'] = static::getDefaultAppRoot();
        }

        $appConfig = array_merge([
            'kernel'      => function () use (&$kernel) {
                return $kernel;
            },
            static::class => DI\get('kernel'),
        ], isset($properties['app']) ? $properties['app'] : []);

        $properties = array_merge([
            'appEnv'  => getenv('PHPIO_APP_ENV') ?: EnvironmentEnumeration::PROD,
            'sources' => [new PimpleDefinitionSource(new Slim\Container($appConfig))],
        ], $properties);

        $intersection = new FilesIntersection(
            "{$properties['appRoot']}/config",
            "{$properties['appRoot']}/config/{$properties['appEnv']}",
            'php'
        );
        foreach ($intersection as $baseConfig => $extendedConfig) {
            $properties['sources'][] = $baseConfig;
            if ($extendedConfig) {
                $properties['sources'][] = $extendedConfig;
            }
        }

        // filter unsupported properties
        $properties = array_intersect_key($properties, (new static())->properties);
        $kernel     = new static($properties);
        return $kernel;
    }

    /**
     * @return DI\FactoryInterface | DI\InvokerInterface | Interop\Container\ContainerInterface
     */
    public function __invoke()
    {
        $builder = $this->createBuilder();

        foreach ($this->sources as $source) {
            $builder->addDefinitions($source);
        }

        return $builder->build();
    }

    /**
     * @return DI\ContainerBuilder
     */
    protected function createBuilder()
    {
        $builder = $this->containerClass ? new DI\ContainerBuilder($this->containerClass) : new DI\ContainerBuilder();

        if ($this->useAutowiring !== null) {
            $builder->useAutowiring($this->useAutowiring);
        }
        if ($this->useAnnotations !== null) {
            $builder->useAnnotations($this->useAnnotations);
        }
        if ($this->ignorePhpDocErrors !== null) {
            $builder->ignorePhpDocErrors($this->ignorePhpDocErrors);
        }
        if ($this->proxyDirectory !== null) {
            $builder->writeProxiesToFile((bool)$this->proxyDirectory, $this->proxyDirectory ?: null);
        }
        if ($this->definitionCache) {
            $builder->setDefinitionCache($this->definitionCache);
        }
        if ($this->wrapperContainer) {
            $builder->wrapContainer($this->wrapperContainer);
        }

        return $builder;
    }
}