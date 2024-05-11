<?php

namespace Cels\Utilities;

use Cels\Utilities\Services\FontAwesome;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/cels-utilities.php' => App::configPath('cels-utilities.php'),
        ], 'cels-utilities-config');

        Blade::directive('fontawesome', fn ($_) => (
            "<?php echo (string) app('".FontAwesome::class."') ?>"
        ));
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cels-utilities.php', 'cels-utilities');
    }
}
