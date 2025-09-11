<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SlotJsonDelivery\SlotJsonDeliveryStrategy;
use App\Services\SlotJsonDelivery\LocalFileStrategy;
use App\Services\SlotJsonDelivery\RemoteApiStrategy;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
        $this->app->bind(
            \App\Services\SlotJsonDelivery\SlotJsonDeliveryStrategy::class, 
            \App\Services\SlotJsonDelivery\LocalFileStrategy::class
        );
        */
        $this->app->bind(SlotJsonDeliveryStrategy::class, function ($app) {
            $strategy = config('slot_json.delivery_strategy', 'local');
            
            return match ($strategy) {
                'local' => new LocalFileStrategy(),
                'remote_api' => new RemoteApiStrategy(),
                default => throw new InvalidArgumentException("Unknown strategy: {$strategy}")
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
