<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\Enum;

/**
 * Available shipping services accessible through Apaczka API v2.
 *
 * Each case name is a human-readable identifier, and the backed value is the service ID.
 */
enum Service: int
{
    // UPS
    case UpsStandard = 1;
    case UpsExpressSaver = 2;
    case UpsExpressBy12 = 3;
    case UpsExpressPlusBy9 = 4;
    case UpsStandardInternational = 5;
    case UpsExpressSaverInternational = 6;
    case UpsExpedited = 8;
    case UpsAccessPointToHome = 13;
    case UpsAccessPointToAccessPoint = 14;
    case UpsHomeToAccessPoint = 15;
    case UpsHomeToAccessPointInternational = 16;

    // DPD
    case DpdCourier = 21;
    case DpdCourierEurope = 22;
    case DpdPickupHomeToPoint = 23;
    case DpdCourierBy930 = 24;
    case DpdCourierBy12 = 25;
    case DpdPickupPointToPoint = 26;
    case DpdMaxInternational = 27;
    case DpdAllegroSmart = 28;
    case DpdPickupEurope = 29;
    case DpdPickupPointToHome = 30;

    // InPost
    case InPostPaczkomatAllegroSmart = 40;
    case InPostPaczkomat = 41;
    case InPostCourier = 42;
    case InPostPaczkomatToHome = 43;
    case InPostInternationalHomeToPoint = 45;
    case InPostInternationalPaczkomat = 46;
    case InPostFastReturnsPaczkomatToHome = 47;

    // Orlen Paczka
    case OrlenPaczkaPointToPoint = 50;
    case OrlenPaczkaHomeToPoint = 53;

    // Poczta Polska
    case PocztexCourierHomeToHome = 60;
    case PocztexCourierBy9 = 61;
    case PocztexCourierBy12 = 62;
    case PocztexCourierBy17 = 63;
    case PocztexCourierHomeToPoint = 64;
    case PocztexPointToHome = 65;
    case PocztexPointToPoint = 66;
    case PocztexAllegroSmart = 67;
    case PocztexAllegroSmartPointToPoint = 68;
    case PocztexCargo = 69;

    // DHL
    case DhlParcelCourier = 82;
    case DhlParcelCourierBy12 = 83;
    case DhlParcelCourierBy9 = 84;
    case DhlPopHomeToPoint = 86;
    case DhlPopPointToHome = 87;
    case DhlAlsendoInternationalWarehouseToHome = 90;
    case DhlParcelConnectHomeToHome = 91;
    case DhlParcelConnectHomeToPoint = 92;

    // Raben
    case RabenInternational = 140;

    // Geis Cargo
    case GeisCargo = 150;

    // FedEx
    case FedExCourier = 151;
    case FedExInternationalEconomy = 153;

    // Apaczka DPD Niemcy
    case ApaczkaDpdGermany = 191;

    // GLS
    case GlsInternational = 200;
    case GlsCourier = 201;
    case GlsDomesticHomeToHome = 202;
    case GlsDomesticHomeToPoint = 203;
    case GlsDomesticPointToHome = 204;
    case GlsInternationalHomeToHome = 205;
    case GlsInternationalHomeToPoint = 206;
    case GlsInternationalPointToHome = 207;
    case GlsInternationalImportPointToHome = 208;

    // Hellmann
    case HellmannDomestic = 230;
    case HellmannInternational = 231;

    // Rhenus
    case RhenusLogistics = 240;

    // Geodis
    case GeodisDomestic = 250;
    case GeodisInternational = 251;

    // Ambro Express
    case AmbroExpress = 260;
    case AmbroExpressInternational = 261;

    // Alsendo International (CBL)
    case AlsendoInternationalCblWarehouseToHome = 310;
    case AlsendoInternationalCblWarehouseToPoint = 311;

    // Alsendo International (PP CBL)
    case AlsendoInternationalPpCblPointToHome = 312;
    case AlsendoInternationalPpCblPointToPoint = 315;

    // Packeta (PP Linehaul)
    case PacketaLinehaulPointToHome = 313;
    case PacketaLinehaulPointToPoint = 314;

