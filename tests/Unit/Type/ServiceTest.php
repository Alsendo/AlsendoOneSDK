<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Tests\Unit\Type;

use AlsendoOne\SDK\Type\Service;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testFromCreatesInstanceWithCorrectValue(): void
    {
        $service = Service::from(Service::InPostPaczkomat);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertSame(41, $service->value);
    }

    public function testFromThrowsExceptionForUnknownId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown service ID: 99999');

        Service::from(99999);
    }

    public function testTryFromReturnsInstanceForKnownId(): void
    {
        $service = Service::tryFrom(Service::DpdCourier);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertSame(21, $service->value);
    }

    public function testTryFromReturnsNullForUnknownId(): void
    {
        $this->assertNull(Service::tryFrom(99999));
    }

    public function testCodeReturnsApiCode(): void
    {
        $this->assertSame('PACZKOMAT', Service::from(Service::InPostPaczkomat)->code());
        $this->assertSame('UPS_K_STANDARD', Service::from(Service::UpsStandard)->code());
        $this->assertSame('DPD_CLASSIC', Service::from(Service::DpdCourier)->code());
        $this->assertSame('DHLSTD', Service::from(Service::DhlParcelCourier)->code());
        $this->assertSame('FEDEX', Service::from(Service::FedExCourier)->code());
        $this->assertSame('GLS', Service::from(Service::GlsCourier)->code());
    }

    public function testLabelReturnsHumanReadableName(): void
    {
        $this->assertSame('InPost Paczkomat', Service::from(Service::InPostPaczkomat)->label());
        $this->assertSame('UPS Standard', Service::from(Service::UpsStandard)->label());
        $this->assertSame('DPD Kurier', Service::from(Service::DpdCourier)->label());
    }

    public function testSupplierReturnsSupplierCode(): void
    {
        $this->assertSame('INPOST', Service::from(Service::InPostPaczkomat)->supplier());
        $this->assertSame('INPOST', Service::from(Service::InPostCourier)->supplier());
        $this->assertSame('UPS', Service::from(Service::UpsStandard)->supplier());
        $this->assertSame('DPD', Service::from(Service::DpdCourier)->supplier());
        $this->assertSame('DPD', Service::from(Service::AlsendoDpdGermany)->supplier());
        $this->assertSame('DHL', Service::from(Service::DhlParcelCourier)->supplier());
        $this->assertSame('DHL_PARCEL', Service::from(Service::DhlPopHomeToPoint)->supplier());
        $this->assertSame('DHL_DE', Service::from(Service::DhlAlsendoInternationalWarehouseToHome)->supplier());
        $this->assertSame('DHL_INT', Service::from(Service::DhlParcelConnectHomeToHome)->supplier());
        $this->assertSame('FEDEX', Service::from(Service::FedExCourier)->supplier());
        $this->assertSame('GLS', Service::from(Service::GlsCourier)->supplier());
        $this->assertSame('POCZTA', Service::from(Service::PocztexCourierHomeToHome)->supplier());
        $this->assertSame('ORLEN', Service::from(Service::OrlenPaczkaPointToPoint)->supplier());
        $this->assertSame('RABEN', Service::from(Service::RabenInternational)->supplier());
        $this->assertSame('KEX', Service::from(Service::GeisCargo)->supplier());
        $this->assertSame('HELLMANN', Service::from(Service::HellmannDomestic)->supplier());
        $this->assertSame('RHENUS', Service::from(Service::RhenusLogistics)->supplier());
        $this->assertSame('PEKAES', Service::from(Service::GeodisDomestic)->supplier());
        $this->assertSame('AMBRO', Service::from(Service::AmbroExpress)->supplier());
        $this->assertSame('CBL', Service::from(Service::AlsendoInternationalCblWarehouseToHome)->supplier());
        $this->assertSame('PP_CBL', Service::from(Service::AlsendoInternationalPpCblPointToHome)->supplier());
        $this->assertSame('PP_PACKETA', Service::from(Service::PacketaLinehaulPointToHome)->supplier());
        $this->assertSame('PACKETA', Service::from(Service::PacketaWarehouseToHome)->supplier());
    }

    public function testConstantsHaveExpectedValues(): void
    {
        $this->assertSame(1, Service::UpsStandard);
        $this->assertSame(41, Service::InPostPaczkomat);
        $this->assertSame(82, Service::DhlParcelCourier);
        $this->assertSame(317, Service::PacketaWarehouseToPoint);
    }

    public function testAllConstantsResolveViaFrom(): void
    {
        $reflection = new \ReflectionClass(Service::class);
        $constants = $reflection->getConstants();

        foreach ($constants as $name => $value) {
            $service = Service::from($value);
            $this->assertSame($value, $service->value, "Constant {$name} should round-trip via from()");
            // Verify all three methods work without throwing
            $this->assertIsString($service->code(), "code() failed for {$name}");
            $this->assertIsString($service->label(), "label() failed for {$name}");
            $this->assertIsString($service->supplier(), "supplier() failed for {$name}");
        }
    }
}
