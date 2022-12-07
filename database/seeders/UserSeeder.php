<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Paw Nielsen',
            'business_id' => '1',
            'email' => 'paw.nielsen@pawersystems.dk',
            'password' => Hash::make('paw.nielsen@pawersystems.dk'),
            'business_id' => '1',
            'role' => 'Owner',
            'language' => 'en',
            'access' => '-1',
            'country_id' => '1',
            'gender' => 'man',
            'birth_year' => '1984',
            'number' => '11111111',
            'is_active' => '1',            
        ]);
    }
}
