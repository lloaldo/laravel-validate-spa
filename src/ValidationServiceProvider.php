<?php

namespace Lloaldo\LaravelValidateSpa;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $rules = [
            'spanish_tax_number' => [ValidationRules::class, 'validateTaxNumber'],
            'spanish_personal_id' => [ValidationRules::class, 'validatePersonalIdNumber'],
            'spanish_nif' => [ValidationRules::class, 'validateSpanishNif'],
            'spanish_nie' => [ValidationRules::class, 'validateSpanishNie'],
            'spanish_cif' => [ValidationRules::class, 'validateSpanishCif'],
            'spanish_ssn' => [ValidationRules::class, 'validateSpanishSsn'],
            'spanish_iban' => [ValidationRules::class, 'validateSpanishIban'],
            'spanish_postal_code' => [ValidationRules::class, 'validateSpanishPostalCode'],
            'spanish_phone' => [ValidationRules::class, 'validateSpanishPhone'],
            'spanish_license_plate' => [ValidationRules::class, 'validateSpanishLicensePlate'],
            'spanish_ccc' => [ValidationRules::class, 'validateSpanishCcc'],
            'spanish_passport' => [ValidationRules::class, 'validateSpanishPassport'],
        ];

        foreach ($rules as $rule => $callback) {
            Validator::extend($rule, function ($attribute, $value, $parameters, $validator) use ($callback) {
                return call_user_func($callback, $value);
            });

            Validator::replacer($rule, function ($message, $attribute, $rule, $parameters) {
                return __("laravel-validate-spa::validation.{$rule}", ['attribute' => $attribute]);
            });
        }

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-validate-spa');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-validate-spa'),
            ], 'lang');
        }
    }

    public function register(): void
    {
        //
    }
}
