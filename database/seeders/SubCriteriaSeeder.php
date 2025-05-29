<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criteria;
use App\Models\SubCriteria;
use Illuminate\Support\Str;

class SubCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Optional: Hapus data lama jika ingin selalu memulai dari awal
        // SubCriteria::truncate();

        $criteriaIds = Criteria::pluck('kriteria_id', 'name');

        $subCriteriasData = [
            'Harga' => [
                ['name' => '< 300.000', 'value' => 4], // Cost: Semakin kecil harga, semakin besar value (baik)
                ['name' => '300.000 - 400.000', 'value' => 3],
                ['name' => '400.000 - 500.000', 'value' => 2],
                ['name' => '> 500.000', 'value' => 1], // Cost: Semakin besar harga, semakin kecil value (buruk)
            ],
            'Ketersediaan Jaringan' => [
                ['name' => 'Ada', 'value' => 1],
                ['name' => 'Tidak ada', 'value' => 0],
            ],
            'Stabilitas Koneksi' => [
                ['name' => 'Sangat Stabil (Jarang Putus)', 'value' => 4], // Benefit
                ['name' => 'Stabil (Kadang Putus)', 'value' => 3],
                ['name' => 'Cukup Stabil (Sering Putus)', 'value' => 2],
                ['name' => 'Tidak Stabil (Sangat Sering Putus)', 'value' => 1],
            ],
            'Kecepatan Upload' => [
                ['name' => '> 50 Mbps', 'value' => 4], // Benefit
                ['name' => '31-50 Mbps', 'value' => 3],
                ['name' => '11-30 Mbps', 'value' => 2],
                ['name' => '<= 10 Mbps', 'value' => 1],
            ],
            'Kecepatan Download' => [
                ['name' => '> 100 Mbps', 'value' => 4], // Benefit
                ['name' => '71-100 Mbps', 'value' => 3],
                ['name' => '41-70 Mbps', 'value' => 2],
                ['name' => '<= 40 Mbps', 'value' => 1],
            ],
        ];

        foreach ($subCriteriasData as $criteriaName => $subCriterias) {
            $kriteriaId = $criteriaIds->get($criteriaName); // Gunakan get() untuk Collection
            if ($kriteriaId) {
                foreach ($subCriterias as $subCriteria) {
                    SubCriteria::create([
                        'subkriteria_id' => (string) Str::uuid(),
                        'kriteria_id' => $kriteriaId,
                        'name' => $subCriteria['name'],
                        'value' => $subCriteria['value'],
                    ]);
                }
            } else {
                $this->command->warn("Kriteria '{$criteriaName}' tidak ditemukan. Subkriteria tidak akan ditambahkan.");
            }
        }
    }
}