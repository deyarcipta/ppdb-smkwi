<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $data = Faq::latest()->paginate(10);
        return view('admin.faq.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'jawaban' => 'required|string',
            'urutan' => 'required|integer|min:1',
        ]);

        Faq::create($request->all());

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'jawaban' => 'required|string',
            'urutan' => 'required|integer|min:1',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update($request->all());

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['status' => true]);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['status' => false]);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil dinonaktifkan.');
    }
}