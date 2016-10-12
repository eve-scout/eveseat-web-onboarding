<?php

/*
 * This file is part of Onboarding SeAT Add-on.
 *
 * Copyright (c) 2016 Johnny Splunk <johnnysplunk@eve-scout.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EveScout\Seat\Web\Onboarding;

use App;

use Illuminate\Support\ServiceProvider;

use Illuminate\Foundation\Application;
use EveScout\Seat\OAuth2Server\Models\Session;
use Seat\Services\Repositories\Configuration\UserRespository;

class OnboardingServiceProvider extends ServiceProvider
{
    use UserRespository;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addMigrations();
        $this->addViews();
        $this->addRoutes($this->app);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['view']->composer('web::home', function($view)
        {
            $isAccountActive = false;
            $hasApiKeys = false;
            $hasMainCharacter = false;
            $hasVisitedForums = false;

            $isAccountActive = auth()->user()->active;

            $characters = $this->getUserCharacters(auth()->user()->id);

            if ($characters->count() > 0)
                $hasApiKeys = true;

            if (is_null(setting('main_character_name')) && $hasApiKeys)
                $hasMainCharacter = true;                

            $characterIDs = $characters->pluck('characterID');

            $oauth2Sessions = Session::join('oauth_clients', 'oauth_clients.id', '=', 'oauth_sessions.client_id')
                                ->where('oauth_clients.name', 'EVE-Scout Forums')
                                ->whereIn('owner_id', $characterIDs->all());

            if ($oauth2Sessions->count() > 0)
                $hasVisitedForums = true;

            $view->nest('full', 'eveseat-onboarding::home', compact('isAccountActive', 'hasApiKeys', 'hasMainCharacter', 'hasVisitedForums'));
        });
    }

    /**
     * Publish Migrations
     */
    public function addMigrations()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ]);
    }

    /**
     * Set the path and namespace for the views
     */
    public function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'eveseat-onboarding');
    }

    /**
     * Include the routes
     */
    public function addRoutes(Application $app)
    {
        if (!$app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }
}