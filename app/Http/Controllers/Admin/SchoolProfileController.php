<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolProfile;

class SchoolProfileController extends Controller
{
    public function index()
    {
        $profile = SchoolProfile::first();
        if (!$profile) {
            $profile = SchoolProfile::create([
                'nama_sekolah' => 'SMK MAARIF NU 01 JATIBARANG',
                'npsn' => '12345678',
                'alamat' => '',
                'kepala_sekolah' => 'Nama Kepala Sekolah',
                'nip_kepala' => '-'
            ]);
        }
        return view('admin.school_profile.index', compact('profile'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kepala_sekolah' => 'required|string|max:255',
            'nip_kepala' => 'nullable|string|max:255',
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $profile = SchoolProfile::first();
        $data = $request->except('kop_surat');

        if ($request->hasFile('kop_surat')) {
            // Delete old file if exists
            if ($profile->kop_surat && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->kop_surat)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->kop_surat);
            }
            $path = $request->file('kop_surat')->store('profiles', 'public');
            $data['kop_surat'] = $path;
        }

        $profile->update($data);

        return back()->with('success', 'Identitas Sekolah berhasil diperbarui.');
    }
}
