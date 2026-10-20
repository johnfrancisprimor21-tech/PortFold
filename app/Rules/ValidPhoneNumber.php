<?php

namespace App\Rules;

use App\Support\PhoneNumberMetadata;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class ValidPhoneNumber implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        $region = null;

        if ($attribute === 'phone_number') {
            $callingCode = $this->data['phone_country_code'] ?? null;
            $selectedRegion = $this->data['phone_region'] ?? null;
            if (! is_string($callingCode) || ! preg_match('/^\+[1-9][0-9]{0,2}$/', $callingCode)) {
                $fail('Choose a valid country calling code.');

                return;
            }

            if (! is_string($selectedRegion) || ! in_array($selectedRegion, PhoneNumberMetadata::supportedRegions(), true)) {
                $fail('Choose a supported country.');

                return;
            }

            $region = $selectedRegion;
            if ($phoneUtil->getCountryCodeForRegion($region) !== (int) substr($callingCode, 1)) {
                $fail('The country and calling code do not match.');

                return;
            }
        }

        try {
            $number = $phoneUtil->parse(trim($value), $region);
        } catch (NumberParseException) {
            $fail('Enter a valid phone number for the selected country.');

            return;
        }

        $isValid = $region === null
            ? $phoneUtil->isValidNumber($number)
            : $phoneUtil->isValidNumberForRegion($number, $region);

        if (! $isValid) {
            $fail('Enter a valid phone number for the selected country.');

            return;
        }

        $numberRegion = $region ?? $phoneUtil->getRegionCodeForNumber($number);
        if (is_string($numberRegion)) {
            $digits = preg_replace('/\D+/', '', trim($value)) ?? '';
            if (str_starts_with(trim($value), '+')) {
                $digits = substr($digits, strlen((string) $number->getCountryCode()));
            }

            if (strlen($digits) > PhoneNumberMetadata::maxInputDigitsForRegion($numberRegion)) {
                $fail('The phone number is too long for the selected country.');
            }
        }
    }
}
