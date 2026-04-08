<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO;

use AlsendoOne\SDK\DTO\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testFromArrayCreatesAddressWithAllFields(): void
    {
        $data = [
            'name' => 'Firma Sp. z o.o.',
            'contact_person' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'phone' => '500100200',
            'line1' => 'Marszalkowska 1/10',
            'line2' => 'Biuro 5',
            'postal_code' => '00-001',
            'city' => 'Warszawa',
            'country_code' => 'PL',
            'foreign_address_id' => 'KRA01',
        ];

        $address = Address::fromArray($data);

        $this->assertSame('Firma Sp. z o.o.', $address->getName());
        $this->assertSame('Jan Kowalski', $address->getContactPerson());
        $this->assertSame('jan@example.com', $address->getEmail());
        $this->assertSame('500100200', $address->getPhone());
        $this->assertSame('Marszalkowska 1/10', $address->getLine1());
        $this->assertSame('Biuro 5', $address->getLine2());
        $this->assertSame('00-001', $address->getPostalCode());
        $this->assertSame('Warszawa', $address->getCity());
        $this->assertSame('PL', $address->getCountryCode());
        $this->assertSame('KRA01', $address->getForeignAddressId());
    }

    public function testFromArrayHandlesMissingFields(): void
    {
        $address = Address::fromArray([]);

        $this->assertNull($address->getName());
        $this->assertNull($address->getContactPerson());
        $this->assertNull($address->getEmail());
        $this->assertNull($address->getPhone());
        $this->assertNull($address->getLine1());
        $this->assertNull($address->getLine2());
        $this->assertNull($address->getPostalCode());
        $this->assertNull($address->getCity());
        $this->assertNull($address->getCountryCode());
        $this->assertNull($address->getForeignAddressId());
    }

    public function testFromArrayCastsValuesToString(): void
    {
        $address = Address::fromArray([
            'name' => 123,
            'phone' => 500100200,
        ]);

        $this->assertSame('123', $address->getName());
        $this->assertSame('500100200', $address->getPhone());
    }

    public function testToArrayReturnsOnlyNonNullFields(): void
    {
        $address = new Address(
            'Anna Nowak',
            null,
            'anna@example.com',
            '600300400',
            'Dluga 15',
            null,
            '80-831',
            'Gdansk',
            'PL'
        );

        $result = $address->toArray();

        $this->assertSame('Anna Nowak', $result['name']);
        $this->assertSame('anna@example.com', $result['email']);
        $this->assertSame('600300400', $result['phone']);
        $this->assertSame('Dluga 15', $result['line1']);
        $this->assertSame('80-831', $result['postal_code']);
        $this->assertSame('Gdansk', $result['city']);
        $this->assertSame('PL', $result['country_code']);
        $this->assertArrayNotHasKey('contact_person', $result);
        $this->assertArrayNotHasKey('line2', $result);
        $this->assertArrayNotHasKey('foreign_address_id', $result);
    }

    public function testToArrayReturnsEmptyForBlankAddress(): void
    {
        $address = Address::fromArray([]);

        $this->assertSame([], $address->toArray());
    }

    public function testRoundTripFromArrayToArray(): void
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@test.pl',
            'phone' => '123456789',
            'line1' => 'Testowa 1',
            'postal_code' => '00-001',
            'city' => 'Warszawa',
            'country_code' => 'PL',
        ];

        $result = Address::fromArray($data)->toArray();

        $this->assertSame($data, $result);
    }
}
