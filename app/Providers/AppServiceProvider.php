<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Vercel/Serverless Specific Fix: Configure paths early in the bootstrap process
        // This MUST run before ViewServiceProvider tries to resolve the view compiler
        $isVercel = isset($_ENV['VERCEL_ENV']) || isset($_ENV['VERCEL']) || getenv('VERCEL') || getenv('VERCEL_ENV');
        
        if ($isVercel) {
            $storagePath = '/tmp/storage';
            
            // Set storage path IMMEDIATELY - this must happen before any other service providers
            // try to resolve dependencies that depend on storage paths
            $this->app->useStoragePath($storagePath);
            
            // Set view compiled path and other cache paths early
            // Config should be bound by this point (ConfigServiceProvider registers early)
            try {
                $this->app['config']->set('view.compiled', $storagePath . '/framework/views');
                $this->app['config']->set('cache.stores.file.path', $storagePath . '/framework/cache/data');
                $this->app['config']->set('cache.stores.file.lock_path', $storagePath . '/framework/cache/data');
                $this->app['config']->set('session.files', $storagePath . '/framework/sessions');
                $this->app['config']->set('logging.channels.single.path', $storagePath . '/logs/laravel.log');
            } catch (\Exception $e) {
                // If config isn't bound yet, it will be set in bootstrap/app.php after create()
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
