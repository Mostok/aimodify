<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Configuration;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->environment() === 'production'){             
            $this->app['request']->server->set('HTTPS', true);        
        }

        URL::forceScheme('https');

        Validator::extend('mp3_extension', function($attribute, $value, $parameters, $validator) {
            return (!empty($value->getClientOriginalExtension()) && ($value->getClientOriginalExtension() == 'mp3'));
        });
        
        // Configurations
        if (php_sapi_name() !== 'cli')
            Configuration::reload();
        
            Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
                $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
    
                return new LengthAwarePaginator(
                    $this->forPage($page, $perPage),
                    $total ?: $this->count(),
                    $perPage,
                    $page,
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'pageName' => $pageName,
                    ]
                );
            });


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
