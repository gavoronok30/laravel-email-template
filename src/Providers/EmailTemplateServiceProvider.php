<?php

namespace Crow\LaravelEmailTemplate\Providers;

use Illuminate\Support\ServiceProvider;
use Crow\LaravelEmailTemplate\Console\Commands\EmailTemplatePublishCommand;
use Crow\LaravelEmailTemplate\Console\Commands\EmailTemplateSyncCommand;

class EmailTemplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadCustomCommands();
        $this->loadCustomConfig();
        $this->loadCustomPublished();
    }

    private function loadCustomCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    EmailTemplateSyncCommand::class,
                    EmailTemplatePublishCommand::class,
                ]
            );
        }
    }

    private function loadCustomConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/email_template.php', 'email_template');
    }

    private function loadCustomPublished()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../../config' => base_path('config')
                ],
                'config'
            );
            $this->publishes(
                [
                    __DIR__ . '/../../migration' => database_path('migrations')
                ],
                'migration'
            );
            $this->publishes(
                [
                    __DIR__ . '/../../lang' => lang_path()
                ],
                'lang'
            );
        }
    }
}
