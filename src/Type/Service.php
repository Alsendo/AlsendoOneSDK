<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Type;

use InvalidArgumentException;

/**
 * Available shipping services accessible through the Alsendo (Apaczka) API v2.
 *
 * Each constant is a human-readable identifier whose value is the service ID.
 * Use {@see Service::from()} / {@see Service::tryFrom()} to obtain a Service
 * instance carrying the id in its $value property, with code(), label() and
 * supplier() accessors. Instances are cached, so two instances of the same
 * service id are identical (===).
 */
class Service
{
    public const UpsStandard = 1;
    public const UpsExpressSaver = 2;
    public const UpsExpressBy12 = 3;
    public const UpsExpressPlusBy9 = 4;
    public const UpsStandardInternational = 5;
    public const UpsExpressSaverInternational = 6;
    public const UpsExpedited = 8;
    public const UpsAccessPointToHome = 13;
    public const UpsAccessPointToAccessPoint = 14;
    public const UpsHomeToAccessPoint = 15;
    public const UpsHomeToAccessPointInternational = 16;
    public const DpdCourier = 21;
    public const DpdCourierEurope = 22;
    public const DpdPickupHomeToPoint = 23;
    public const DpdCourierBy930 = 24;
    public const DpdCourierBy12 = 25;
    public const DpdPickupPointToPoint = 26;
    public const DpdMaxInternational = 27;
    public const DpdAllegroSmart = 28;
    public const DpdPickupEurope = 29;
    public const DpdPickupPointToHome = 30;
    public const DpdPickupHomeToMachine = 32;
    public const DpdPickupPointToMachine = 33;
    public const InPostPaczkomatAllegroSmart = 40;
    public const InPostPaczkomat = 41;
    public const InPostCourier = 42;
    public const InPostPaczkomatToHome = 43;
    public const InPostInternationalHomeToPoint = 45;
    public const InPostInternationalPaczkomat = 46;
    public const InPostFastReturnsPaczkomatToHome = 47;
    public const OrlenPaczkaPointToPoint = 50;
    public const OrlenPaczkaHomeToPoint = 53;
    public const PocztexCourierHomeToHome = 60;
    public const PocztexCourierBy9 = 61;
    public const PocztexCourierBy12 = 62;
    public const PocztexCourierBy17 = 63;
    public const PocztexCourierHomeToPoint = 64;
    public const PocztexPointToHome = 65;
    public const PocztexPointToPoint = 66;
    public const PocztexAllegroSmart = 67;
    public const PocztexAllegroSmartPointToPoint = 68;
    public const PocztexCargo = 69;
    public const PocztaPolskaUkraine = 71;
    public const DhlParcelCourier = 82;
    public const DhlParcelCourierBy12 = 83;
    public const DhlParcelCourierBy9 = 84;
    public const DhlPopHomeToPoint = 86;
    public const DhlPopPointToHome = 87;
    public const DhlAlsendoInternationalWarehouseToHome = 90;
    public const DhlParcelConnectHomeToHome = 91;
    public const DhlParcelConnectHomeToPoint = 92;
    public const RabenInternational = 140;
    public const GeisCargo = 150;
    public const FedExCourier = 151;
    public const FedExInternationalEconomy = 153;
    public const AlsendoDpdGermany = 191;
    public const GlsInternational = 200;
    public const GlsCourier = 201;
    public const GlsDomesticHomeToHome = 202;
    public const GlsDomesticHomeToPoint = 203;
    public const GlsDomesticPointToHome = 204;
    public const GlsInternationalHomeToHome = 205;
    public const GlsInternationalHomeToPoint = 206;
    public const GlsInternationalPointToHome = 207;
    public const GlsInternationalImportPointToHome = 208;
    public const HellmannDomestic = 230;
    public const HellmannInternational = 231;
    public const RhenusLogistics = 240;
    public const GeodisDomestic = 250;
    public const GeodisInternational = 251;
    public const AmbroExpress = 260;
    public const AmbroExpressInternational = 261;
    public const AlsendoInternationalCblWarehouseToHome = 310;
    public const AlsendoInternationalCblWarehouseToPoint = 311;
    public const AlsendoInternationalPpCblPointToHome = 312;
    public const AlsendoInternationalPpCblPointToPoint = 315;
    public const PacketaLinehaulPointToHome = 313;
    public const PacketaLinehaulPointToPoint = 314;
    public const PacketaWarehouseToHome = 316;
    public const PacketaWarehouseToPoint = 317;

