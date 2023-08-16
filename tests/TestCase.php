<?php

namespace Fruitcake\Cors\Tests;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Fruitcake\Cors\HandleCors;
use Fruitcake\Cors\CorsServiceProvider;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use ValidatesRequests;

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['cors'] = [
            'paths' => ['api/*'],
            'supports_credentials' => false,
            'allowed_origins' => ['http://localhost'],
            'allowed_headers' => ['X-Custom-1', 'X-Custom-2'],
            'allowed_methods' => ['GET', 'POST'],
            'exposed_headers' => [],
            'max_age' => 0,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [CorsServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var Router $router */
        $router = $app['router'];

        $this->addWebRoutes($router);
        $this->addApiRoutes($router);
    }

    /**
     * @param Router $router
     */
    protected function addWebRoutes(Router $router)
    {
        $router->post('web/ping', [
            'uses' => function () {
                return 'PONG';
            }
        ]);
    }

    /**
     * @param Router $router
     */
    protected function addApiRoutes($router)
    {
        $router->post('api/ping', [
            'uses' => function () {
                return 'PONG';
            }
        ]);

        $router->put('api/ping', [
            'uses' => function () {
                return 'PONG';
            }
        ]);

        $router->post('api/error', [
            'uses' => function () {
                abort(500);
            }
        ]);

        $router->post('api/validation', [
            'uses' => function (Request $request) {
                $this->validate($request, [
                    'name' => 'required',
                ]);

                return 'ok';
            }
        ]);
    }
}
