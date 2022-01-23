<?php
/**
 * Updated by Leestarch at 7/15/20 6:43 PM
 */

/**
 * Updated by Leestarch at 7/2/20 4:10 AM
 */

/**
 * Updated by Leestarch at 6/30/20 5:09 AM
 */

namespace App\Providers;

use App\Models\Access;
use App\Models\Client;
use App\Models\ClientClaim;
use App\Models\Favourite;
use App\Models\Journal;
use App\Models\Option;
use App\Models\ProductCategory;
use App\Models\User;
use App\Observers\ClientObserver;
use App\Observers\UserObserver;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Keyboard\Keyboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
    }
}
