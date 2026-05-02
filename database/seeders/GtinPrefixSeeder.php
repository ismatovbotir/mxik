<?php

namespace Database\Seeders;

use App\Models\GtinPrefix;
use Illuminate\Database\Seeder;

class GtinPrefixSeeder extends Seeder
{
    public function run(): void
    {
        $flag = fn(string $code): string => implode('', array_map(
            fn($c) => mb_chr(0x1F1E6 + \ord($c) - \ord('A')),
            str_split(strtoupper($code))
        ));

        $prefixes = [
            // USA & Canada
            ['prefix' => '00', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '01', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '03', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '04', 'country' => 'USA & Canada (in-store)', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '06', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '07', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '08', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            ['prefix' => '09', 'country' => 'USA & Canada', 'country_code' => 'US', 'flag' => $flag('US')],
            // France
            ['prefix' => '30', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '31', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '32', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '33', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '34', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '35', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '36', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            ['prefix' => '37', 'country' => 'France', 'country_code' => 'FR', 'flag' => $flag('FR')],
            // Germany
            ['prefix' => '40', 'country' => 'Germany', 'country_code' => 'DE', 'flag' => $flag('DE')],
            ['prefix' => '41', 'country' => 'Germany', 'country_code' => 'DE', 'flag' => $flag('DE')],
            ['prefix' => '42', 'country' => 'Germany', 'country_code' => 'DE', 'flag' => $flag('DE')],
            ['prefix' => '43', 'country' => 'Germany', 'country_code' => 'DE', 'flag' => $flag('DE')],
            ['prefix' => '44', 'country' => 'Germany', 'country_code' => 'DE', 'flag' => $flag('DE')],
            // Japan
            ['prefix' => '45', 'country' => 'Japan', 'country_code' => 'JP', 'flag' => $flag('JP')],
            ['prefix' => '49', 'country' => 'Japan', 'country_code' => 'JP', 'flag' => $flag('JP')],
            // Russia
            ['prefix' => '46', 'country' => 'Russia', 'country_code' => 'RU', 'flag' => $flag('RU')],
            // Kyrgyzstan
            ['prefix' => '470', 'country' => 'Kyrgyzstan', 'country_code' => 'KG', 'flag' => $flag('KG')],
            // Taiwan
            ['prefix' => '471', 'country' => 'Taiwan', 'country_code' => 'TW', 'flag' => $flag('TW')],
            // Estonia
            ['prefix' => '474', 'country' => 'Estonia', 'country_code' => 'EE', 'flag' => $flag('EE')],
            // Latvia
            ['prefix' => '475', 'country' => 'Latvia', 'country_code' => 'LV', 'flag' => $flag('LV')],
            // Azerbaijan
            ['prefix' => '476', 'country' => 'Azerbaijan', 'country_code' => 'AZ', 'flag' => $flag('AZ')],
            // Lithuania
            ['prefix' => '477', 'country' => 'Lithuania', 'country_code' => 'LT', 'flag' => $flag('LT')],
            // Uzbekistan
            ['prefix' => '478', 'country' => 'Uzbekistan', 'country_code' => 'UZ', 'flag' => $flag('UZ')],
            // Sri Lanka
            ['prefix' => '479', 'country' => 'Sri Lanka', 'country_code' => 'LK', 'flag' => $flag('LK')],
            // Philippines
            ['prefix' => '480', 'country' => 'Philippines', 'country_code' => 'PH', 'flag' => $flag('PH')],
            // Belarus
            ['prefix' => '481', 'country' => 'Belarus', 'country_code' => 'BY', 'flag' => $flag('BY')],
            // Ukraine
            ['prefix' => '482', 'country' => 'Ukraine', 'country_code' => 'UA', 'flag' => $flag('UA')],
            // Turkmenistan
            ['prefix' => '483', 'country' => 'Turkmenistan', 'country_code' => 'TM', 'flag' => $flag('TM')],
            // Moldova
            ['prefix' => '484', 'country' => 'Moldova', 'country_code' => 'MD', 'flag' => $flag('MD')],
            // Armenia
            ['prefix' => '485', 'country' => 'Armenia', 'country_code' => 'AM', 'flag' => $flag('AM')],
            // Georgia
            ['prefix' => '486', 'country' => 'Georgia', 'country_code' => 'GE', 'flag' => $flag('GE')],
            // Kazakhstan
            ['prefix' => '487', 'country' => 'Kazakhstan', 'country_code' => 'KZ', 'flag' => $flag('KZ')],
            // Tajikistan
            ['prefix' => '488', 'country' => 'Tajikistan', 'country_code' => 'TJ', 'flag' => $flag('TJ')],
            // Hong Kong
            ['prefix' => '489', 'country' => 'Hong Kong', 'country_code' => 'HK', 'flag' => $flag('HK')],
            // UK
            ['prefix' => '50', 'country' => 'United Kingdom', 'country_code' => 'GB', 'flag' => $flag('GB')],
            // Greece
            ['prefix' => '520', 'country' => 'Greece', 'country_code' => 'GR', 'flag' => $flag('GR')],
            ['prefix' => '521', 'country' => 'Greece', 'country_code' => 'GR', 'flag' => $flag('GR')],
            // Lebanon
            ['prefix' => '528', 'country' => 'Lebanon', 'country_code' => 'LB', 'flag' => $flag('LB')],
            // Cyprus
            ['prefix' => '529', 'country' => 'Cyprus', 'country_code' => 'CY', 'flag' => $flag('CY')],
            // Albania
            ['prefix' => '530', 'country' => 'Albania', 'country_code' => 'AL', 'flag' => $flag('AL')],
            // North Macedonia
            ['prefix' => '531', 'country' => 'North Macedonia', 'country_code' => 'MK', 'flag' => $flag('MK')],
            // Malta
            ['prefix' => '535', 'country' => 'Malta', 'country_code' => 'MT', 'flag' => $flag('MT')],
            // Ireland
            ['prefix' => '539', 'country' => 'Ireland', 'country_code' => 'IE', 'flag' => $flag('IE')],
            // Belgium & Luxembourg
            ['prefix' => '54', 'country' => 'Belgium & Luxembourg', 'country_code' => 'BE', 'flag' => $flag('BE')],
            // Portugal
            ['prefix' => '560', 'country' => 'Portugal', 'country_code' => 'PT', 'flag' => $flag('PT')],
            // Iceland
            ['prefix' => '569', 'country' => 'Iceland', 'country_code' => 'IS', 'flag' => $flag('IS')],
            // Denmark
            ['prefix' => '57', 'country' => 'Denmark', 'country_code' => 'DK', 'flag' => $flag('DK')],
            // Poland
            ['prefix' => '590', 'country' => 'Poland', 'country_code' => 'PL', 'flag' => $flag('PL')],
            // Romania
            ['prefix' => '594', 'country' => 'Romania', 'country_code' => 'RO', 'flag' => $flag('RO')],
            // Hungary
            ['prefix' => '599', 'country' => 'Hungary', 'country_code' => 'HU', 'flag' => $flag('HU')],
            // South Africa
            ['prefix' => '600', 'country' => 'South Africa', 'country_code' => 'ZA', 'flag' => $flag('ZA')],
            ['prefix' => '601', 'country' => 'South Africa', 'country_code' => 'ZA', 'flag' => $flag('ZA')],
            // Ghana
            ['prefix' => '603', 'country' => 'Ghana', 'country_code' => 'GH', 'flag' => $flag('GH')],
            // Senegal
            ['prefix' => '604', 'country' => 'Senegal', 'country_code' => 'SN', 'flag' => $flag('SN')],
            // Bahrain
            ['prefix' => '608', 'country' => 'Bahrain', 'country_code' => 'BH', 'flag' => $flag('BH')],
            // Mauritius
            ['prefix' => '609', 'country' => 'Mauritius', 'country_code' => 'MU', 'flag' => $flag('MU')],
            // Morocco
            ['prefix' => '611', 'country' => 'Morocco', 'country_code' => 'MA', 'flag' => $flag('MA')],
            // Algeria
            ['prefix' => '613', 'country' => 'Algeria', 'country_code' => 'DZ', 'flag' => $flag('DZ')],
            // Nigeria
            ['prefix' => '615', 'country' => 'Nigeria', 'country_code' => 'NG', 'flag' => $flag('NG')],
            // Kenya
            ['prefix' => '616', 'country' => 'Kenya', 'country_code' => 'KE', 'flag' => $flag('KE')],
            // Cameroon
            ['prefix' => '617', 'country' => 'Cameroon', 'country_code' => 'CM', 'flag' => $flag('CM')],
            // Ivory Coast
            ['prefix' => '618', 'country' => 'Ivory Coast', 'country_code' => 'CI', 'flag' => $flag('CI')],
            // Tunisia
            ['prefix' => '619', 'country' => 'Tunisia', 'country_code' => 'TN', 'flag' => $flag('TN')],
            // Syria
            ['prefix' => '621', 'country' => 'Syria', 'country_code' => 'SY', 'flag' => $flag('SY')],
            // Egypt
            ['prefix' => '622', 'country' => 'Egypt', 'country_code' => 'EG', 'flag' => $flag('EG')],
            // Brunei
            ['prefix' => '623', 'country' => 'Brunei', 'country_code' => 'BN', 'flag' => $flag('BN')],
            // Libya
            ['prefix' => '624', 'country' => 'Libya', 'country_code' => 'LY', 'flag' => $flag('LY')],
            // Jordan
            ['prefix' => '625', 'country' => 'Jordan', 'country_code' => 'JO', 'flag' => $flag('JO')],
            // Iran
            ['prefix' => '626', 'country' => 'Iran', 'country_code' => 'IR', 'flag' => $flag('IR')],
            // Kuwait
            ['prefix' => '627', 'country' => 'Kuwait', 'country_code' => 'KW', 'flag' => $flag('KW')],
            // Saudi Arabia
            ['prefix' => '628', 'country' => 'Saudi Arabia', 'country_code' => 'SA', 'flag' => $flag('SA')],
            // UAE
            ['prefix' => '629', 'country' => 'United Arab Emirates', 'country_code' => 'AE', 'flag' => $flag('AE')],
            // Finland
            ['prefix' => '640', 'country' => 'Finland', 'country_code' => 'FI', 'flag' => $flag('FI')],
            ['prefix' => '641', 'country' => 'Finland', 'country_code' => 'FI', 'flag' => $flag('FI')],
            ['prefix' => '642', 'country' => 'Finland', 'country_code' => 'FI', 'flag' => $flag('FI')],
            ['prefix' => '643', 'country' => 'Finland', 'country_code' => 'FI', 'flag' => $flag('FI')],
            ['prefix' => '649', 'country' => 'Finland', 'country_code' => 'FI', 'flag' => $flag('FI')],
            // China
            ['prefix' => '690', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '691', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '692', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '693', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '694', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '695', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '696', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '697', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '698', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            ['prefix' => '699', 'country' => 'China', 'country_code' => 'CN', 'flag' => $flag('CN')],
            // Norway
            ['prefix' => '70', 'country' => 'Norway', 'country_code' => 'NO', 'flag' => $flag('NO')],
            // Israel
            ['prefix' => '729', 'country' => 'Israel', 'country_code' => 'IL', 'flag' => $flag('IL')],
            // Sweden
            ['prefix' => '73', 'country' => 'Sweden', 'country_code' => 'SE', 'flag' => $flag('SE')],
            // Guatemala
            ['prefix' => '740', 'country' => 'Guatemala', 'country_code' => 'GT', 'flag' => $flag('GT')],
            // El Salvador
            ['prefix' => '741', 'country' => 'El Salvador', 'country_code' => 'SV', 'flag' => $flag('SV')],
            // Honduras
            ['prefix' => '742', 'country' => 'Honduras', 'country_code' => 'HN', 'flag' => $flag('HN')],
            // Nicaragua
            ['prefix' => '743', 'country' => 'Nicaragua', 'country_code' => 'NI', 'flag' => $flag('NI')],
            // Costa Rica
            ['prefix' => '744', 'country' => 'Costa Rica', 'country_code' => 'CR', 'flag' => $flag('CR')],
            // Panama
            ['prefix' => '745', 'country' => 'Panama', 'country_code' => 'PA', 'flag' => $flag('PA')],
            // Dominican Republic
            ['prefix' => '746', 'country' => 'Dominican Republic', 'country_code' => 'DO', 'flag' => $flag('DO')],
            // Mexico
            ['prefix' => '750', 'country' => 'Mexico', 'country_code' => 'MX', 'flag' => $flag('MX')],
            // Canada
            ['prefix' => '754', 'country' => 'Canada', 'country_code' => 'CA', 'flag' => $flag('CA')],
            ['prefix' => '755', 'country' => 'Canada', 'country_code' => 'CA', 'flag' => $flag('CA')],
            // Venezuela
            ['prefix' => '759', 'country' => 'Venezuela', 'country_code' => 'VE', 'flag' => $flag('VE')],
            // Switzerland
            ['prefix' => '76', 'country' => 'Switzerland', 'country_code' => 'CH', 'flag' => $flag('CH')],
            // Colombia
            ['prefix' => '770', 'country' => 'Colombia', 'country_code' => 'CO', 'flag' => $flag('CO')],
            ['prefix' => '771', 'country' => 'Colombia', 'country_code' => 'CO', 'flag' => $flag('CO')],
            // Uruguay
            ['prefix' => '773', 'country' => 'Uruguay', 'country_code' => 'UY', 'flag' => $flag('UY')],
            // Peru
            ['prefix' => '775', 'country' => 'Peru', 'country_code' => 'PE', 'flag' => $flag('PE')],
            // Bolivia
            ['prefix' => '777', 'country' => 'Bolivia', 'country_code' => 'BO', 'flag' => $flag('BO')],
            // Argentina
            ['prefix' => '779', 'country' => 'Argentina', 'country_code' => 'AR', 'flag' => $flag('AR')],
            // Chile
            ['prefix' => '780', 'country' => 'Chile', 'country_code' => 'CL', 'flag' => $flag('CL')],
            // Paraguay
            ['prefix' => '784', 'country' => 'Paraguay', 'country_code' => 'PY', 'flag' => $flag('PY')],
            // Ecuador
            ['prefix' => '786', 'country' => 'Ecuador', 'country_code' => 'EC', 'flag' => $flag('EC')],
            // Brazil
            ['prefix' => '789', 'country' => 'Brazil', 'country_code' => 'BR', 'flag' => $flag('BR')],
            ['prefix' => '790', 'country' => 'Brazil', 'country_code' => 'BR', 'flag' => $flag('BR')],
            // Italy
            ['prefix' => '80', 'country' => 'Italy', 'country_code' => 'IT', 'flag' => $flag('IT')],
            ['prefix' => '81', 'country' => 'Italy', 'country_code' => 'IT', 'flag' => $flag('IT')],
            ['prefix' => '82', 'country' => 'Italy', 'country_code' => 'IT', 'flag' => $flag('IT')],
            ['prefix' => '83', 'country' => 'Italy', 'country_code' => 'IT', 'flag' => $flag('IT')],
            // Spain
            ['prefix' => '84', 'country' => 'Spain', 'country_code' => 'ES', 'flag' => $flag('ES')],
            // Cuba
            ['prefix' => '850', 'country' => 'Cuba', 'country_code' => 'CU', 'flag' => $flag('CU')],
            // Slovakia
            ['prefix' => '858', 'country' => 'Slovakia', 'country_code' => 'SK', 'flag' => $flag('SK')],
            // Czech Republic
            ['prefix' => '859', 'country' => 'Czech Republic', 'country_code' => 'CZ', 'flag' => $flag('CZ')],
            // Serbia
            ['prefix' => '860', 'country' => 'Serbia', 'country_code' => 'RS', 'flag' => $flag('RS')],
            // Mongolia
            ['prefix' => '865', 'country' => 'Mongolia', 'country_code' => 'MN', 'flag' => $flag('MN')],
            // North Korea
            ['prefix' => '867', 'country' => 'North Korea', 'country_code' => 'KP', 'flag' => $flag('KP')],
            // Turkey
            ['prefix' => '868', 'country' => 'Turkey', 'country_code' => 'TR', 'flag' => $flag('TR')],
            ['prefix' => '869', 'country' => 'Turkey', 'country_code' => 'TR', 'flag' => $flag('TR')],
            // Netherlands
            ['prefix' => '87', 'country' => 'Netherlands', 'country_code' => 'NL', 'flag' => $flag('NL')],
            // South Korea
            ['prefix' => '880', 'country' => 'South Korea', 'country_code' => 'KR', 'flag' => $flag('KR')],
            // Cambodia
            ['prefix' => '884', 'country' => 'Cambodia', 'country_code' => 'KH', 'flag' => $flag('KH')],
            // Thailand
            ['prefix' => '885', 'country' => 'Thailand', 'country_code' => 'TH', 'flag' => $flag('TH')],
            // Singapore
            ['prefix' => '888', 'country' => 'Singapore', 'country_code' => 'SG', 'flag' => $flag('SG')],
            // India
            ['prefix' => '890', 'country' => 'India', 'country_code' => 'IN', 'flag' => $flag('IN')],
            // Vietnam
            ['prefix' => '893', 'country' => 'Vietnam', 'country_code' => 'VN', 'flag' => $flag('VN')],
            // Pakistan
            ['prefix' => '896', 'country' => 'Pakistan', 'country_code' => 'PK', 'flag' => $flag('PK')],
            // Indonesia
            ['prefix' => '899', 'country' => 'Indonesia', 'country_code' => 'ID', 'flag' => $flag('ID')],
            // Austria
            ['prefix' => '90', 'country' => 'Austria', 'country_code' => 'AT', 'flag' => $flag('AT')],
            ['prefix' => '91', 'country' => 'Austria', 'country_code' => 'AT', 'flag' => $flag('AT')],
            // Australia
            ['prefix' => '93', 'country' => 'Australia', 'country_code' => 'AU', 'flag' => $flag('AU')],
            // New Zealand
            ['prefix' => '94', 'country' => 'New Zealand', 'country_code' => 'NZ', 'flag' => $flag('NZ')],
            // Malaysia
            ['prefix' => '955', 'country' => 'Malaysia', 'country_code' => 'MY', 'flag' => $flag('MY')],
            // Macau
            ['prefix' => '958', 'country' => 'Macau', 'country_code' => 'MO', 'flag' => $flag('MO')],
        ];

        GtinPrefix::upsert($prefixes, ['prefix'], ['country', 'country_code', 'flag']);
    }
}
