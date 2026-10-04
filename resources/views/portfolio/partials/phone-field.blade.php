@php
    $phoneCountries = [
        ["Afghanistan", "+93"], ["Albania", "+355"], ["Algeria", "+213"], ["American Samoa", "+1"], ["Andorra", "+376"],
        ["Angola", "+244"], ["Anguilla", "+1"], ["Antigua and Barbuda", "+1"], ["Argentina", "+54"], ["Armenia", "+374"],
        ["Aruba", "+297"], ["Ascension", "+247"], ["Australia", "+61"], ["Austria", "+43"], ["Azerbaijan", "+994"],
        ["Bahamas", "+1"], ["Bahrain", "+973"], ["Bangladesh", "+880"], ["Barbados", "+1"], ["Belarus", "+375"],
        ["Belgium", "+32"], ["Belize", "+501"], ["Benin", "+229"], ["Bermuda", "+1"], ["Bhutan", "+975"],
        ["Bolivia", "+591"], ["Bonaire, Sint Eustatius and Saba", "+599"], ["Bosnia and Herzegovina", "+387"], ["Botswana", "+267"],
        ["Brazil", "+55"], ["British Virgin Islands", "+1"], ["Brunei Darussalam", "+673"], ["Bulgaria", "+359"], ["Burkina Faso", "+226"], ["Burundi", "+257"],
        ["Cabo Verde", "+238"], ["Cambodia", "+855"], ["Cameroon", "+237"], ["Canada", "+1"], ["Cayman Islands", "+1"],
        ["Central African Republic", "+236"], ["Chad", "+235"], ["Chile", "+56"], ["China", "+86"], ["Colombia", "+57"],
        ["Comoros", "+269"], ["Congo", "+242"], ["Cook Islands", "+682"], ["Costa Rica", "+506"], ["Côte d’Ivoire", "+225"],
        ["Croatia", "+385"], ["Cuba", "+53"], ["Curaçao", "+599"], ["Cyprus", "+357"], ["Czech Republic", "+420"],
        ["Democratic People's Republic of Korea", "+850"], ["Democratic Republic of the Congo", "+243"], ["Denmark", "+45"], ["Diego Garcia", "+246"],
        ["Djibouti", "+253"], ["Dominica", "+1"], ["Dominican Republic", "+1"], ["Ecuador", "+593"], ["Egypt", "+20"],
        ["El Salvador", "+503"], ["Equatorial Guinea", "+240"], ["Eritrea", "+291"], ["Estonia", "+372"], ["Eswatini", "+268"], ["Ethiopia", "+251"],
        ["Falkland Islands", "+500"], ["Faroe Islands", "+298"], ["Fiji", "+679"], ["Finland", "+358"], ["France", "+33"],
        ["French Departments and Territories in the Indian Ocean", "+262"], ["French Guiana", "+594"], ["French Polynesia", "+689"],
        ["Gabon", "+241"], ["Gambia", "+220"], ["Georgia", "+995"], ["Germany", "+49"], ["Ghana", "+233"], ["Gibraltar", "+350"],
        ["Greece", "+30"], ["Greenland", "+299"], ["Grenada", "+1"], ["Guadeloupe", "+590"], ["Guam", "+1"],
        ["Guatemala", "+502"], ["Guinea", "+224"], ["Guinea-Bissau", "+245"], ["Guyana", "+592"], ["Haiti", "+509"],
        ["Honduras", "+504"], ["Hong Kong", "+852"], ["Hungary", "+36"], ["Iceland", "+354"], ["India", "+91"],
        ["Indonesia", "+62"], ["Iran", "+98"], ["Iraq", "+964"], ["Ireland", "+353"], ["Israel", "+972"], ["Italy", "+39"],
        ["Jamaica", "+1"], ["Japan", "+81"], ["Jordan", "+962"], ["Kazakhstan", "+7"], ["Kenya", "+254"], ["Kiribati", "+686"],
        ["South Korea", "+82"], ["Kosovo", "+383"], ["Kuwait", "+965"], ["Kyrgyzstan", "+996"], ["Laos", "+856"], ["Latvia", "+371"],
        ["Lebanon", "+961"], ["Lesotho", "+266"], ["Liberia", "+231"], ["Libya", "+218"], ["Liechtenstein", "+423"], ["Lithuania", "+370"], ["Luxembourg", "+352"],
        ["Macao", "+853"], ["Madagascar", "+261"], ["Malawi", "+265"], ["Malaysia", "+60"], ["Maldives", "+960"], ["Mali", "+223"],
        ["Malta", "+356"], ["Marshall Islands", "+692"], ["Martinique", "+596"], ["Mauritania", "+222"], ["Mauritius", "+230"], ["Mexico", "+52"],
        ["Micronesia", "+691"], ["Moldova", "+373"], ["Monaco", "+377"], ["Mongolia", "+976"], ["Montenegro", "+382"], ["Montserrat", "+1"],
        ["Morocco", "+212"], ["Mozambique", "+258"], ["Myanmar", "+95"], ["Namibia", "+264"], ["Nauru", "+674"], ["Nepal", "+977"],
        ["Netherlands", "+31"], ["New Caledonia", "+687"], ["New Zealand", "+64"], ["Nicaragua", "+505"], ["Niger", "+227"], ["Nigeria", "+234"],
        ["Niue", "+683"], ["Norfolk Island", "+672"], ["North Macedonia", "+389"], ["Northern Mariana Islands", "+1"], ["Norway", "+47"],
        ["Oman", "+968"], ["Pakistan", "+92"], ["Palau", "+680"], ["Palestine", "+970"], ["Panama", "+507"], ["Papua New Guinea", "+675"],
        ["Paraguay", "+595"], ["Peru", "+51"], ["Philippines", "+63"], ["Poland", "+48"], ["Portugal", "+351"], ["Puerto Rico", "+1"],
        ["Qatar", "+974"], ["Romania", "+40"], ["Russia", "+7"], ["Rwanda", "+250"], ["Saint Helena and Tristan da Cunha", "+290"],
        ["Saint Kitts and Nevis", "+1"], ["Saint Lucia", "+1"], ["Saint Pierre and Miquelon", "+508"], ["Saint Vincent and the Grenadines", "+1"],
        ["Samoa", "+685"], ["San Marino", "+378"], ["Sao Tome and Principe", "+239"], ["Saudi Arabia", "+966"], ["Senegal", "+221"],
        ["Serbia", "+381"], ["Seychelles", "+248"], ["Sierra Leone", "+232"], ["Singapore", "+65"], ["Sint Maarten", "+1"],
        ["Slovakia", "+421"], ["Slovenia", "+386"], ["Solomon Islands", "+677"], ["Somalia", "+252"], ["South Africa", "+27"],
        ["South Sudan", "+211"], ["Spain", "+34"], ["Sri Lanka", "+94"], ["Sudan", "+249"], ["Suriname", "+597"], ["Sweden", "+46"],
        ["Switzerland", "+41"], ["Syria", "+963"], ["Taiwan", "+886"], ["Tajikistan", "+992"], ["Tanzania", "+255"], ["Thailand", "+66"],
        ["Timor-Leste", "+670"], ["Togo", "+228"], ["Tokelau", "+690"], ["Tonga", "+676"], ["Trinidad and Tobago", "+1"], ["Tunisia", "+216"],
        ["Türkiye", "+90"], ["Turkmenistan", "+993"], ["Turks and Caicos Islands", "+1"], ["Tuvalu", "+688"], ["Uganda", "+256"],
        ["Ukraine", "+380"], ["United Arab Emirates", "+971"], ["United Kingdom", "+44"], ["United States", "+1"], ["United States Virgin Islands", "+1"],
        ["Uruguay", "+598"], ["Uzbekistan", "+998"], ["Vanuatu", "+678"], ["Venezuela", "+58"], ["Vietnam", "+84"], ["Wallis and Futuna", "+681"],
        ["Yemen", "+967"], ["Zambia", "+260"], ["Zimbabwe", "+263"],
    ];
    $phoneValue = (string) old('phone', data_get($info ?? null, 'phone', ''));
    $phoneCountryCode = old('phone_country_code');
    $phoneNumber = old('phone_number');
    if ($phoneNumber === null) {
        $phoneNumber = $phoneValue;
        $countryCodes = array_values(array_unique(array_column($phoneCountries, 1)));
        usort($countryCodes, fn ($left, $right) => strlen($right) <=> strlen($left));
        foreach ($countryCodes as $countryCode) {
            if (\Illuminate\Support\Str::startsWith($phoneValue, $countryCode)) {
                $phoneCountryCode = $phoneCountryCode ?? $countryCode;
                $phoneNumber = trim(substr($phoneValue, strlen($countryCode)));
                break;
            }
        }
        if (!$phoneCountryCode && preg_match('/^(\+[1-9][0-9]{0,2})[\s-]*(.*)$/', $phoneValue, $phoneParts)) {
            $phoneCountryCode = $phoneParts[1];
            $phoneNumber = trim($phoneParts[2]);
        }
    }
    $phoneCountryCode = $phoneCountryCode ?? '+63';
    $phoneNumber = trim((string) ($phoneNumber ?? ''));
    if ($phoneCountryCode === '+63' && str_starts_with($phoneNumber, '0')) {
        $phoneNumber = substr($phoneNumber, 1);
    }
    $phoneCodeLabels = [
        '+1' => 'United States, Canada and territories',
        '+7' => 'Kazakhstan and Russia',
        '+599' => 'Curaçao and Caribbean Netherlands',
    ];
    $phoneCodeAvailable = in_array($phoneCountryCode, array_column($phoneCountries, 1), true);
    if (!$phoneCodeAvailable && preg_match('/^\+[1-9][0-9]{0,2}$/', $phoneCountryCode)) {
        array_unshift($phoneCountries, ['Saved calling code', $phoneCountryCode]);
    }
    $phoneOptions = [];
    $renderedPhoneCodes = [];
    foreach ($phoneCountries as [$countryName, $countryCode]) {
        if (in_array($countryCode, $renderedPhoneCodes, true)) {
            continue;
        }
        $phoneOptions[] = [
            'name' => $phoneCodeLabels[$countryCode] ?? $countryName,
            'code' => $countryCode,
        ];
        $renderedPhoneCodes[] = $countryCode;
    }
    usort($phoneOptions, fn ($left, $right) => strcasecmp($left['name'], $right['name']));
