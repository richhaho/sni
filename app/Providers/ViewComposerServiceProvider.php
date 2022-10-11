<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
          $this->composeNavigation();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    private function composeNavigation() {
        view()->composer(['admin.navigation','client.navigation','researcher.navigation'],'App\Http\Composers\NavigationComposer@notifications');
       
    }
    

}
