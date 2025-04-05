<?php

namespace Lloaldo\LaravelValidateSpa\Tests;

use Illuminate\Support\Facades\Validator;
use Lloaldo\LaravelValidateSpa\ValidationServiceProvider;
use Orchestra\Testbench\TestCase;

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
    public function it_rejects_invalid_cccs()
    {
        $validCccs = ['2085 0668 24 3101824287', '20850668393101824285'];
        foreach ($validCccs as $ccc) {
            $validator = Validator::make(['ccc' => $ccc], ['ccc' => 'spanish_ccc']);
            $this->assertTrue($validator->fails(), "The CCC $ccc should be invalid");
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
