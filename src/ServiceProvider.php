<?php

namespace AniketMagadum\LogPlus;

use AniketMagadum\LogPlus\Http\Controllers\CP\LogPlusController;
use Statamic\Facades\Utility;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $scripts = [
        __DIR__.'/../resources/js/log-plus.js'
    ];
    public function bootAddon()
    {
        // TODO: Add Timezone Support
        $this->loadViewsFrom(__DIR__."../resources/views","log-plus");
        
        Utility::extend(function(){
            Utility::register('log-plus')
            ->action([LogPlusController::class, 'index'])
            ->title(__('Log Plus'))
            ->icon('entries')
            ->navTitle(__('Logs'))
            ->routes(function ($router) {
                $router->delete('/{file}', [LogPlusController::class, 'delete'])->name('delete');
            })
            ->description('Check your laravel log files'); //->description(__('statamic::messages.cache_utility_description')) TODO: Make this field dynamic later
            //->docsUrl(Statamic::docsUrl('utilities/cache-manager')) TODO: Add this later
            // ->routes(function ($router) {
            //     $router->post('cache/{cache}', [CacheController::class, 'clear'])->name('clear');
            //     $router->post('cache/{cache}/warm', [CacheController::class, 'warm'])->name('warm');
            // });
        });
    }
}
