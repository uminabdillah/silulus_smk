<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;

class SettingController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::latest()->get();
        return view('admin.settings.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255',
            'nomor_skl_template' => 'nullable|string|max:255',
            'tanggal_pleno' => 'required|date',
            'tanggal_kelulusan' => 'required|date',
            'tempat' => 'required|string|max:255',
            'announcement_start' => 'nullable|date',
            'announcement_end' => 'nullable|date',
            'hold_message' => 'nullable|string',
        ]);

        $data = $request->all();

        // If this is the first one, activate it
        if (AcademicYear::count() == 0) {
            $data['is_active'] = true;
        }

        AcademicYear::create($data);

        return back()->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.settings.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:255',
            'nomor_skl_template' => 'nullable|string|max:255',
            'tanggal_pleno' => 'required|date',
            'tanggal_kelulusan' => 'required|date',
            'tempat' => 'required|string|max:255',
            'announcement_start' => 'nullable|date',
            'announcement_end' => 'nullable|date',
            'hold_message' => 'nullable|string',
        ]);

        $data = $request->all();

        $academicYear->update($data);

        return redirect()->route('settings.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tidak bisa menghapus Tahun Ajaran yang sedang Aktif!');
        }

        $academicYear->delete();

        return back()->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with('success', 'Tahun Ajaran aktif berhasil diubah.');
    }
}
