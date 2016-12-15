<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

/**
 * Environment constants
 */
interface EnvironmentEnumeration
{
    /**
     * @var string
     */
    const DEV = 'dev';

    /**
     * @var string
     */
    const PROD = 'prod';

    /**
     * @var string
     */
    const TEST = 'test';
}