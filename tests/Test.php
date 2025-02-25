<?php

namespace Lloaldo\LaravelValidateSpa\Tests;

use Orchestra\Testbench\TestCase;
use Lloaldo\LaravelValidateSpa\ValidationServiceProvider;
use Illuminate\Support\Facades\Validator;

class SpanishValidatorsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ValidationServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_validates_correct_personal_id()
    {
        $validPersonalId = [
            '12345678Z', // NIF
            'X1234567L', // NIE
        ];

        foreach ($validPersonalId as $personalId) {
            $validator = Validator::make(['personal_id' => $personalId], ['nif' => 'spanish_personal_id']);
            $this->assertFalse($validator->fails(), "The personal_id $personalId should be valid");
        }
    }

    public function it_rejects_incorrect_personal_id()
    {
        $invalidPersonalId = [
            'A58818501', // CIF
            '123456789A', // NIF
            'X4234567L', // NIE
        ];

        foreach ($invalidPersonalId as $personalId) {
            $validator = Validator::make(['personal_id' => $personalId], ['nif' => 'spanish_personal_id']);
            $this->assertTrue($validator->fails(), "The personal_id $personalId should be valid");
        }
    }

    public function it_validates_correct_tax_number()
    {
        $validTaxNumber = [
            '12345678A', // NIF
            'X1234567L', // NIE
            'A58818501', // CIF
        ];

        foreach ($validTaxNumber as $taxNumber) {
            $validator = Validator::make(['tax_number' => $taxNumber], ['nif' => 'spanish_tax_number']);
            $this->assertFalse($validator->fails(), "The tax_number $taxNumber should be valid");
        }
    }

    public function it_rejects_incorrect_tax_number()
    {
        $invalidTaxNumber = [
            '12345678Z', // NIF
            'X123456', // NIE
            'E58818501', // CIF
        ];

        foreach ($invalidTaxNumber as $taxNumber) {
            $validator = Validator::make(['tax_number' => $taxNumber], ['nif' => 'spanish_tax_number']);
            $this->assertFalse($validator->fails(), "The tax_number $taxNumber should be valid");
        }
    }

    /** @test */
    public function it_validates_correct_nifs()
    {
        $validNifs = [
            '12345678Z', // Básico
            '00000001R', // Valor bajo
            '99999999R', // Valor alto
            '00000000T', // Caso especial
        ];

        foreach ($validNifs as $nif) {
            $validator = Validator::make(['nif' => $nif], ['nif' => 'spanish_nif']);
            $this->assertFalse($validator->fails(), "The NIF $nif should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_nifs()
    {
        $invalidNifs = [
            '1234567',      // Demasiado corto
            '123456789A',   // Demasiado largo
            '12345678A',    // Letra incorrecta
            'X12345678Z',   // Formato NIE
            'ABCDEFGHI',    // Sin números
        ];

        foreach ($invalidNifs as $nif) {
            $validator = Validator::make(['nif' => $nif], ['nif' => 'spanish_nif']);
            $this->assertTrue($validator->fails(), "The NIF $nif should be invalid");
        }
    }

    /** @test */
    public function it_validates_correct_nies()
    {
        $validNies = [
            'X1234567L', // Tipo X
            'Y4712444B', // Tipo Y
            'Z1234567R', // Tipo Z
        ];

        foreach ($validNies as $nie) {
            $validator = Validator::make(['nie' => $nie], ['nie' => 'spanish_nie']);
            $this->assertFalse($validator->fails(), "The NIE $nie should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_nies()
    {
        $invalidNies = [
            'X1234567A',    // Letra incorrecta
            '12345678Z',    // Formato NIF
            'X123456',      // Demasiado corto
            'X12345678A',   // Demasiado largo
            'A1234567L',    // Prefijo inválido
            'Y1234567R',    // Tipo Y
        ];

        foreach ($invalidNies as $nie) {
            $validator = Validator::make(['nie' => $nie], ['nie' => 'spanish_nie']);
            $this->assertTrue($validator->fails(), "The NIE $nie should be invalid");
        }
    }

    /** @test */
    public function it_validates_correct_cifs()
    {
        $validCifs = [
            'A58818501', 'B63679435', 'U26543876', 'N9876543A', 'Q28012342',
        ];

        foreach ($validCifs as $cif) {
            $validator = Validator::make(['cif' => $cif], ['cif' => 'spanish_cif']);
            $this->assertFalse($validator->fails(), "The CIF $cif should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_cifs()
    {
        $invalidCifs = [
            'P5700001E', 'X12345678', 'A1234567', 'A123456789', 'A12345678',
            'A 1234567A', 'A1234567J', 'P1234567A', 'J7654321K', 'V1234567B',
            'C12345678', 'C1234567A', 'F12345670', 'K12345670', 'R12345670',
            '12345678A', 'ABCDEFGHIJ', '!ABCDE#FG',
        ];

        foreach ($invalidCifs as $cif) {
            $validator = Validator::make(['cif' => $cif], ['cif' => 'spanish_cif']);
            $this->assertTrue($validator->fails(), "The CIF $cif should be invalid");
        }
    }

    /** @test */
    public function it_accepts_personal_id_with_lowercase_and_spaces()
    {
        $specialDnis = [
            '12345678z', ' 12345678Z ', ' 12345678z ',
        ];

        foreach ($specialDnis as $personalId) {
            $validator = Validator::make(['personal_id' => $personalId], ['personal_id' => 'spanish_personal_id']);
            $this->assertFalse($validator->fails(), "The Persona Id $personalId should be valid after normalization");
        }
    }

    /** @test */
    public function it_accepts_cifs_with_lowercase_and_spaces()
    {
        $specialCifs = [
            'a58818501', ' A58818501 ', ' a58818501 ',
        ];

        foreach ($specialCifs as $cif) {
            $validator = Validator::make(['cif' => $cif], ['cif' => 'spanish_cif']);
            $this->assertFalse($validator->fails(), "The CIF $cif should be valid after normalization");
        }
    }

    /** @test */
    public function it_validates_correct_ssns()
    {
        $validSsns = [
            '087894806929',
            '438509069151',
            '190250601711',
        ];

        foreach ($validSsns as $ssn) {
            $validator = Validator::make(['ssn' => $ssn], ['ssn' => 'spanish_ssn']);
            $this->assertFalse($validator->fails(), "The SSN $ssn should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_ssns()
    {
        $invalidSsns = [
            '280123456780', '12345678', '1234567890123', '000123456789',
            '990123456701', '28abc4567812', '2801234567  ', '28012345678A',
        ];

        foreach ($invalidSsns as $ssn) {
            $validator = Validator::make(['ssn' => $ssn], ['ssn' => 'spanish_ssn']);
            $this->assertTrue($validator->fails(), "The SSN $ssn should be invalid");
        }
    }

    /** @test */
    public function it_accepts_ssns_with_spaces()
    {
        $specialSsns = [
            ' 291917876947 ', '2919178769 47',
        ];

        foreach ($specialSsns as $ssn) {
            $validator = Validator::make(['ssn' => $ssn], ['ssn' => 'spanish_ssn']);
            $this->assertFalse($validator->fails(), "The SSN $ssn should be valid after normalization");
        }
    }

    /** @test */
    public function it_validates_correct_ibans()
    {
        $validIbans = [
            'ES9121000418450200051332', // Standard valid IBAN
            'ES6120466547007633602928', // Another valid IBAN
        ];

        foreach ($validIbans as $iban) {
            $validator = Validator::make(['iban' => $iban], ['iban' => 'spanish_iban']);
            $this->assertFalse($validator->fails(), "The IBAN $iban should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_ibans()
    {
        $invalidIbans = [
            'ES7600246912500600176650', 'ES9121000418450200051333', 'FR1234567890123456789012', 'ES123456789012',
            'ES1234567890123456789012345', 'ES91 2100 0418', 'ES91210004184502000513AA',
            'XX9121000418450200051332',
        ];

        foreach ($invalidIbans as $iban) {
            $validator = Validator::make(['iban' => $iban], ['iban' => 'spanish_iban']);
            $this->assertTrue($validator->fails(), "The IBAN $iban should be invalid");
        }
    }

    /** @test */
    public function it_accepts_ibans_with_lowercase_and_spaces()
    {
        $specialIbans = [
            ' ES9121000418450200051332 ', ' ES91 2100 0418 4502 0005 1332', ' ES8401526659251705626017  '
        ];

        foreach ($specialIbans as $iban) {
            $validator = Validator::make(['iban' => $iban], ['iban' => 'spanish_iban']);
            $this->assertFalse($validator->fails(), "The IBAN $iban should be valid after normalization");
        }
    }

    /** @test */
    public function it_validates_correct_postal_codes()
    {
        $validCodes = ['28001', '08001', '48020'];
        foreach ($validCodes as $code) {
            $validator = Validator::make(['code' => $code], ['code' => 'spanish_postal_code']);
            $this->assertFalse($validator->fails(), "The postal code $code should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_postal_codes()
    {
        $validCodes = ['2801', '108001', '98020'];
        foreach ($validCodes as $code) {
            $validator = Validator::make(['code' => $code], ['code' => 'spanish_postal_code']);
            $this->assertTrue($validator->fails(), "The postal code $code should be invalid");
        }
    }

    /** @test */
    public function it_validates_correct_phones()
    {
        $validPhones = ['612345678', '912345678', '712 345 678', '6-12-34-56-78'];
        foreach ($validPhones as $phone) {
            $validator = Validator::make(['phone' => $phone], ['phone' => 'spanish_phone']);
            $this->assertFalse($validator->fails(), "The phone $phone should be valid");
        }
    }

    /** @test */
    public function it_validates_correct_license_plates()
    {
        $validPlates = ['1234BCD', '0000XYZ', '5678KLM'];
        foreach ($validPlates as $plate) {
            $validator = Validator::make(['plate' => $plate], ['plate' => 'spanish_license_plate']);
            $this->assertFalse($validator->fails(), "The license plate $plate should be valid");
        }
    }

    /** @test */
    public function it_validates_correct_cccs()
    {
        $validCccs = ['2100 0418 45 0200051332', '20850668313101824285'];
        foreach ($validCccs as $ccc) {
            $validator = Validator::make(['ccc' => $ccc], ['ccc' => 'spanish_ccc']);
            $this->assertFalse($validator->fails(), "The CCC $ccc should be valid");
        }
    }

    /** @test */
    public function it_rejects_invalid_cccs()
    {
        $validCccs = ['2085 0668 24 3101824287', '20850668393101824285'];
        foreach ($validCccs as $ccc) {
            $validator = Validator::make(['ccc' => $ccc], ['ccc' => 'spanish_ccc']);
            $this->assertTrue($validator->fails(), "The CCC $ccc should be invalid");
        }
    }
    /** @test */
    public function it_validates_correct_passports()
    {
        $validPassports = ['ABC123456', 'XYZ987654'];
        foreach ($validPassports as $passport) {
            $validator = Validator::make(['passport' => $passport], ['passport' => 'spanish_passport']);
            $this->assertFalse($validator->fails(), "The passport $passport should be valid");
        }
    }

    /** @test */
    public function it_rejects_incorrect_passports()
    {
        $invalidPassports = ['XYZ9876a54', 'XYZ9876A54'];
        foreach ($invalidPassports as $passport) {
            $validator = Validator::make(['passport' => $passport], ['passport' => 'spanish_passport']);
            $this->assertTrue($validator->fails(), "The passport $passport should be invalid");
        }
    }
}