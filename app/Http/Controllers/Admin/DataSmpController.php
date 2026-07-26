<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataSmp;
use App\Exports\DataSmpExport;
use App\Exports\TemplateDataSmpExport;
use App\Imports\DataSmpImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class DataSmpController extends Controller
{
    public function index(Request $request)
    {
        $query = DataSmp::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_smp', 'like', "%{$search}%");
        }

        $dataSmp = $query->orderBy('nama_smp')->paginate(10)->withQueryString();
        return view('admin.data-smp.index', compact('dataSmp'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_smp' => 'required|string|max:200|unique:data_smp,nama_smp'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        DataSmp::create(['nama_smp' => $request->nama_smp]);

        return redirect()->back()->with('success', 'Data SMP berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $dataSmp = DataSmp::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_smp' => 'required|string|max:200|unique:data_smp,nama_smp,' . $id . ',id_smp'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first())
                ->withInput();
        }

        $dataSmp->update(['nama_smp' => $request->nama_smp]);

        return redirect()->back()->with('success', 'Data SMP berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $dataSmp = DataSmp::findOrFail($id);
        
        // Cek apakah SMP ini digunakan di data siswa
        if ($dataSmp->dataSiswa()->exists()) {
            return redirect()->back()->with('error', 'Data SMP tidak dapat dihapus karena sudah digunakan oleh data siswa');
        }
        
        $dataSmp->delete();

        return redirect()->back()->with('success', 'Data SMP berhasil dihapus!');
    }

    /**
     * Export Data SMP ke berkas Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $filename = 'Data_SMP_PPDB_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new DataSmpExport($request->search), $filename);
    }

    /**
     * Unduh Berkas Contoh / Template Import Excel Data SMP
     */
    public function downloadTemplate()
    {
        $filename = 'Template_Import_Data_SMP.xlsx';
        return Excel::download(new TemplateDataSmpExport, $filename);
    }

    /**
     * Import Data SMP dari berkas Excel
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Maksimal 10MB
        ], [
            'file.required' => 'Pilih berkas Excel terlebih dahulu.',
            'file.mimes'    => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran berkas maksimal adalah 10MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            $import = new DataSmpImport();
            Excel::import($import, $request->file('file'));

            $msg = "Import Data SMP berhasil! ({$import->importedCount} data baru ditambahkan";
            if ($import->skippedCount > 0) {
                $msg .= ", {$import->skippedCount} data sudah ada diabaikan";
            }
            $msg .= ")";

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses import Excel: ' . $e->getMessage());
        }
    }
}