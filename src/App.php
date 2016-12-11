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
 */
class App extends Slim\App
{
    /**
     * @param Phpio\App\Kernel|null $kernel
     */
    public function __construct(Phpio\App\Kernel $kernel = null)
    {
        parent::__construct(call_user_func($kernel ? : Phpio\App\Kernel::fromEnvironment()));
    }
}