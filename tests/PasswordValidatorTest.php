<?php

use PHPUnit\Framework\TestCase;
use App\Utils\PasswordValidator;

class PasswordValidatorTest extends TestCase
{

    public function testTooShortPasswordIsInvalid()
    {
        // Le test exprime notre intention : une chaîne < 8 chars doit retourner false
        $isValid = PasswordValidator::validate('1234567');
        $this->assertFalse($isValid);
    }

    public function testPasswordWithoutNumberIsInvalid()
    {
        // 8 caractères, mais pas de chiffre
        $isValid = PasswordValidator::validate('abcdefgh');
        $this->assertFalse($isValid);
    }

    public function testValidPasswordPasses()
    {
        // 8 caractères + 1 chiffre
        $isValid = PasswordValidator::validate('abcdefg1');
        $this->assertTrue($isValid);
    }
}
