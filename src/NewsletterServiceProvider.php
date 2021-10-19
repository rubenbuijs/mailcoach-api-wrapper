<?php

namespace RubenBuijs\MailcoachApiWrapper;

use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/newsletter.php', 'newsletter');

        $this->publishes([
            __DIR__.'/../config/newsletter.php' => config_path('newsletter.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(Newsletter::class, function () {
            $driver = config('newsletter.driver', 'api');

            $api_token = config('newsletter.apiToken');
            $api_url = config('newsletter.baseUrl');
            $list_id = config('newsletter.listId');
            $ssl = config('newsletter.ssl');

            return new Newsletter($api_token, $api_url, $list_id, $ssl);
        });

        $this->app->alias(Newsletter::class, 'newsletter');
    }
}
