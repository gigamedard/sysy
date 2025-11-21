<?php

use PHPUnit\Framework\TestCase;
use Bookstore\Validator;

class ValidatorTest extends TestCase {
    public function testValidateOrderValidData() {
        $validator = new Validator();
        $data = [
            'name' => 'John Doe',
            'phone' => '77 000 00 00',
            'payment_method' => 'wave'
        ];

        $this->assertTrue($validator->validateOrder($data));
        $this->assertEmpty($validator->getErrors());
    }

    public function testValidateOrderMissingName() {
        $validator = new Validator();
        $data = [
            'name' => '',
            'phone' => '77 000 00 00',
            'payment_method' => 'wave'
        ];

        $this->assertFalse($validator->validateOrder($data));
        $this->assertArrayHasKey('name', $validator->getErrors());
    }

    public function testValidateOrderInvalidPhone() {
        $validator = new Validator();
        $data = [
            'name' => 'John Doe',
            'phone' => 'invalid-phone',
            'payment_method' => 'wave'
        ];

        $this->assertFalse($validator->validateOrder($data));
        $this->assertArrayHasKey('phone', $validator->getErrors());
    }

    public function testValidateOrderInvalidPaymentMethod() {
        $validator = new Validator();
        $data = [
            'name' => 'John Doe',
            'phone' => '77 000 00 00',
            'payment_method' => 'bitcoin'
        ];

        $this->assertFalse($validator->validateOrder($data));
        $this->assertArrayHasKey('payment_method', $validator->getErrors());
    }
}
