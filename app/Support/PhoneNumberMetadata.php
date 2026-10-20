<?php

namespace App\Support;

use Giggsey\Locale\Locale;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberMetadata
{
    private const COMMON_REGIONS = [
        'AU', 'BD', 'BR', 'CA', 'CN', 'DE', 'ES', 'FR', 'GB', 'HK', 'ID', 'IE', 'IN', 'IT',
        'JP', 'KR', 'MX', 'MY', 'NG', 'NL', 'NZ', 'PH', 'PK', 'SA', 'SG', 'TH', 'TW', 'AE',
        'US', 'VN', 'ZA',
    ];

    /** @return list<string> */
    public static function supportedRegions(): array
    {
        static $regions = null;

        if ($regions !== null) {
            return $regions;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        $regions = array_values(array_filter(
            $phoneUtil->getSupportedRegions(),
            fn (string $region): bool => $region !== PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY
                && $phoneUtil->getCountryCodeForRegion($region) > 0,
        ));

        return $regions;
    }

    /** @return list<array{region: string, name: string, code: string, max_digits: int}> */
    public static function countryOptions(?string $includeRegion = null): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $options = [];

        $regionsToShow = self::COMMON_REGIONS;
        $includeRegion = $includeRegion === null ? null : strtoupper($includeRegion);
        if ($includeRegion !== null && in_array($includeRegion, self::supportedRegions(), true)) {
            $regionsToShow[] = $includeRegion;
        }

        foreach (array_unique($regionsToShow) as $region) {
            $callingCode = $phoneUtil->getCountryCodeForRegion($region);
            $name = Locale::getDisplayRegion('en_'.$region, 'en');

            $options[] = [
                'region' => $region,
                'name' => $name !== '' ? $name : $region,
                'code' => '+'.$callingCode,
                'max_digits' => self::maxInputDigitsForRegion($region),
            ];
        }

        usort($options, fn (array $left, array $right): int => strcasecmp($left['name'], $right['name']));

        return $options;
    }

    public static function maxInputDigitsForRegion(string $region): int
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $metadata = $region === PhoneNumberUtil::REGION_CODE_FOR_NON_GEO_ENTITY
            ? null
            : $phoneUtil->getMetadataForRegion($region);

        if ($metadata === null) {
            return 15;
        }

        $lengths = [];
        foreach ([$metadata->getFixedLine(), $metadata->getMobile()] as $description) {
            foreach ($description->getPossibleLength() as $length) {
                if ($length > 0) {
                    $lengths[] = $length;
                }
            }
        }

        if ($lengths === []) {
            foreach ($metadata->getGeneralDesc()->getPossibleLength() as $length) {
                if ($length > 0) {
                    $lengths[] = $length;
                }
            }
        }

        $nationalPrefix = $metadata->getNationalPrefix() ?? '';
        $hasDomesticPrefix = $phoneUtil->getCountryCodeForRegion($region) !== 1
            && preg_match('/^[0-9]+$/', $nationalPrefix) === 1;
        $prefixLength = $hasDomesticPrefix ? strlen($nationalPrefix) : 0;

        return ($lengths === [] ? 15 : max($lengths)) + $prefixLength;
    }
}
