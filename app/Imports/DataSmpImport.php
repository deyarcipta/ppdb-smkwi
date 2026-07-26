<?php

namespace App\Imports;

use App\Models\DataSmp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataSmpImport implements ToCollection, WithHeadingRow
{
    public $importedCount = 0;
    public $skippedCount = 0;

    public function headingRow(): int
    {
        return 4; // Header berada di baris 4 pada template Excel
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $namaSmp = null;

            if (isset($row['nama_smp'])) {
                $namaSmp = trim((string) $row['nama_smp']);
            } elseif (isset($row['nama_smp_mts'])) {
                $namaSmp = trim((string) $row['nama_smp_mts']);
            } elseif (isset($row[1])) {
                $namaSmp = trim((string) $row[1]);
            }

            if (empty($namaSmp) || strtolower($namaSmp) === 'nama smp') {
                continue;
            }

            // Cek apakah data SMP sudah ada di database
            $exists = DataSmp::where('nama_smp', $namaSmp)->exists();
            if (!$exists) {
                DataSmp::create(['nama_smp' => $namaSmp]);
                $this->importedCount++;
            } else {
                $this->skippedCount++;
            }
        }
    }
}
