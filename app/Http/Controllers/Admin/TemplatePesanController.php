<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplatePesan;
use Illuminate\Http\Request;

class TemplatePesanController extends Controller
{
    public function index()
    {
        $data = TemplatePesan::latest()->paginate(10);
        $jenisList = TemplatePesan::jenisList();
        return view('admin.template-pesan.index', compact('data', 'jenisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pesan' => 'required|string',
            'judul' => 'required|string|max:255',
            'isi_pesan' => 'required|string',
        ]);

        TemplatePesan::create($validated);
        return back()->with('success', 'Template pesan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_pesan' => 'required|string',
            'judul' => 'required|string|max:255',
            'isi_pesan' => 'required|string',
        ]);

        $template = TemplatePesan::findOrFail($id);
        $template->update($validated);

        return back()->with('success', 'Template pesan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        TemplatePesan::findOrFail($id)->delete();
        return back()->with('success', 'Template pesan berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        TemplatePesan::where('id', $id)->update(['status' => true]);
        return back()->with('success', 'Template pesan diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        TemplatePesan::where('id', $id)->update(['status' => false]);
        return back()->with('success', 'Template pesan dinonaktifkan.');
    }
}
