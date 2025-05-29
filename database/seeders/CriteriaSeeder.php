<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criteria;
use Illuminate\Support\Str; // Untuk UUID

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama jika ada

        $criterias = [
            [
                'kriteria_id' => (string) Str::uuid(), // Generate UUID
                'name' => 'Harga',
                'type' => 'cost', // Atau 'benefit', tergantung SPK Anda
                'weight' => 0, // Akan diatur nanti oleh user, atau bisa diisi default
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Ketersediaan Jaringan',
                'type' => 'benefit',
                'weight' => 0,
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Stabilitas Koneksi',
                'type' => 'benefit',
                'weight' => 0,
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Kecepatan Upload',
                'type' => 'benefit',
                'weight' => 0,
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Kecepatan Download',
                'type' => 'benefit',
                'weight' => 0,
            ],
        ];

        foreach ($criterias as $criteria) {
            Criteria::create($criteria);
        }
    }
}