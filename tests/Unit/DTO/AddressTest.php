<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\DTO;

use AlsendoOne\SDK\DTO\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testToArraySerializesAllWireFields(): void
    {
        $address = new Address(
            'Point Receiver',
            null,
            'point@example.com',
            '600300400',
            'Dluga 15',
            null,
            '80-831',
            'Gdansk',
            'PL',
            'ADA01M',      // foreign_address_id — target pickup point
            null,
            null,
            'ORLEN'        // foreign_address_subtype — destination network brand
        );

        $data = $address->toArray();

        $this->assertSame('ADA01M', $data['foreign_address_id']);
        $this->assertSame('ORLEN', $data['foreign_address_subtype']);
        $this->assertArrayNotHasKey('contact_person', $data);
        $this->assertArrayNotHasKey('state_code', $data);
        $this->assertArrayNotHasKey('is_residential', $data);
    }

    public function testFromArrayRoundTripsNewFields(): void
    {
        $address = Address::fromArray([
            'name' => 'US Receiver',
            'state_code' => 'CA',
            'is_residential' => true,
            'foreign_address_subtype' => 'PACKETA',
        ]);

        $this->assertSame('CA', $address->getStateCode());
        $this->assertTrue($address->getIsResidential());
        $this->assertSame('PACKETA', $address->getForeignAddressSubtype());
    }
}
