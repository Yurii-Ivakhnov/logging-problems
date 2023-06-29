<?php

namespace Corpsoft\Logging;

use Corpsoft\Logging\Actions\JobFailedAction;
use Corpsoft\Logging\Actions\GeneralLogAction;
use Corpsoft\Logging\Actions\QueryingForLongerThanAction;
use Corpsoft\Logging\Exceptions\LoggingExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * - bootstrap web services
     * - listen for events
     * - publish configuration files or databases migrations
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPublishables();

        if ($this->app->runningInConsole()) {
            $this->registerPackageServiceProvider();
        }

        $this->registerTrackingActions();
    }

    /**
     * Register any application services.
     * - extend func from other classes
     * - register service providers
     * - create singleton classes
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/logging-problems.php', 'logging-problems');

        $this->app->singleton(
            ExceptionHandler::class,
            LoggingExceptionHandler::class
        );

        $this->app->singleton(GeneralLogAction::class, function () {
            return new GeneralLogAction();
        });

        $this->app->singleton(JobFailedAction::class, function () {
            return new JobFailedAction();
        });
    }

    /**
     * Register Tracking actions
     * @return void
     */
    protected function registerTrackingActions(): void
    {
        if (
            config('logging-problems')['log_slack_webhook_url']
            && app()->environment(config('logging-problems')['enable_in_environment'])
        ) {
            $this->jobFailedTracking();
            $this->queryingForLongerThanTracking();
        }
    }


    /**
     * Observe Failed Job and Send Slack Notification
     * @return void
     */
    protected function jobFailedTracking(): void
    {
        $action = new JobFailedAction();
        $action();
    }

    /**
     * Observe Failed Job and Send Slack Notification
     * @return void
     */
    protected function queryingForLongerThanTracking(): void
    {
        $action = new QueryingForLongerThanAction();
        $action();
    }

    /**
     * Register publish files
     * @return void
     */
    protected function registerPublishables(): void
    {
        if (!$this->app->runningInConsole()) return;

        $this->publishes([
            __DIR__ . '/../config/logging-problems.php' => config_path('logging-problems.php'),
        ], 'logging');
    }

    /**
     * Register package service providers
     * @return void
     */
    protected function registerPackageServiceProvider(): void
    {
        $laravelConfig = $this->app['config']->get('app.providers', []);

        $packageProviders = $this->app['config']->get('logging.providers', []);

        $providersToAdd = array_diff($packageProviders, $laravelConfig);

        $providers = array_merge($laravelConfig, $providersToAdd);

        $this->app['config']->set('app.providers', $providers);
    }
}
