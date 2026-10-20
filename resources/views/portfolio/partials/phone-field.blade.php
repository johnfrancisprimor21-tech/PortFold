@php
    $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
    $phoneValue = (string) old('phone', data_get($info ?? null, 'phone', ''));
    $phoneRegion = old('phone_region');
    $phoneCountryCode = old('phone_country_code');
    $phoneNumber = old('phone_number');

    if ($phoneNumber === null) {
        $phoneNumber = $phoneValue;

        if (trim($phoneValue) !== '') {
            try {
                $savedPhone = $phoneUtil->parse($phoneValue, null);
                $phoneRegion = $phoneRegion ?? $phoneUtil->getRegionCodeForNumber($savedPhone);
                $phoneCountryCode = $phoneCountryCode ?? '+'.$savedPhone->getCountryCode();
                $phoneNumber = $phoneUtil->format($savedPhone, \libphonenumber\PhoneNumberFormat::NATIONAL);
            } catch (\libphonenumber\NumberParseException) {
                // Keep an unparseable legacy value visible so the owner can correct it.
            }
        }
    }

    $phoneRegion = is_string($phoneRegion) ? strtoupper($phoneRegion) : 'PH';
    $phoneOptions = \App\Support\PhoneNumberMetadata::countryOptions($phoneRegion);
    $selectedPhoneOption = collect($phoneOptions)->firstWhere('region', $phoneRegion)
        ?? collect($phoneOptions)->firstWhere('region', 'PH');
    $phoneRegion = $selectedPhoneOption['region'];
    $phoneCountryCode = $selectedPhoneOption['code'];
    $phoneMaxDigits = $selectedPhoneOption['max_digits'];
    $phoneNumber = trim((string) ($phoneNumber ?? ''));
@endphp
<fieldset class="form-group phone-field">
    <legend>Contact number</legend>
    <div class="phone-control">
        <div>
            <label class="sr-only" for="phone_region">Country</label>
            <select id="phone_region" name="phone_region" autocomplete="country" aria-describedby="phone-help">
                @foreach($phoneOptions as $phoneOption)
                    <option value="{{ $phoneOption['region'] }}" data-calling-code="{{ $phoneOption['code'] }}" data-max-digits="{{ $phoneOption['max_digits'] }}" {{ $phoneRegion === $phoneOption['region'] ? 'selected' : '' }}>{{ $phoneOption['name'] }} ({{ $phoneOption['code'] }})</option>
                @endforeach
            </select>
            <input type="hidden" id="phone_country_code" name="phone_country_code" value="{{ $phoneCountryCode }}">
        </div>
        <div>
            <label class="sr-only" for="phone_number">Phone number</label>
            <input type="tel" id="phone_number" name="phone_number" value="{{ $phoneNumber }}" autocomplete="tel-national" inputmode="numeric" maxlength="{{ min(40, $phoneMaxDigits + 8) }}" data-max-digits="{{ $phoneMaxDigits }}" placeholder="0912 345 6789" aria-describedby="phone-help">
        </div>
    </div>
    <span class="form-help" id="phone-help" data-phone-help>Enter a valid national phone number for {{ $selectedPhoneOption['name'] }}, up to {{ $phoneMaxDigits }} digits{{ $phoneRegion === 'PH' ? ', including a leading 0 when used' : '' }}.</span>
    @error('phone')<span class="field-error" role="alert">{{ $message }}</span>@enderror
    @error('phone_region')<span class="field-error" role="alert">{{ $message }}</span>@enderror
    @error('phone_country_code')<span class="field-error" role="alert">{{ $message }}</span>@enderror
    @error('phone_number')<span class="field-error" role="alert">{{ $message }}</span>@enderror
</fieldset>
<script>
(() => {
    const country = document.getElementById('phone_region');
    const callingCodeInput = document.getElementById('phone_country_code');
    const number = document.getElementById('phone_number');
    const help = document.querySelector('[data-phone-help]');
    if (!country || !callingCodeInput || !number || !help) return;

    const updateLimit = () => {
        const option = country.options[country.selectedIndex];
        const callingCode = option?.dataset.callingCode || '+63';
        const maxDigits = Number(option?.dataset.maxDigits || 11);
        const countryName = option?.textContent.split(' (')[0].trim() || 'selected country';
        let value = number.value;

        if (value.trimStart().startsWith(callingCode)) {
            value = value.trimStart().slice(callingCode.length).replace(/^\s*/, '');
        }

        let digitCount = 0;
        let limitedValue = '';
        for (const character of value) {
            if (/[0-9]/.test(character) && ++digitCount > maxDigits) continue;
            limitedValue += character;
        }

        callingCodeInput.value = callingCode;
        number.value = limitedValue;
        number.dataset.maxDigits = String(maxDigits);
        number.maxLength = Math.min(40, maxDigits + 8);
        number.setCustomValidity('');
        help.textContent = `Enter a valid national phone number for ${countryName} (${callingCode}), up to ${maxDigits} digits${country.value === 'PH' ? ', including a leading 0 when used' : ''}.`;
    };

    number.addEventListener('input', updateLimit);
    country.addEventListener('change', updateLimit);
    updateLimit();
})();
</script>
