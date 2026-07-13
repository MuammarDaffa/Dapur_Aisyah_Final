<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

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
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda - Dapur Aisyah')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di Dapur Aisyah. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email', $url)
                ->line('Jika Anda tidak merasa mendaftar akun ini, Anda tidak perlu melakukan tindakan apa pun.')
                ->salutation("Salam hormat,\nTim Dapur Aisyah");
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Pemberitahuan Reset Password - Dapur Aisyah')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
                ->action('Reset Password', $url)
                ->line('Tautan reset password ini akan kedaluwarsa dalam 60 menit.')
                ->line('Jika Anda tidak merasa meminta reset password, Anda tidak perlu melakukan tindakan apa pun.')
                ->salutation("Salam hormat,\nTim Dapur Aisyah");
        });
    }
}
