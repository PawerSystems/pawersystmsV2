<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('businesses')->insert([
            'business_name' => 'admin',
            'brand_name' => 'Pawer Systems 2',
            'time_interval' => 30,
            'business_email' => 'paw.nielsen@pawersystems.dk',
            'languages' => '-1',
            'access_modules' => '-1',
            'is_active' => 1,
        ]);
    }
}
