<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom verification email
        VerifyEmail::toMailUsing(function (object $notificable, string $url) {

            return (new MailMessage)->subject('Verify your Email Address')->line('Welcome to DigiMarket! Please verify your email to get started.')->action('Verify Email Address', $url)->line('If you did not create an account, no further action is required.')->salutation('Regards, ' . '\n' . 'The DigiMarket Team');
        });

        // Define scopes
        Passport::tokensCan([
            'super-admin' => 'Super administrator access',
            'admin' => 'Administrator access',
            'user' => 'Regular user access'
        ]);

        // Set default scope for tokens
        Passport::defaultScopes([
            'user',
        ]);

        // AuthToken configuration
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
