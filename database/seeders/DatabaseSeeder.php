<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $files_arr = scandir( dirname(__FILE__) ); //store filenames into $files_array
        foreach ($files_arr as $key => $file) {
            if ($file !== 'DatabaseSeeder.php' && $file[0] !== "." ) {
                $this->call( __NAMESPACE__ . '\\' . (explode('.', $file)[0]) );
            }
        }
    }
}
