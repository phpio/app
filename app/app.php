<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'autoload.php';

return function() {
    $app = new Phpio\App();
    $app->get('/', function(Slim\Http\Response $res, $version) {
        return $res->withJson(['version' => $version]);
    });
    $app->run();
};