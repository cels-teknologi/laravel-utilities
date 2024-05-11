<?php

namespace Cels\Utilities;

use Cels\Utilities\Services\FontAwesome;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Blade::directive('constants', fn ($_) => (
            "<?php echo (string) app('".FontAwesome::class."') ?>"
        ));
    }

    /**
     * Register the service provider.
     */
    public function register()
    {

    }
}