    /** @var int */
    public $value;

    /** @var array<int, self> */
    private static $instances = [];

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Create a Service instance from the integer service ID.
     *
     * @throws InvalidArgumentException when the service ID is unknown
     */
    public static function from(int $value): self
    {
        $service = self::tryFrom($value);
        if ($service === null) {
            throw new InvalidArgumentException(sprintf('Unknown service ID: %d', $value));
        }

        return $service;
    }

    /**
     * Try to create a Service instance, returning null for unknown IDs.
     */
    public static function tryFrom(int $value): ?self
    {
        if (!isset(self::codeMap()[$value])) {
            return null;
        }

        if (!isset(self::$instances[$value])) {
            self::$instances[$value] = new self($value);
        }

        return self::$instances[$value];
    }

    /**
     * All known services.
     *
     * @return self[]
     */
    public static function cases(): array
    {
        return array_map(static function (int $value): self {
            return self::from($value);
        }, array_keys(self::codeMap()));
    }

    /**
     * Get the original API service code.
     */
    public function code(): string
    {
        return self::codeMap()[$this->value];
    }

    /**
     * Get the human-readable service name.
     */
    public function label(): string
    {
        return self::labelMap()[$this->value];
    }

    /**
     * Get the supplier name for this service.
     */
    public function supplier(): string
    {
        return self::supplierMap()[$this->value];
    }

