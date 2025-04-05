<?php

namespace Lloaldo\LaravelValidateSpa;

class ValidationRules
{
    public static function validateSpanishNif(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = strtoupper(trim($value));
        if (! preg_match('/^[0-9]{8}[A-Z]$/', $value)) {
            return false;
        }
        $number = substr($value, 0, 8);
        $letter = substr($value, -1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $calculatedLetter = $letters[(int) $number % 23];

        return $letter === $calculatedLetter;
    }

    public static function validateSpanishNie(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = strtoupper(trim($value));
        if (! preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $value)) {
            return false;
        }
        $number = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], substr($value, 0, 1)).substr($value, 1, 7);
        $letter = substr($value, -1);
        $letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $calculatedLetter = $letters[(int) $number % 23];

        return $letter === $calculatedLetter;
    }

    public static function validateSpanishCif(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $cif = strtoupper(trim($value));
        if (empty($cif) || ! preg_match('/^[A-W][0-9]{7}[0-9A-J]$/', $cif)) {
            return false;
        }
        $type = substr($cif, 0, 1);
        $number = substr($cif, 1, 7);
        $control = substr($cif, 8, 1);
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            if ($i % 2 == 0) {
                $tmp = (int) $number[$i] * 2;
                $sum += ($tmp > 9) ? (int) ($tmp / 10) + ($tmp % 10) : $tmp;
            } else {
                $sum += (int) $number[$i];
            }
        }
        $controlDigit = (10 - ($sum % 10)) % 10;
        $numericControlTypes = ['A', 'B', 'E', 'H', 'J', 'U', 'V', 'P', 'Q', 'S', 'W'];
        if (in_array($type, $numericControlTypes)) {
            return $control == (string) $controlDigit;
        }
        $letterControlTypes = ['C', 'D', 'F', 'G', 'K', 'L', 'M', 'N', 'R', 'T'];
        if (in_array($type, $letterControlTypes)) {
            $letters = 'JABCDEFGHI';
            $controlLetter = $letters[$controlDigit];

            return $control == $controlLetter;
        }

        return false;
    }

    public static function validateTaxNumber(?string $value): bool
    {
        return self::validateSpanishNif($value) || self::validateSpanishNie($value) || self::validateSpanishCif($value);
    }

    public static function validatePersonalIdNumber(?string $value): bool
    {
        return self::validateSpanishNif($value) || self::validateSpanishNie($value);
    }

    public static function validateSpanishSsn(?string $value): bool
    {
        if (! is_string($value) || empty($value)) {
            return false;
        }
        $ssn = preg_replace('/[^0-9]/', '', trim($value));
        if (! preg_match('/^[0-9]{12}$/', $ssn)) {
            return false;
        }
        $province = substr($ssn, 0, 2);
        $sequential = substr($ssn, 2, 8);
        $controlDigits = substr($ssn, 10, 2);
        $num = (int) $sequential < 10000000 ? (int) $sequential + (int) $province * 10000000 : (int) ($province.$sequential);
        $remainder = $num % 97;
        $expectedControl = str_pad($remainder, 2, '0', STR_PAD_LEFT);

        return $controlDigits === $expectedControl;
    }

    public static function validateSpanishIban(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $iban = strtoupper(preg_replace('/[^A-Z0-9]/', '', trim($value)));
        if (strlen($iban) !== 24 || substr($iban, 0, 2) !== 'ES') {
            return false;
        }
        $reordered = substr($iban, 4).substr($iban, 0, 4);
        $numeric = '';
        foreach (str_split($reordered) as $char) {
            $numeric .= is_numeric($char) ? $char : (ord($char) - ord('A') + 10);
        }
        $remainder = 0;
        foreach (str_split($numeric) as $digit) {
            $remainder = ($remainder * 10 + (int) $digit) % 97;
        }

        return $remainder === 1;
    }

    public static function validateSpanishPostalCode(?string $value): bool
    {
        if (! is_string($value) || ! preg_match('/^[0-5][0-9]{4}$/', $value)) {
            return false;
        }
        $province = (int) substr($value, 0, 2);

        return $province >= 1 && $province <= 52;
    }

    public static function validateSpanishPhone(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = preg_replace('/[^0-9]/', '', trim($value));

        return preg_match('/^[6-9][0-9]{8}$/', $value);
    }

    public static function validateSpanishLicensePlate(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = strtoupper(preg_replace('/[^0-9A-Z]/', '', trim($value)));

        return preg_match('/^[0-9]{4}[B-DF-HJ-NP-TV-Z]{3}$/', $value);
    }

    public static function validateSpanishCcc(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = preg_replace('/[^0-9]/', '', trim($value));
        if (! preg_match('/^[0-9]{20}$/', $value)) {
            return false;
        }
        $entity = substr($value, 0, 4);
        $office = substr($value, 4, 4);
        $control = substr($value, 8, 2);
        $account = substr($value, 10, 10);

        $weights1 = [4, 8, 5, 10, 9, 7, 3, 6];
        $sum1 = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum1 += (int) ($entity.$office)[$i] * $weights1[$i];
        }
        $control1 = (11 - ($sum1 % 11)) % 11;
        $control1 = $control1 == 10 ? 1 : $control1;

        $weights2 = [1, 2, 4, 8, 5, 10, 9, 7, 3, 6];
        $sum2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum2 += (int) $account[$i] * $weights2[$i];
        }
        $control2 = (11 - ($sum2 % 11)) % 11;
        $control2 = $control2 == 10 ? 1 : $control2;

        $calculatedControl = str_pad($control1, 1, '0', STR_PAD_LEFT).str_pad($control2, 1, '0', STR_PAD_LEFT);

        return $control === $calculatedControl;
    }

    public static function validateSpanishPassport(?string $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = strtoupper(trim($value));

        return preg_match('/^[A-Z]{3}[0-9]{6}$/', $value) && strlen($value) === 9;
    }
}
