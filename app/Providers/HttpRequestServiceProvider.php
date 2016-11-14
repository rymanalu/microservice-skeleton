<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Requests\Request as FormRequest;
use Symfony\Component\HttpFoundation\Request;

class HttpRequestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureFormRequests();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Configure the form request.
     *
     * @return void
     */
    protected function configureFormRequests()
    {
        $this->app->resolving(function (FormRequest $request, $app) {
            $this->initializeRequest($request, $app['request']);

            $request->validate();
        });
    }

    /**
     * Initialize the form request with data from the given request.
     *
     * @param  \App\Http\Requests\Request  $form
     * @param  \Symfony\Component\HttpFoundation\Request  $current
     * @return void
     */
    protected function initializeRequest(FormRequest $form, Request $current)
    {
        $files = $current->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $form->initialize(
            $current->query->all(), $current->request->all(), $current->attributes->all(),
            $current->cookies->all(), $files, $current->server->all(), $current->getContent()
        );

        if ($session = $current->getSession()) {
            $form->setSession($session);
        }

        $form->setUserResolver($current->getUserResolver());

        $form->setRouteResolver($current->getRouteResolver());
    }
}
