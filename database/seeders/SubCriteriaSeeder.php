<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Criteria;    // Pastikan path model benar
use App\Models\SubCriteria; // Pastikan path model benar
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
        $criteriaCollection = Criteria::all()->keyBy('name');

        $subCriteriasData = [
            'Harga' => [ // input_method: direct_value
                ['name' => '< Rp 300.000',         'value' => 4, 'range_min' => null, 'range_max' => 299999.99],
                ['name' => 'Rp 300.000 - Rp 400.000', 'value' => 3, 'range_min' => 300000, 'range_max' => 400000],
                ['name' => 'Rp 400.001 - Rp 500.000', 'value' => 2, 'range_min' => 400000.01, 'range_max' => 500000],
                ['name' => '> Rp 500.000',         'value' => 1, 'range_min' => 500000.01, 'range_max' => null],
            ],
            'Ketersediaan Jaringan' => [ // input_method: select
                ['name' => 'Ada', 'value' => 1], 
                ['name' => 'Tidak Ada', 'value' => 0],
            ],
            'Stabilitas Koneksi' => [ // input_method: select
                ['name' => 'Sangat Stabil (Jarang atau tidak pernah putus)', 'value' => 4],
                ['name' => 'Stabil (Kadang-kadang putus, <1x sehari)', 'value' => 3],
                ['name' => 'Cukup Stabil (Sering putus, 1-3x sehari)', 'value' => 2],
                ['name' => 'Tidak Stabil (Sangat sering putus, >3x sehari)', 'value' => 1],
            ],
            'Kecepatan Upload' => [ // input_method: direct_value
                ['name' => '<= 10 Mbps',    'value' => 1, 'range_min' => null, 'range_max' => 10],
                ['name' => '11 - 30 Mbps',  'value' => 2, 'range_min' => 10.01, 'range_max' => 30],
                ['name' => '31 - 50 Mbps',  'value' => 3, 'range_min' => 30.01, 'range_max' => 50],
                ['name' => '> 50 Mbps',     'value' => 4, 'range_min' => 50.01, 'range_max' => null],
            ],
            'Kecepatan Download' => [ // input_method: direct_value
                ['name' => '<= 40 Mbps',     'value' => 1, 'range_min' => null, 'range_max' => 40],
                ['name' => '41 - 70 Mbps',   'value' => 2, 'range_min' => 40.01, 'range_max' => 70],
                ['name' => '71 - 100 Mbps',  'value' => 3, 'range_min' => 70.01, 'range_max' => 100],
                ['name' => '> 100 Mbps',    'value' => 4, 'range_min' => 100.01, 'range_max' => null],
            ],
        ];

        foreach ($subCriteriasData as $criteriaName => $subCriterias) {
            // Cari objek Criteria berdasarkan nama
            $criterion = $criteriaCollection->get($criteriaName);

            if ($criterion) {
                $kriteriaId = $criterion->kriteria_id; // Ambil UUID kriteria_id
                foreach ($subCriterias as $subCriteria) {
                    SubCriteria::create([
                        'subkriteria_id' => (string) Str::uuid(), // Generate UUID untuk subkriteria
                        'kriteria_id' => $kriteriaId,
                        'name' => $subCriteria['name'],
                        'value' => $subCriteria['value'],
                        // Hanya isi range jika ada di data dan kriteria mendukungnya
                        'range_min' => $subCriteria['range_min'] ?? null,
                        'range_max' => $subCriteria['range_max'] ?? null,
                    ]);
                }
            } else {
                $this->command->warn("Kriteria dengan nama '{$criteriaName}' tidak ditemukan di database. Sub-kriteria untuk ini tidak akan ditambahkan.");
            }
        }
    }
}