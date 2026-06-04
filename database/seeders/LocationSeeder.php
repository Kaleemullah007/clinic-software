<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        // ── Truncate (safe order: cities → states → countries) ──
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('cities')->truncate();
        DB::table('states')->truncate();
        DB::table('countries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ══════════════════════════════════════════════════════════
        //  COUNTRIES
        // ══════════════════════════════════════════════════════════
        $countries = [
            ['name' => 'Pakistan',      'code' => 'PK', 'phone_code' => '+92',  'currency' => 'PKR'],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'phone_code' => '+971', 'currency' => 'AED'],
            ['name' => 'Saudi Arabia',  'code' => 'SA', 'phone_code' => '+966', 'currency' => 'SAR'],
            ['name' => 'United Kingdom','code' => 'GB', 'phone_code' => '+44',  'currency' => 'GBP'],
            ['name' => 'United States', 'code' => 'US', 'phone_code' => '+1',   'currency' => 'USD'],
            ['name' => 'Canada',        'code' => 'CA', 'phone_code' => '+1',   'currency' => 'CAD'],
            ['name' => 'Australia',     'code' => 'AU', 'phone_code' => '+61',  'currency' => 'AUD'],
            ['name' => 'Germany',       'code' => 'DE', 'phone_code' => '+49',  'currency' => 'EUR'],
            ['name' => 'Turkey',        'code' => 'TR', 'phone_code' => '+90',  'currency' => 'TRY'],
            ['name' => 'Qatar',         'code' => 'QA', 'phone_code' => '+974', 'currency' => 'QAR'],
            ['name' => 'Kuwait',        'code' => 'KW', 'phone_code' => '+965', 'currency' => 'KWD'],
            ['name' => 'Bahrain',       'code' => 'BH', 'phone_code' => '+973', 'currency' => 'BHD'],
            ['name' => 'Oman',          'code' => 'OM', 'phone_code' => '+968', 'currency' => 'OMR'],
        ];

        $now = now();
        foreach ($countries as &$c) {
            $c['is_active']   = true;
            $c['created_at']  = $now;
            $c['updated_at']  = $now;
        }
        DB::table('countries')->insert($countries);

        $pkId  = DB::table('countries')->where('code', 'PK')->value('id');
        $aeId  = DB::table('countries')->where('code', 'AE')->value('id');
        $saId  = DB::table('countries')->where('code', 'SA')->value('id');
        $gbId  = DB::table('countries')->where('code', 'GB')->value('id');
        $usId  = DB::table('countries')->where('code', 'US')->value('id');

        // ══════════════════════════════════════════════════════════
        //  PAKISTAN — PROVINCES / TERRITORIES
        // ══════════════════════════════════════════════════════════
        $pkStates = [
            ['code' => 'PB',  'name' => 'Punjab'],
            ['code' => 'SD',  'name' => 'Sindh'],
            ['code' => 'KPK', 'name' => 'Khyber Pakhtunkhwa'],
            ['code' => 'BL',  'name' => 'Balochistan'],
            ['code' => 'ICT', 'name' => 'Islamabad Capital Territory'],
            ['code' => 'AJK', 'name' => 'Azad Jammu & Kashmir'],
            ['code' => 'GB',  'name' => 'Gilgit-Baltistan'],
        ];

        foreach ($pkStates as &$s) {
            $s['country_id'] = $pkId;
            $s['is_active']  = true;
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
        }
        DB::table('states')->insert($pkStates);

        // Fetch state IDs
        $stateIds = DB::table('states')->where('country_id', $pkId)->pluck('id', 'code');

        // ── CITIES BY PROVINCE ──────────────────────────────────
        $pkCities = [
            // Punjab
            'PB'  => ['Lahore','Faisalabad','Rawalpindi','Gujranwala','Multan','Sialkot',
                      'Bahawalpur','Sargodha','Sheikhupura','Jhang','Rahim Yar Khan','Gujrat',
                      'Kasur','Sahiwal','Okara','Wah Cantonment','Dera Ghazi Khan','Mirpur Khas',
                      'Chiniot','Khanewal'],
            // Sindh
            'SD'  => ['Karachi','Hyderabad','Sukkur','Larkana','Nawabshah','Mirpurkhas',
                      'Jacobabad','Shikarpur','Khairpur','Dadu','Thatta','Badin',
                      'Sanghar','Tando Adam','Kotri'],
            // KPK
            'KPK' => ['Peshawar','Abbottabad','Mardan','Mingora','Kohat','Bannu',
                      'Dera Ismail Khan','Nowshera','Mansehra','Charsadda','Swabi',
                      'Haripur','Karak','Hangu'],
            // Balochistan
            'BL'  => ['Quetta','Gwadar','Turbat','Khuzdar','Hub','Chaman','Loralai',
                      'Zhob','Sibi','Dera Bugti'],
            // ICT
            'ICT' => ['Islamabad'],
            // AJK
            'AJK' => ['Muzaffarabad','Mirpur','Rawalakot','Bagh','Kotli','Bhimber'],
            // GB
            'GB'  => ['Gilgit','Skardu','Hunza','Ghanche','Chilas'],
        ];

        $cityRows = [];
        foreach ($pkCities as $stateCode => $cities) {
            $sid = $stateIds[$stateCode] ?? null;
            if (!$sid) continue;
            foreach ($cities as $cityName) {
                $cityRows[] = [
                    'state_id'   => $sid,
                    'name'       => $cityName,
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('cities')->insert($cityRows);

        // ══════════════════════════════════════════════════════════
        //  UAE — EMIRATES
        // ══════════════════════════════════════════════════════════
        $aeStates = ['Abu Dhabi','Dubai','Sharjah','Ajman','Umm Al Quwain','Ras Al Khaimah','Fujairah'];
        $aeStateRows = [];
        foreach ($aeStates as $s) {
            $aeStateRows[] = ['country_id' => $aeId,'name' => $s,'code' => null,'is_active' => true,'created_at' => $now,'updated_at' => $now];
        }
        DB::table('states')->insert($aeStateRows);

        $aeCityMap = [
            'Abu Dhabi' => ['Abu Dhabi','Al Ain','Ruwais'],
            'Dubai'     => ['Dubai'],
            'Sharjah'   => ['Sharjah','Khor Fakkan'],
            'Ajman'     => ['Ajman'],
            'Umm Al Quwain' => ['Umm Al Quwain'],
            'Ras Al Khaimah' => ['Ras Al Khaimah'],
            'Fujairah'  => ['Fujairah'],
        ];
        $aeSids = DB::table('states')->where('country_id', $aeId)->pluck('id', 'name');
        $aeCityRows = [];
        foreach ($aeCityMap as $stateName => $cities) {
            $sid = $aeSids[$stateName] ?? null;
            if (!$sid) continue;
            foreach ($cities as $c) {
                $aeCityRows[] = ['state_id' => $sid,'name' => $c,'is_active' => true,'created_at' => $now,'updated_at' => $now];
            }
        }
        DB::table('cities')->insert($aeCityRows);

        // ══════════════════════════════════════════════════════════
        //  SAUDI ARABIA — REGIONS
        // ══════════════════════════════════════════════════════════
        $saStates = ['Riyadh','Makkah','Al Madinah','Eastern Province','Asir','Tabuk',
                     'Ha\'il','Northern Borders','Jazan','Najran','Al Bahah','Al Jawf','Al Qassim'];
        $saStateRows = [];
        foreach ($saStates as $s) {
            $saStateRows[] = ['country_id' => $saId,'name' => $s,'code' => null,'is_active' => true,'created_at' => $now,'updated_at' => $now];
        }
        DB::table('states')->insert($saStateRows);

        $saSids = DB::table('states')->where('country_id', $saId)->pluck('id', 'name');
        $saCityMap = [
            'Riyadh'          => ['Riyadh','Al Kharj','Dawadmi'],
            'Makkah'          => ['Jeddah','Makkah','Taif'],
            'Al Madinah'      => ['Madinah','Yanbu'],
            'Eastern Province'=> ['Dammam','Al Khobar','Dhahran','Al Jubail'],
        ];
        $saCityRows = [];
        foreach ($saCityMap as $stateName => $cities) {
            $sid = $saSids[$stateName] ?? null;
            if (!$sid) continue;
            foreach ($cities as $c) {
                $saCityRows[] = ['state_id' => $sid,'name' => $c,'is_active' => true,'created_at' => $now,'updated_at' => $now];
            }
        }
        if ($saCityRows) DB::table('cities')->insert($saCityRows);

        // ══════════════════════════════════════════════════════════
        //  UK — REGIONS
        // ══════════════════════════════════════════════════════════
        $gbStates = ['England','Scotland','Wales','Northern Ireland'];
        $gbStateRows = [];
        foreach ($gbStates as $s) {
            $gbStateRows[] = ['country_id' => $gbId,'name' => $s,'code' => null,'is_active' => true,'created_at' => $now,'updated_at' => $now];
        }
        DB::table('states')->insert($gbStateRows);

        $gbSids = DB::table('states')->where('country_id', $gbId)->pluck('id', 'name');
        $gbCityMap = [
            'England'          => ['London','Birmingham','Manchester','Leeds','Liverpool','Sheffield','Bristol','Coventry','Leicester','Nottingham'],
            'Scotland'         => ['Edinburgh','Glasgow','Aberdeen','Dundee'],
            'Wales'            => ['Cardiff','Swansea','Newport'],
            'Northern Ireland' => ['Belfast','Derry'],
        ];
        $gbCityRows = [];
        foreach ($gbCityMap as $stateName => $cities) {
            $sid = $gbSids[$stateName] ?? null;
            if (!$sid) continue;
            foreach ($cities as $c) {
                $gbCityRows[] = ['state_id' => $sid,'name' => $c,'is_active' => true,'created_at' => $now,'updated_at' => $now];
            }
        }
        DB::table('cities')->insert($gbCityRows);

        // ══════════════════════════════════════════════════════════
        //  USA — KEY STATES
        // ══════════════════════════════════════════════════════════
        $usStates = [
            ['code' => 'CA','name' => 'California'],
            ['code' => 'TX','name' => 'Texas'],
            ['code' => 'NY','name' => 'New York'],
            ['code' => 'FL','name' => 'Florida'],
            ['code' => 'IL','name' => 'Illinois'],
            ['code' => 'PA','name' => 'Pennsylvania'],
            ['code' => 'OH','name' => 'Ohio'],
            ['code' => 'GA','name' => 'Georgia'],
            ['code' => 'NC','name' => 'North Carolina'],
            ['code' => 'MI','name' => 'Michigan'],
        ];
        $usStateRows = [];
        foreach ($usStates as $s) {
            $usStateRows[] = ['country_id' => $usId,'name' => $s['name'],'code' => $s['code'],'is_active' => true,'created_at' => $now,'updated_at' => $now];
        }
        DB::table('states')->insert($usStateRows);

        $usSids = DB::table('states')->where('country_id', $usId)->pluck('id', 'name');
        $usCityMap = [
            'California' => ['Los Angeles','San Francisco','San Diego','San Jose'],
            'Texas'      => ['Houston','Dallas','San Antonio','Austin'],
            'New York'   => ['New York City','Buffalo','Rochester'],
            'Florida'    => ['Miami','Orlando','Tampa','Jacksonville'],
            'Illinois'   => ['Chicago','Aurora','Naperville'],
        ];
        $usCityRows = [];
        foreach ($usCityMap as $stateName => $cities) {
            $sid = $usSids[$stateName] ?? null;
            if (!$sid) continue;
            foreach ($cities as $c) {
                $usCityRows[] = ['state_id' => $sid,'name' => $c,'is_active' => true,'created_at' => $now,'updated_at' => $now];
            }
        }
        DB::table('cities')->insert($usCityRows);

        $this->command->info('✅ Location seeder completed — countries, states, cities inserted.');
    }
}
