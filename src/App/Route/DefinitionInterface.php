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
 * Route definition interface.
 */
interface DefinitionInterface
{
    /**
     * Create a slim route.
     *
     * @param Slim\App $app
     *
     * @return Slim\Route
     */
    public function __invoke(Slim\App $app);
}