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
            'jenjang' => 'nullable|string|max:10',
            'logo_path' => 'nullable|file|image|mimes:jpeg,png,jpg,svg|max:5120',
            'kop_surat' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $profile = SchoolProfile::first();
        if (!$profile) {
            $profile = new SchoolProfile();
        }

        $data = $request->only([
            'nama_sekolah', 'npsn', 'alamat', 'kabupaten', 'provinsi', 
            'kepala_sekolah', 'nip_kepala', 'jabatan_penandatangan', 'jenjang'
        ]);

        try {
            if ($request->hasFile('logo_path')) {
                // Delete old file if exists
                if ($profile->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->logo_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->logo_path);
                }
                $path = $request->file('logo_path')->store('profiles', 'public');
                $profile->logo_path = $path;
            }

            if ($request->hasFile('kop_surat')) {
                // Delete old file if exists
                if ($profile->kop_surat && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->kop_surat)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->kop_surat);
                }
                $path = $request->file('kop_surat')->store('profiles', 'public');
                $profile->kop_surat = $path;
            }

            $profile->fill($data);
            $profile->save();

            \Illuminate\Support\Facades\Log::info('School Profile Updated', ['id' => $profile->id, 'data' => $data]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('School Profile Update Failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }

        return back()->with('success', 'Identitas ' . $profile->nama_sekolah . ' berhasil diperbarui.');
    }
}
