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
        $this->router->get('/storage-link', function () {
            $source = storage_path('app/public');
            $target = public_path('storage');

            if(@readlink($target)) {
              $html = '<span style="color: #fa7504;font-size: 20px">This link has already been created. Please disable "storage_link" from <strong>`config/api.php`</strong>.</span>';
              return response($html, 500)->withHeaders(['Content-Type', 'text/html']);
            }

            if(!@symlink($source, $target) || !@readlink($target)) {
                $html = '<span style="color: #8d0505;font-size: 20px">Attempt to create storage folder shortcut failed.</span>';
                return response($html, 500)->withHeaders(['Content-Type', 'text/html']);
            }

            $html = '<span style="color: #028038;font-size: 20px">Done successfully. Please disable "storage_link" from <strong>`config/api.php`</strong>.</span>';
            return response($html, 200)->withHeaders(['Content-Type', 'text/html']);
        });
    }
}
