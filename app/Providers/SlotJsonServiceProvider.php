<?php
declare(strict_types=1);
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SlotJsonDelivery\SlotJsonDeliveryStrategy;
use App\Services\SlotJsonDelivery\LocalFileStrategy;
use App\Services\SlotJsonDelivery\RemoteApiStrategy;
use InvalidArgumentException;

class SlotJsonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the delivery strategy based on configuration
        $this->app->bind(SlotJsonDeliveryStrategy::class, function ($app) {
            $strategy = config('slot_json.delivery_strategy', 'local');
            
            return match ($strategy) {
                'local' => new LocalFileStrategy(),
                'remote_api' => new RemoteApiStrategy(),
                default => throw new InvalidArgumentException("Unknown slot JSON delivery strategy: {$strategy}")
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() && function_exists('config_path')) {
            $this->publishes([
                __DIR__.'/../../config/slot_json.php' => config_path('slot_json.php'),
            ], 'slot-json-config');
        }
    }   
}
?>