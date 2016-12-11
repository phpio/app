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
 * @property-read string                                                  $environment
 *
 * @see DI\ContainerBuilder Configuration properties for builder. NULL guesses default values.
 *
 * @property-read null|string                                             $containerClass
 * @property-read null|bool                                               $useAutowiring
 * @property-read null|bool                                               $useAnnotations
 * @property-read null|bool                                               $ignorePhpDocErrors
 * @property-read null|false|string                                       $proxyDirectory
 * @property-read null|Doctrine\Common\Cache\Cache                        $definitionCache
 * @property-read null|string|array|DI\Definition\Source\DefinitionSource $definitions
 * @property-read null|Interop\Container\ContainerInterface               $wrapperContainer
 */
class Kernel
{
    use Phpio\Spl\WithPropertiesReadOnlyTrait;

    /**
     * @var array
     */
    protected $properties = [
        'environment'        => null,

        /* @see DI\ContainerBuilder */
        'containerClass'     => null,
        'useAutowiring'      => null,
        'useAnnotations'     => null,
        'ignorePhpDocErrors' => null,
        'proxyDirectory'     => null,
        'definitionCache'    => null,
        'definitions'        => null,
        'wrapperContainer'   => null,
    ];

    /**
     * @param array $properties
     *
     * @return static
     */
    public static function fromEnvironment(array $properties = [])
    {
        // @todo implement
//        (new DI\ContainerBuilder())->addDefinitions([
//            'kernel.environment' => DI\env('environment', EnvironmentEnumeration::PROD),
//            'kernel.config'      => DI\env('environment', EnvironmentEnumeration::PROD),
//        ])->build()->get('environment');
        $defaults = [
//            'wrapperContainer' => new Slim\Container(),
        ];

        return new static(array_merge($defaults, $properties));
    }

    /**
     * @return DI\Container
     */
    public function __invoke()
    {
        $builder = $this->createBuilder();

        if ($this->definitions) {
            $builder->addDefinitions($this->definitions);
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