@endphp
<fieldset class="form-group phone-field">
    <legend>Contact number</legend>
    <div class="phone-control">
        <div>
            <label class="sr-only" for="phone_country_code">Country calling code</label>
            <select id="phone_country_code" name="phone_country_code" autocomplete="tel-country-code" aria-describedby="phone-help">
                @foreach($phoneOptions as $phoneOption)
                    <option value="{{ $phoneOption['code'] }}" {{ $phoneCountryCode === $phoneOption['code'] ? 'selected' : '' }}>{{ $phoneOption['name'] }} ({{ $phoneOption['code'] }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="sr-only" for="phone_number">Phone number</label>
            <input type="tel" id="phone_number" name="phone_number" value="{{ $phoneNumber }}" autocomplete="tel-national" inputmode="tel" placeholder="912 345 6789" aria-describedby="phone-help">
        </div>
    </div>
    <span class="form-help" id="phone-help">Enter the number after the country code. For +63, omit the leading 0 (for example, 912 345 6789).</span>
    @error('phone')<span class="field-error" role="alert">{{ $message }}</span>@enderror
    @error('phone_country_code')<span class="field-error" role="alert">{{ $message }}</span>@enderror
    @error('phone_number')<span class="field-error" role="alert">{{ $message }}</span>@enderror
</fieldset>