    // Packeta
    case PacketaWarehouseToHome = 316;
    case PacketaWarehouseToPoint = 317;

    /**
     * Get the original API service code.
     */
    public function code(): string
    {
        return match ($this) {
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
            self::ApaczkaDpdGermany => 'APACZKA_DPD',
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
        };
    }

    /**
     * Get the human-readable service name.
     */
    public function label(): string
    {
        return match ($this) {
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
            self::ApaczkaDpdGermany => 'Apaczka Niemcy',
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
        };
    }

    /**
     * Get the supplier name for this service.
     */
    public function supplier(): string
    {
        return match ($this) {
            self::UpsStandard, self::UpsExpressSaver, self::UpsExpressBy12, self::UpsExpressPlusBy9,
            self::UpsStandardInternational, self::UpsExpressSaverInternational, self::UpsExpedited,
            self::UpsAccessPointToHome, self::UpsAccessPointToAccessPoint,
            self::UpsHomeToAccessPoint, self::UpsHomeToAccessPointInternational => 'UPS',

            self::DpdCourier, self::DpdCourierEurope, self::DpdPickupHomeToPoint, self::DpdCourierBy930,
            self::DpdCourierBy12, self::DpdPickupPointToPoint, self::DpdMaxInternational, self::DpdAllegroSmart,
            self::DpdPickupEurope, self::DpdPickupPointToHome, self::ApaczkaDpdGermany => 'DPD',

            self::InPostPaczkomatAllegroSmart, self::InPostPaczkomat, self::InPostCourier,
            self::InPostPaczkomatToHome, self::InPostInternationalHomeToPoint,
            self::InPostInternationalPaczkomat, self::InPostFastReturnsPaczkomatToHome => 'INPOST',

            self::OrlenPaczkaPointToPoint, self::OrlenPaczkaHomeToPoint => 'ORLEN',

            self::PocztexCourierHomeToHome, self::PocztexCourierBy9, self::PocztexCourierBy12,
            self::PocztexCourierBy17, self::PocztexCourierHomeToPoint, self::PocztexPointToHome,
            self::PocztexPointToPoint, self::PocztexAllegroSmart,
            self::PocztexAllegroSmartPointToPoint, self::PocztexCargo => 'POCZTA',

            self::DhlParcelCourier, self::DhlParcelCourierBy12, self::DhlParcelCourierBy9,
            self::DhlPopPointToHome => 'DHL',
            self::DhlPopHomeToPoint => 'DHL_PARCEL',
            self::DhlAlsendoInternationalWarehouseToHome => 'DHL_DE',
            self::DhlParcelConnectHomeToHome, self::DhlParcelConnectHomeToPoint => 'DHL_INT',

            self::RabenInternational => 'RABEN',
            self::GeisCargo => 'KEX',
            self::FedExCourier, self::FedExInternationalEconomy => 'FEDEX',

            self::GlsInternational, self::GlsCourier, self::GlsDomesticHomeToHome,
            self::GlsDomesticHomeToPoint, self::GlsDomesticPointToHome,
            self::GlsInternationalHomeToHome, self::GlsInternationalHomeToPoint,
            self::GlsInternationalPointToHome, self::GlsInternationalImportPointToHome => 'GLS',

            self::HellmannDomestic, self::HellmannInternational => 'HELLMANN',
            self::RhenusLogistics => 'RHENUS',
            self::GeodisDomestic, self::GeodisInternational => 'PEKAES',
            self::AmbroExpress, self::AmbroExpressInternational => 'AMBRO',

            self::AlsendoInternationalCblWarehouseToHome, self::AlsendoInternationalCblWarehouseToPoint => 'CBL',
            self::AlsendoInternationalPpCblPointToHome, self::AlsendoInternationalPpCblPointToPoint => 'PP_CBL',
            self::PacketaLinehaulPointToHome, self::PacketaLinehaulPointToPoint => 'PP_PACKETA',
            self::PacketaWarehouseToHome, self::PacketaWarehouseToPoint => 'PACKETA',
        };
    }
}
