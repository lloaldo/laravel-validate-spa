<?php

namespace Lloaldo\LaravelValidateSpa;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Tax Number: NIF or NIE or CIF
        Validator::extend('spanish_tax_number', function ($attribute, $value, $parameters, $validator) {
            return $this->validateTaxNumber($value);
        });

        Validator::replacer('spanish_tax_number', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_tax_number", ['attribute' => $attribute]);
        });

        // Personal ID: NIF or NIE
        Validator::extend('spanish_personal_id', function ($attribute, $value, $parameters, $validator) {
            return $this->validatePersonalIdNumber($value);
        });;
        
        Validator::replacer('spanish_personal_id', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_personal_id", ['attribute' => $attribute]);
        });
        
        Validator::extend('spanish_nif', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSpanishNif($value);
        });
        Validator::replacer('spanish_nif', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_nif", ['attribute' => $attribute]);
        });

        Validator::extend('spanish_nie', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSpanishNie($value);
        });

        Validator::replacer('spanish_nie', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_nie", ['attribute' => $attribute]);
        });

        Validator::extend('spanish_cif', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSpanishCif($value);
        });
        Validator::replacer('spanish_cif', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_cif", ['attribute' => $attribute]);
        });

        Validator::extend('spanish_ssn', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSpanishSsn($value);
        });
        Validator::replacer('spanish_ssn', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_ssn", ['attribute' => $attribute]);
        });

        Validator::extend('spanish_iban', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSpanishIban($value);
        });
        Validator::replacer('spanish_iban', function ($message, $attribute, $rule, $parameters) {
            return __("laravel-validate-spa::validation.spanish_iban", ['attribute' => $attribute]);
        });

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-validate-spa');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-validate-spa'),
            ], 'lang');
        }
    }
    public function validateTaxNumber(?string $value): bool
    {
        return
            $this->validateSpanishNif($value) or
            $this->validateSpanishNie($value) or
            $this->validateSpanishCif($value);
    }

    public function validatePersonalIdNumber(?string $value): bool
    {
        return
            $this->validateSpanishNif($value) or
            $this->validateSpanishNie($value);
    }

    private function validateSpanishNif(string $nif): bool
    {
        if (!is_string($nif)) {
            return false;
        }
        $value = strtoupper(trim($nif));
        if (!preg_match('/^[0-9]{8}[A-Z]$/', $value)) {
            return false;
        }
        $number = substr($value, 0, 8);
        $letter = substr($value, -1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $calculatedLetter = $letters[(int)$number % 23];
        return $letter === $calculatedLetter;
    }

    private function validateSpanishNie(string $nie): bool
    {
        if (!is_string($nie)) {
            return false;
        }
        $value = strtoupper(trim($nie));
        if (!preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $value)) {
            return false;
        }
        $number = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], substr($value, 0, 1)) . substr($value, 1, 7);
        $letter = substr($value, -1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $calculatedLetter = $letters[(int)$number % 23];
        return $letter === $calculatedLetter;
    }

    private function validateSpanishCif(string $cif): bool
    {
        $cif = strtoupper(trim($cif));
        if (empty($cif) || !preg_match('/^[A-W][0-9]{7}[0-9A-J]$/', $cif)) {
            return false;
        }
        $type = substr($cif, 0, 1);
        $number = substr($cif, 1, 7);
        $control = substr($cif, 8, 1);
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            if ($i % 2 == 0) {
                $tmp = (int)$number[$i] * 2;
                $sum += ($tmp > 9) ? (int)($tmp / 10) + ($tmp % 10) : $tmp;
            } else {
                $sum += (int)$number[$i];
            }
        }
        $controlDigit = (10 - ($sum % 10)) % 10;
        $numericControlTypes = ['A', 'B', 'E', 'H', 'J', 'U', 'V', 'P', 'Q', 'S', 'W'];
        if (in_array($type, $numericControlTypes)) {
            return $control == (string)$controlDigit;
        }
        $letterControlTypes = ['C', 'D', 'F', 'G', 'K', 'L', 'M', 'N', 'R', 'T'];
        if (in_array($type, $letterControlTypes)) {
            $letters = 'JABCDEFGHI';
            $controlLetter = $letters[$controlDigit];
            return $control == $controlLetter;
        }
        return false;
    }

    private function validateSpanishSsn($ssn): bool
    {
        if (!is_string($ssn) || empty($ssn)) {
            return false;
        }
        $ssn = preg_replace('/[^0-9]/', '', trim($ssn));
        if (!preg_match('/^[0-9]{12}$/', $ssn)) {
            return false;
        }
        $province = substr($ssn, 0, 2);
        $sequential = substr($ssn, 2, 8);
        $controlDigits = substr($ssn, 10, 2);

        $num = (int)$sequential < 10000000 
            ? (int)$sequential + (int)$province * 10000000 
            : (int)($province . $sequential);
        $remainder = $num % 97;
        $expectedControl = str_pad($remainder, 2, '0', STR_PAD_LEFT);
        return $controlDigits === $expectedControl;
    }

    private function validateSpanishIban($iban): bool
    {
        if (!is_string($iban)) {
            return false;
        }
        $iban = strtoupper(preg_replace('/[^A-Z0-9]/', '', trim($iban)));
        if (strlen($iban) !== 24 || substr($iban, 0, 2) !== 'ES') {
            return false;
        }
        $reordered = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';
        foreach (str_split($reordered) as $char) {
            if (is_numeric($char)) {
                $numeric .= $char;
            } else {
                $numeric .= (ord($char) - ord('A') + 10);
            }
        }
        $remainder = 0;
        foreach (str_split($numeric) as $digit) {
            $remainder = ($remainder * 10 + (int)$digit) % 97;
        }
        return $remainder === 1;
    }

    public function register(): void
    {
        //
    }
}