<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FirstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mesins')->insert([
            'nama' => "Wemos 1",
            'is_online' => false,
            'is_active' => false,
            'is_on' => false,
            'batas_on' => 10
        ]);
    }
}
