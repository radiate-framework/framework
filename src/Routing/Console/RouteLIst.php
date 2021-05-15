<?php

namespace Radiate\Routing\Console;

use Radiate\Console\Command;
use Radiate\Routing\Route;
use Radiate\Routing\Router;

class RouteList extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'route:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered routes';

    /**
     * The router instance.
     *
     * @var \Radiate\Routing\Router
     */
    protected $router;

    /**
     * The route table headers
     *
     * @var array
     */
    protected $headers = ['Method', 'URI', 'Name', 'Action', 'Middleware'];

    /**
     * Create a new command instance.
     *
     * @param \Radiate\Routing\Router $router
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->getRoutes())) {
            return $this->error("Your application doesn't have any routes.");
        }

        $this->table($this->getHeaders(), $this->getRoutes());
    }

    /**
     * Get the table headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->router->getRoutes()->map(function ($route) {
            return $this->getRouteInformation($route);
        })->filter()->all();
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Radiate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return [
            'Method'     => implode('|', $route->methods()),
            'URI'        => $route->uri(),
            'Name'       => $route->getName(),
            'Action'     => $route->getActionName(),
            'Middleware' => implode(', ', $route->middleware()),
        ];
    }
}
