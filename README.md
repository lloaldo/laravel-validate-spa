# Laravel Validate SPA

A Laravel package that provides custom validation rules for Spanish identification numbers: NIF, NIE, CIF, SSN, IBAN,
POSTAL CODE, PHONE, LICENSE PLATE, CCC, PASSPORT .

## Features

-   Validates Spanish NIF (Documento Identificación Fiscal) and NIE (Número de Identidad de Extranjero).
-   Validates Spanish CIF (Código de Identificación Fiscal).
-   Validates Spanish SSN (Social Security Number).
-   Validates Spanish IBAN (International Bank Account Number).
-   Validates Postal Code Spanish.
-   Validates Phone Number Spanish.
-   Validates Spanish License plate vehicle(Vehículos Matrícula Española).
-   Validates Spanish CCC(Old Spanish Bank Account, precursor of the IBAN).
-   Validates Spanish Passport.
-   Includes translatable error messages.
-   Easy integration with Laravel's validation system.

## Requirements

-   PHP >= 8.0
-   Laravel >= 9.0

## Installation

1. **Install the package via Composer**  
   Run the following command in your Laravel project directory:

    ```bash
    composer require lloaldo/laravel-validate-spa
    ```

2. **Publish the language files (optional)**  
   If you want to customize the validation error messages, publish the language files:

    ```bash
    php artisan vendor:publish --tag=lang
    ```

    This will copy the translation files to `resources/lang/vendor/laravel-validate-spa`. You can modify the messages in your desired language (e.g., `es/validation.php` for Spanish).

3. **Service Provider (optional for Laravel 9+)**  
   The package uses Laravel's auto-discovery feature, so no manual registration is needed. If you're using an older version of Laravel without package discovery, add the service provider to `config/app.php`:
    ```php
    'providers' => [
        // Other providers...
        Lloaldo\LaravelValidateSpa\ValidationServiceProvider::class,
    ],
    ```

## Usage

### Validation Rules

This package provides two custom validation rules: `personal_id`, `tax_identifier`, `spanish_nif`, `spanish_nie`, `spanish_cif`, `spanish_ssn`,`spanish_iban`, `spanish_postal_code`, `spanish_phone`, `spanish_license_plate` `spanish_ccc`and `spanish_passport`. You can use them in your controllers, form requests, or anywhere Laravel's validator is supported.

#### Example in a Controller

```php
use Illuminate\Http\Request;

public function store(Request $request)
{
    $validated = $request->validate([
        'personal_id' => 'required|spanish_personal_id',    // NIF,NIE
        'tax_identifier' => 'required|spanish_tax_number',   // NIF, NIE, CIF
        'nif' => 'required|spanish_nif',
        'nie' => 'required|spanish_nie',
        'cif' => 'nullable|spanish_cif',
        'ssn' => 'required|spanish_ssn',
        'iban' => 'required|spanish_iban',
        'address_postal_code' => 'nullable|spanish_postal_code',
        'phone' => 'nullable|spanish_phone',
        'license_plate' => 'nullable|spanish_license_plate',
        'ccc' => 'nullable|spanish_ccc',
        'passport' => 'nullable|spanish_passport',
        'name' => 'required|string|max:255',
    ]);

    // Process validated data...
    return redirect()->back()->with('success', 'Data saved successfully!');
}
```

