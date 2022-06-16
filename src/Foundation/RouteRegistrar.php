<?php

namespace Hemend\Api\Foundation;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function use($routes)
    {
        if(is_scalar($routes) && $routes == '*') {
            $this->all();
        }
        else if(is_array($routes)) {
            in_array('storage_link', $routes) && $this->storageLink();
        }
    }

    /**
     * All routes
     *
     * @return void
     */
    public function all()
    {
        $this->storageLink();
    }

    /**
     * Routes needed for generate storage link on server.
     *
     * @return void
     */
    public function storageLink()
    {
        $this->router->group(['middleware' => ['web']], function ($router) {
            $router->get('/storage-link', function () {
                $target = storage_path('app/public');
                $shortcut = public_path('storage');

                if(!@symlink($target, $shortcut)) {
                    $html = '<span style="color: #8d0505">Attempt to create storage folder shortcut failed.</span>';
                    return response($html, 500)->withHeaders(['Content-Type', 'text/html']);
                }

                $html = '<span style="color: #028038">Done successfully. Please disable "storage_link" from <strong>`config/api.php`</strong>.</span>';
                return response($html, 200)->withHeaders(['Content-Type', 'text/html']);
            });
        });
    }
}
