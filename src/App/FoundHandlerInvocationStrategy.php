<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

use Invoker;
use Psr;
use Slim;

/**
 * Allow router callbacks with dynamic argument lists
 *
 * @link https://github.com/PHP-DI/Invoker
 */
class FoundHandlerInvocationStrategy implements Slim\Interfaces\InvocationStrategyInterface
{
    /**
     * @var Invoker\InvokerInterface
     */
    private $invoker;

    /**
     * @param Invoker\InvokerInterface $invoker
     */
    public function __construct(Invoker\InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(
        callable $callable,
        Psr\Http\Message\ServerRequestInterface $request,
        Psr\Http\Message\ResponseInterface $response,
        array $routeArguments
    ) {
        return $this->invoker->call($callable, [
                'request'  => $request,
                'req'      => $request,
                'response' => $response,
                'res'      => $response,
            ] + $routeArguments
        );
    }
}