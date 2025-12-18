<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataSmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class DataSmpController extends Controller
{
    public function index()
    {
        $dataSmp = DataSmp::orderBy('nama_smp')->paginate(10);
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
}