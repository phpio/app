<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

call_user_func(function() {
    foreach ([
            dirname(__DIR__) . '/vendor/autoload.php', // composer
            dirname(dirname(dirname(dirname(__DIR__)))) . '/vendor/autoload.php', // composer dependency
            dirname(dirname(dirname(__DIR__))) . '/autoload.php', // fallback
        ] as $file) {
        if (file_exists($file)) {
            /** @noinspection PhpIncludeInspection */
            require_once $file;
            return;
        }
    }
    die('file autoload.php not found');
});