    /**
     * @return array<int, string>
     */
    private static function codeMap(): array
    {
        return [
            self::UpsStandard => 'UPS_K_STANDARD',
            self::UpsExpressSaver => 'UPS_K_EX_SAV',
            self::UpsExpressBy12 => 'UPS_K_EX',
            self::UpsExpressPlusBy9 => 'UPS_K_EXP_PLUS',
            self::UpsStandardInternational => 'UPS_Z_STANDARD',
            self::UpsExpressSaverInternational => 'UPS_Z_EX_SAV',
            self::UpsExpedited => 'UPS_Z_EXPEDITED',
            self::UpsAccessPointToHome => 'UPS_K_STD_APDR',
            self::UpsAccessPointToAccessPoint => 'UPS_K_STD_APAP',
            self::UpsHomeToAccessPoint => 'UPS_K_STD_DRAP',
            self::UpsHomeToAccessPointInternational => 'UPS_F_STD_DRAP',
            self::DpdCourier => 'DPD_CLASSIC',
            self::DpdCourierEurope => 'DPD_CLASSIC_FOREIGN',
            self::DpdPickupHomeToPoint => 'DPD_PICKUP',
            self::DpdCourierBy930 => 'DPD_CLASSIC_0930',
            self::DpdCourierBy12 => 'DPD_CLASSIC_1200',
            self::DpdPickupPointToPoint => 'DPD_PICKUP_P2P',
            self::DpdMaxInternational => 'DPD_MAX_FOREIGN',
            self::DpdAllegroSmart => 'ALLEGRO_DPD',
            self::DpdPickupEurope => 'DPD_PICKUP_FOREIGN',
            self::DpdPickupPointToHome => 'DPD_PICKUP_P2D',
            self::DpdPickupHomeToMachine => 'DPD_PICKUP_D2A',
            self::DpdPickupPointToMachine => 'DPD_PICKUP_P2A',
            self::InPostPaczkomatAllegroSmart => 'PACZKOMAT_ALLEGRO',
            self::InPostPaczkomat => 'PACZKOMAT',
            self::InPostCourier => 'INPOST',
            self::InPostPaczkomatToHome => 'INPOST_P2D',
            self::InPostInternationalHomeToPoint => 'INPOST_INTERNATIONAL_D2P',
            self::InPostInternationalPaczkomat => 'INPOST_INTERNATIONAL_P2P',
            self::InPostFastReturnsPaczkomatToHome => 'INPOST_FAST_RETURNS_P2D',
            self::OrlenPaczkaPointToPoint => 'PWR',
            self::OrlenPaczkaHomeToPoint => 'ORLEN_D2P',
            self::PocztexCourierHomeToHome => 'POCZTEX',
            self::PocztexCourierBy9 => 'POCZTEX_9',
            self::PocztexCourierBy12 => 'POCZTEX_12',
            self::PocztexCourierBy17 => 'POCZTEX_17',
            self::PocztexCourierHomeToPoint => 'POCZTEX_D2P',
            self::PocztexPointToHome => 'POCZTEX_P2D',
            self::PocztexPointToPoint => 'POCZTEX_P2P',
            self::PocztexAllegroSmart => 'ALLEGRO_POCZTEX',
            self::PocztexAllegroSmartPointToPoint => 'ALLEGRO_POCZTEX_POINT2POINT',
            self::PocztexCargo => 'POCZTEX_CARGO',
            self::PocztaPolskaUkraine => 'POCZTA_UA',
            self::DhlParcelCourier => 'DHLSTD',
            self::DhlParcelCourierBy12 => 'DHL12',
            self::DhlParcelCourierBy9 => 'DHL09',
            self::DhlPopHomeToPoint => 'DHLPARCEL',
            self::DhlPopPointToHome => 'DHL_P2D',
            self::DhlAlsendoInternationalWarehouseToHome => 'DHL_DE',
            self::DhlParcelConnectHomeToHome => 'DHL_INTERNATIONAL_D2D',
            self::DhlParcelConnectHomeToPoint => 'DHL_INTERNATIONAL_D2P',
            self::RabenInternational => 'RABEN_FOREIGN',
            self::GeisCargo => 'KEX_EXPRESS',
            self::FedExCourier => 'FEDEX',
            self::FedExInternationalEconomy => 'FEDEX_FOREIGN_ECONOMY',
            self::AlsendoDpdGermany => 'APACZKA_DPD',
            self::GlsInternational => 'GLS_FOREIGN',
            self::GlsCourier => 'GLS',
            self::GlsDomesticHomeToHome => 'GLS_DOMESTIC_D2D',
            self::GlsDomesticHomeToPoint => 'GLS_DOMESTIC_D2P',
            self::GlsDomesticPointToHome => 'GLS_DOMESTIC_P2D',
            self::GlsInternationalHomeToHome => 'GLS_FOREIGN_D2D',
            self::GlsInternationalHomeToPoint => 'GLS_FOREIGN_D2P',
            self::GlsInternationalPointToHome => 'GLS_FOREIGN_P2D',
            self::GlsInternationalImportPointToHome => 'GLS_FOREIGN_IMPORT_P2D',
            self::HellmannDomestic => 'HELLMANN',
            self::HellmannInternational => 'HELLMANN_FOREIGN',
            self::RhenusLogistics => 'RHENUS',
            self::GeodisDomestic => 'PEKAES',
            self::GeodisInternational => 'PEKAES_FOREIGN',
            self::AmbroExpress => 'AMBRO',
            self::AmbroExpressInternational => 'AMBRO_FOREIGN',
            self::AlsendoInternationalCblWarehouseToHome => 'CBL_P2D',
            self::AlsendoInternationalCblWarehouseToPoint => 'CBL_P2P',
            self::AlsendoInternationalPpCblPointToHome => 'PP_CBL_LINEHAUL_P2D',
            self::AlsendoInternationalPpCblPointToPoint => 'LINEHAUL_PP_CBL_P2P',
            self::PacketaLinehaulPointToHome => 'PP_PACKETA_LINEHAUL_P2D',
            self::PacketaLinehaulPointToPoint => 'PP_PACKETA_LINEHAUL_P2P',
            self::PacketaWarehouseToHome => 'PACKETA_P2D',
            self::PacketaWarehouseToPoint => 'PACKETA_P2P',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function labelMap(): array
    {
        return [
            self::UpsStandard => 'UPS Standard',
            self::UpsExpressSaver => 'UPS Express Saver',
            self::UpsExpressBy12 => 'UPS Express Plus do 12:00',
            self::UpsExpressPlusBy9 => 'UPS Express Plus do 9:00',
            self::UpsStandardInternational => 'UPS Standard (zagranica)',
            self::UpsExpressSaverInternational => 'UPS Express Saver (zagranica)',
            self::UpsExpedited => 'UPS Expedited',
            self::UpsAccessPointToHome => 'UPS AP Punkt-Drzwi',
            self::UpsAccessPointToAccessPoint => 'UPS AP Punkt-Punkt',
            self::UpsHomeToAccessPoint => 'UPS AP Drzwi-Punkt',
            self::UpsHomeToAccessPointInternational => 'UPS AP Drzwi-Punkt (zagranica)',
            self::DpdCourier => 'DPD Kurier',
            self::DpdCourierEurope => 'DPD Kurier Europa',
            self::DpdPickupHomeToPoint => 'DPD Pickup Drzwi-Punkt',
            self::DpdCourierBy930 => 'DPD Kurier do 9:30',
            self::DpdCourierBy12 => 'DPD Kurier do 12:00',
            self::DpdPickupPointToPoint => 'DPD Pickup Punkt-Punkt',
            self::DpdMaxInternational => 'DPD Max',
            self::DpdAllegroSmart => 'Allegro SMART DPD Kurier',
            self::DpdPickupEurope => 'DPD Pickup Europa',
            self::DpdPickupPointToHome => 'DPD Pickup Punkt-Drzwi',
            self::DpdPickupHomeToMachine => 'DPD Pickup Drzwi-Automat',
            self::DpdPickupPointToMachine => 'DPD Pickup Punkt-Automat',
            self::InPostPaczkomatAllegroSmart => 'Allegro SMART Paczkomat InPost',
            self::InPostPaczkomat => 'InPost Paczkomat',
            self::InPostCourier => 'InPost Kurier',
            self::InPostPaczkomatToHome => 'InPost Paczkomat-Drzwi',
            self::InPostInternationalHomeToPoint => 'InPost International Drzwi-Punkt',
            self::InPostInternationalPaczkomat => 'InPost International Paczkomat',
            self::InPostFastReturnsPaczkomatToHome => 'Szybkie Zwroty Paczkomat-Drzwi',
            self::OrlenPaczkaPointToPoint => 'Orlen Paczka Punkt-Punkt',
            self::OrlenPaczkaHomeToPoint => 'Orlen Paczka Drzwi-Punkt',
            self::PocztexCourierHomeToHome => 'Pocztex Kurier Drzwi-Drzwi',
            self::PocztexCourierBy9 => 'Pocztex Kurier do 9:00',
            self::PocztexCourierBy12 => 'Pocztex Kurier do 12:00',
            self::PocztexCourierBy17 => 'Pocztex Kurier do 17:00',
            self::PocztexCourierHomeToPoint => 'Pocztex Kurier Drzwi-Punkt',
            self::PocztexPointToHome => 'Pocztex Punkt Punkt-Drzwi',
            self::PocztexPointToPoint => 'Pocztex Punkt Punkt-Punkt',
            self::PocztexAllegroSmart => 'Allegro SMART Pocztex',
            self::PocztexAllegroSmartPointToPoint => 'Allegro SMART Pocztex Punkty',
            self::PocztexCargo => 'Poczta Polska Palety',
            self::PocztaPolskaUkraine => 'Poczta Polska Ukraina',
            self::DhlParcelCourier => 'DHL Parcel Kurier',
            self::DhlParcelCourierBy12 => 'DHL Parcel Kurier do 12:00',
            self::DhlParcelCourierBy9 => 'DHL Parcel Kurier do 9:00',
            self::DhlPopHomeToPoint => 'DHL POP do punktu',
            self::DhlPopPointToHome => 'DHL POP Punkt-Drzwi',
            self::DhlAlsendoInternationalWarehouseToHome => 'Alsendo International DHL Magazyn-Drzwi',
            self::DhlParcelConnectHomeToHome => 'DHL Parcel Connect Drzwi-Drzwi',
            self::DhlParcelConnectHomeToPoint => 'DHL Parcel Connect Drzwi-Punkt',
            self::RabenInternational => 'Raben',
            self::GeisCargo => 'Geis Cargo',
            self::FedExCourier => 'FEDEX Kurier',
            self::FedExInternationalEconomy => 'FedEx International Economy',
            self::AlsendoDpdGermany => 'Apaczka Niemcy',
            self::GlsInternational => 'GLS Zagranica',
            self::GlsCourier => 'GLS Kurier',
            self::GlsDomesticHomeToHome => 'GLS Kurier Drzwi-Drzwi',
            self::GlsDomesticHomeToPoint => 'GLS Kurier Drzwi-Punkt',
            self::GlsDomesticPointToHome => 'GLS Kurier Punkt-Drzwi',
            self::GlsInternationalHomeToHome => 'GLS Zagranica Drzwi-Drzwi',
            self::GlsInternationalHomeToPoint => 'GLS Zagranica Drzwi-Punkt',
            self::GlsInternationalPointToHome => 'GLS Zagranica Punkt-Drzwi',
            self::GlsInternationalImportPointToHome => 'GLS Zagranica Import Punkt-Drzwi',
            self::HellmannDomestic => 'Hellmann',
            self::HellmannInternational => 'Hellmann (zagranica)',
            self::RhenusLogistics => 'Rhenus Logistics',
            self::GeodisDomestic => 'Geodis',
            self::GeodisInternational => 'Geodis (zagranica)',
            self::AmbroExpress => 'Ambro Express',
            self::AmbroExpressInternational => 'Ambro Express Zagranica',
            self::AlsendoInternationalCblWarehouseToHome => 'Alsendo International Magazyn-Drzwi',
            self::AlsendoInternationalCblWarehouseToPoint => 'Alsendo International Magazyn-Punkt',
            self::AlsendoInternationalPpCblPointToHome => 'Alsendo International Punkt-Drzwi',
            self::AlsendoInternationalPpCblPointToPoint => 'Alsendo International Punkt-Punkt',
            self::PacketaLinehaulPointToHome => 'Packeta Punkt-Drzwi',
            self::PacketaLinehaulPointToPoint => 'Packeta Punkt-Punkt',
            self::PacketaWarehouseToHome => 'Packeta Magazyn-Drzwi',
            self::PacketaWarehouseToPoint => 'Packeta Magazyn-Punkt',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function supplierMap(): array
    {
        return [
            self::UpsStandard => 'UPS',
            self::UpsExpressSaver => 'UPS',
            self::UpsExpressBy12 => 'UPS',
            self::UpsExpressPlusBy9 => 'UPS',
            self::UpsStandardInternational => 'UPS',
            self::UpsExpressSaverInternational => 'UPS',
            self::UpsExpedited => 'UPS',
            self::UpsAccessPointToHome => 'UPS',
            self::UpsAccessPointToAccessPoint => 'UPS',
            self::UpsHomeToAccessPoint => 'UPS',
            self::UpsHomeToAccessPointInternational => 'UPS',
            self::DpdCourier => 'DPD',
            self::DpdCourierEurope => 'DPD',
            self::DpdPickupHomeToPoint => 'DPD',
            self::DpdCourierBy930 => 'DPD',
            self::DpdCourierBy12 => 'DPD',
            self::DpdPickupPointToPoint => 'DPD',
            self::DpdMaxInternational => 'DPD',
            self::DpdAllegroSmart => 'DPD',
            self::DpdPickupEurope => 'DPD',
            self::DpdPickupPointToHome => 'DPD',
            self::DpdPickupHomeToMachine => 'DPD',
            self::DpdPickupPointToMachine => 'DPD',
            self::InPostPaczkomatAllegroSmart => 'INPOST',
            self::InPostPaczkomat => 'INPOST',
            self::InPostCourier => 'INPOST',
            self::InPostPaczkomatToHome => 'INPOST',
            self::InPostInternationalHomeToPoint => 'INPOST',
            self::InPostInternationalPaczkomat => 'INPOST',
            self::InPostFastReturnsPaczkomatToHome => 'INPOST',
            self::OrlenPaczkaPointToPoint => 'ORLEN',
            self::OrlenPaczkaHomeToPoint => 'ORLEN',
            self::PocztexCourierHomeToHome => 'POCZTA',
            self::PocztexCourierBy9 => 'POCZTA',
            self::PocztexCourierBy12 => 'POCZTA',
            self::PocztexCourierBy17 => 'POCZTA',
            self::PocztexCourierHomeToPoint => 'POCZTA',
            self::PocztexPointToHome => 'POCZTA',
            self::PocztexPointToPoint => 'POCZTA',
            self::PocztexAllegroSmart => 'POCZTA',
            self::PocztexAllegroSmartPointToPoint => 'POCZTA',
            self::PocztexCargo => 'POCZTA',
            self::PocztaPolskaUkraine => 'POCZTA',
            self::DhlParcelCourier => 'DHL',
            self::DhlParcelCourierBy12 => 'DHL',
            self::DhlParcelCourierBy9 => 'DHL',
            self::DhlPopHomeToPoint => 'DHL_PARCEL',
            self::DhlPopPointToHome => 'DHL',
            self::DhlAlsendoInternationalWarehouseToHome => 'DHL_DE',
            self::DhlParcelConnectHomeToHome => 'DHL_INT',
            self::DhlParcelConnectHomeToPoint => 'DHL_INT',
            self::RabenInternational => 'RABEN',
            self::GeisCargo => 'KEX',
            self::FedExCourier => 'FEDEX',
            self::FedExInternationalEconomy => 'FEDEX',
            self::AlsendoDpdGermany => 'DPD',
            self::GlsInternational => 'GLS',
            self::GlsCourier => 'GLS',
            self::GlsDomesticHomeToHome => 'GLS',
            self::GlsDomesticHomeToPoint => 'GLS',
            self::GlsDomesticPointToHome => 'GLS',
            self::GlsInternationalHomeToHome => 'GLS',
            self::GlsInternationalHomeToPoint => 'GLS',
            self::GlsInternationalPointToHome => 'GLS',
            self::GlsInternationalImportPointToHome => 'GLS',
            self::HellmannDomestic => 'HELLMANN',
            self::HellmannInternational => 'HELLMANN',
            self::RhenusLogistics => 'RHENUS',
            self::GeodisDomestic => 'PEKAES',
            self::GeodisInternational => 'PEKAES',
            self::AmbroExpress => 'AMBRO',
            self::AmbroExpressInternational => 'AMBRO',
            self::AlsendoInternationalCblWarehouseToHome => 'CBL',
            self::AlsendoInternationalCblWarehouseToPoint => 'CBL',
            self::AlsendoInternationalPpCblPointToHome => 'PP_CBL',
            self::AlsendoInternationalPpCblPointToPoint => 'PP_CBL',
            self::PacketaLinehaulPointToHome => 'PP_PACKETA',
            self::PacketaLinehaulPointToPoint => 'PP_PACKETA',
            self::PacketaWarehouseToHome => 'PACKETA',
            self::PacketaWarehouseToPoint => 'PACKETA',
        ];
    }
}
