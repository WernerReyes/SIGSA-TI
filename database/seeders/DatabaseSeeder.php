<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $isDev = config('app.env') === 'local';
        if ($isDev) {
            $this->call([
                AssetTypeSeeder::class,
                InfrastructureEventSeeder::class,
                      AssetSeeder::class,
                SlaPolicySeeder::class,
            ]);

        } else {
            $this->call([
                AssetTypeSeeder::class,
                AssetSeeder::class,
                SlaPolicySeeder::class,
            ]);

        }
    }


}