<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio;
use Phpio;
use Slim;

/**
 * @property-read Phpio\App\Kernel $kernel
 */
class App extends Slim\App
{
    /**
     * @param Phpio\App\Kernel|array|null $kernel
     */
    public function __construct($kernel = null)
    {
        $kernel = $kernel ?: [];
        if (is_array($kernel)) {
            $kernel = Phpio\App\Kernel::fromEnvironment($kernel);
        }
        parent::__construct($kernel());
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getContainer()->get($name);
    }
}