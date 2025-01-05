<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        // Instead of using require_once, use loadRoutesFrom to load the channels route
        $this->loadRoutesFrom(base_path('routes/channels.php'));
    }
}