#### Example in a Form Request

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'personal_id' => 'required|spanish_personal_id',
            'cif' => 'nullable|spanish_cif',
            'ssn' => 'required|spanish_ssn',
            'iban' => 'required|spanish_iban',
            'name' => 'required|string|max:255',
        ];
    }
}
```

### Customizing Error Messages

Error messages are loaded from the package's language files. After publishing the language files, you can edit them in `resources/lang/vendor/laravel-validate-spa/{lang}/validation.php`. For example:

```php
// resources/lang/vendor/laravel-validate-spa/es/validation.php
return [
    'spanish_nif' => 'El :attribute no es un NIF válido.',
    'spanish_nie' => 'El :attribute no es un NIE válido.',
    'spanish_cif' => 'El :attribute no es un CIF válido.',
    'spanish_ssn' => 'El :attribute no es un Número de la Seguridad Social válido.',
    'spanish_iban' => 'El :attribute no es un IBAN español válido.',
    'spanish_postal_code' => 'El :attribute no es un código postal español válido.',
    'spanish_phone' => 'El :attribute no es un número de teléfono español válido.',
    'spanish_license_plate' => 'El :attribute no es una matrícula española válida.',
    'spanish_ccc' => 'El :attribute no es un CCC español válido.',
    'spanish_passport' => 'El :attribute es un pasaporte español válido.',
];
```

If you don't publish the files, the package will use its default messages (if provided) or fall back to Laravel's generic "The :attribute is invalid" message.

### Testing the Rules

You can test the validation rules using Laravel's `Validator` facade in Tinker:

```bash
php artisan tinker
Validator::make(['nif' => 'XXXXXXZ'], ['nif' => 'spanish_nif'])->fails() // false (valid)
Validator::make(['nif' => 'XXXXXXA'], ['nif' => 'spanish_nif'])->fails() // true (invalid)
Validator::make(['cif' => 'AXXXXXX'], ['cif' => 'spanish_cif'])->fails() // false (valid)
Validator::make(['ssn' => 'XXXXXXXXXXXX'], ['ssn' => 'spanish_ssn'])->fails() // true (invalid)
Validator::make(['ssn' => 'XXXXXXXXXXXX'], ['ssn' => 'spanish_ssn'])->fails() // false (valid)
Validator::make(['iban' => 'ESXXXXXXXXXXXXX'], ['iban' => 'spanish_iban'])->fails() // false (valid)
Validator::make(['iban' => 'ESXXXXXXXXXXXXX'], ['iban' => 'spanish_iban'])->fails() // true (invalid)
Validator::make(['postal_code' => '08001'], ['postal_code' => 'spanish_postal_code'])->fails() // false (valid)
Validator::make(['postal_code' => '108001'], ['postal_code' => 'spanish_postal_code'])->fails() // false (invalid)
Validator::make(['phone' => 'XXXXXXXX'], ['phone' => 'spanish_phone'])->fails() // false (valid)
Validator::make(['license_plate' => 'XXXXABC'], ['license_plate' => 'spanish_license_plate'])->fails() // false (valid)
Validator::make(['ccc' => 'XXXX XXXX XX XXXXXXXXXX'], ['ccc' => 'spanish_ccc'])->fails() // false (valid)
Validator::make(['passport' => 'ABCXXXXXX'], ['ccc' => 'spanish_passport'])->fails() // false (valid)

```

## Validation Logic

-   **NIF/NIE**: Validates Spanish NIF (8 digits + letter) and NIE (X/Y/Z + 7 digits + letter) using the official algorithm.
-   **CIF**: Validates Spanish CIF codes (letter + 7 digits + control character) according to the official rules.
-   **SSN**: Validates Spanish Social Security Numbers (12 digits: 2 for province code, 8 sequential digits, and 2 control digits) by checking the province code (01-99) and verifying the control digits using the modulo 97 algorithm.
-   **IBAN**: Validates Spanish IBANs (24 characters: "ES" + 2 control digits + 20-digit bank account number) using the ISO 7064 MOD 97-10 algorithm, ensuring the country code is "ES" and the control digits are correct.
-   **POSTAL CODE** Validates Spanish Postal Code
-   **PHONE** Validates Spanish Phone Number
-   **VEHICLE LICENSE PLATE** Validates Spanish License Plate Car(vehículos Matrículas española)
-   **CCC** Validates Spanish Old Spanish Bank Account, precursor of the IBAN
-   **PASSPORT** Validates Spanish Passport

## Contributing

Contributions are welcome! Feel free to submit a pull request or open an issue on the [GitHub repository](https://github.com/lloaldo/laravel-validate-spa).

## License

This package is open-source software licensed under the [MIT License](LICENSE).

**Laravel Valida Spa** was created by **[Domingo Albújar]** under the **[MIT license](https://opensource.org/licenses/MIT)**.
