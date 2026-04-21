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
                'alamat' => 'Jl. Kebon Jeruk No. 1',
                'kabupaten' => 'Brebes',
                'provinsi' => 'Jawa Tengah',
                'kepala_sekolah' => 'Nama Kepala Sekolah',
                'nip_kepala' => '-',
                'jabatan_penandatangan' => 'Kepala Sekolah'
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
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kepala_sekolah' => 'required|string|max:255',
            'nip_kepala' => 'nullable|string|max:255',
            'jabatan_penandatangan' => 'required|string|max:255',
            'logo_path' => 'nullable|file|image|mimes:jpeg,png,jpg,svg|max:5120',
            'kop_surat' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $profile = SchoolProfile::first();
        if (!$profile) {
            $profile = new SchoolProfile();
        }
        $data = $request->except(['logo_path', 'kop_surat']);

        try {
            if ($request->hasFile('logo_path')) {
                // Delete old file if exists
                if ($profile->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->logo_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->logo_path);
                }
                $path = $request->file('logo_path')->store('profiles', 'public');
                $data['logo_path'] = $path;
            }

            if ($request->hasFile('kop_surat')) {
                // Delete old file if exists
                if ($profile->kop_surat && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->kop_surat)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->kop_surat);
                }
                $path = $request->file('kop_surat')->store('profiles', 'public');
                $data['kop_surat'] = $path;
            }

            if ($profile->exists) {
                $profile->update($data);
            } else {
                $profile->fill($data);
                $profile->save();
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }

        return back()->with('success', 'Identitas Sekolah berhasil diperbarui.');
    }
}
