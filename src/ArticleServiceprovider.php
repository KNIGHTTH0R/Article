<?php

namespace Article\Api;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
class ArticleServiceprovider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    public function boot()
    {
        $this->loadViewsFrom(realpath(__DIR__.'/../views'), 'article');
        $this->setupRoutes($this->app->router);
        // this for conig
        $this->publishes([
            __DIR__.'/config/article.php' => config_path('article.php'),
        ]);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {

        $router->group(['namespace' => 'Article\Api\Http\Controllers'], function($router)
        {
            require __DIR__.'/Http/routes.php';
        });
    }

    public function register()
    {

        $this->registerContact();
        config([
            'config/article.php',
        ]);

    }
    private function registerContact()
    {
        $this->app->bind('article',function($app){
            return new article($app);
        });
    }
}