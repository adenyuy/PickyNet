<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criteria; // Pastikan path model benar
use Illuminate\Support\Str;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Opsional: Hapus data lama jika ingin seeder ini selalu menghasilkan set data yang sama
        // Criteria::truncate(); // Berhati-hati dengan ini jika ada foreign key constraint

        $criterias = [
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Harga',
                'type' => 'cost',
                'input_method' => 'direct_value', // User input angka langsung
                'weight' => 0, // Diisi nanti berdasarkan ranking user per sesi
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Ketersediaan Jaringan',
                'type' => 'benefit',
                'input_method' => 'select', // User pilih dari opsi sub-kriteria
                'weight' => 0,
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Stabilitas Koneksi',
                'type' => 'benefit',
                'input_method' => 'select',
                'weight' => 0,
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Kecepatan Upload',
                'type' => 'benefit',
                'input_method' => 'direct_value',
                'weight' => 0,
            ],
            [
                'kriteria_id' => (string) Str::uuid(),
                'name' => 'Kecepatan Download',
                'type' => 'benefit',
                'input_method' => 'direct_value',
                'weight' => 0,
            ],
        ];

        foreach ($criterias as $criteriaData) {
            Criteria::create($criteriaData);
        }
    }
}