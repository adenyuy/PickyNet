<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alternative;
use Illuminate\Support\Str; // Untuk UUID

class AlternativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama jika ada

        $alternatives = [
            [
                'alternative_id' => (string) Str::uuid(), // Generate UUID
                'name' => 'IndiHome',
                'image_path' => 'providers/indihome.png', // Path relatif dari public/storage
            ],
            [
                'alternative_id' => (string) Str::uuid(),
                'name' => 'MNC Play',
                'image_path' => 'providers/mncplay.png',
            ],
            [
                'alternative_id' => (string) Str::uuid(),
                'name' => 'Biznet Home',
                'image_path' => 'providers/biznet.png',
            ],
            [
                'alternative_id' => (string) Str::uuid(),
                'name' => 'First Media',
                'image_path' => 'providers/firstmedia.png',
            ],
            [
                'alternative_id' => (string) Str::uuid(),
                'name' => 'MyRepublic',
                'image_path' => 'providers/myrepublic.png',
            ],
            [
                'alternative_id' => (string) Str::uuid(),
                'name' => 'Oxygen.id',
                'image_path' => 'providers/oxygen.png',
            ],
            [
                'alternative_id' => (string) Str::uuid(),
                'name' => 'ICONNET',
                'image_path' => 'providers/iconnet.png',
            ],
        ];

        foreach ($alternatives as $alternative) {
            Alternative::create($alternative);
        }
    }
}