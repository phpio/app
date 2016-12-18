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
    $app->get('/test', function(Slim\Http\Request $req, Slim\Http\Response $res) {
        return $res->withJson(['it' => 'works']);
    });
    $app->run();
};