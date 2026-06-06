<?php

namespace Modules\MoodleDash\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\MoodleDash\Console\MoodleSendEncouragement;

class MoodleDashServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'MoodleDash';
    protected string $moduleNameLower = 'moodledash';

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register console commands within this module
        $this->commands([
            MoodleSendEncouragement::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        
        $migrationPath = base_path('Modules/MoodleDash/database/migrations');
        if (is_dir($migrationPath)) {
            $this->loadMigrationsFrom($migrationPath);
        }
    }

    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        $routePath = base_path('Modules/MoodleDash/routes/web.php');
        if (file_exists($routePath)) {
            $this->loadRoutesFrom($routePath);
        }
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        $viewPath = base_path('Modules/MoodleDash/resources/views');
        if (is_dir($viewPath)) {
            $this->loadViewsFrom($viewPath, $this->moduleNameLower);
        }
    }
}
