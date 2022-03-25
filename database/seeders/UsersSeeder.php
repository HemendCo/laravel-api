<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Users::create([
            'status' => '1',
            'first_name' => 'بلال',
            'last_name' => 'آرست',
            'gender' => 'M',
            'mobile' => '09356449579',
        ]);
    }
}
