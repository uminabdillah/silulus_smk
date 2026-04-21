<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolProfile;

class SchoolProfileController extends Controller
{
    public function index()
    {
        $profile = SchoolProfile::find(1);
        if (!$profile) {
            $profile = SchoolProfile::first(); // fallback to any record
            if (!$profile) {
                $profile = SchoolProfile::create([
                    'id' => 1,
                    'nama_sekolah' => 'SMK MAARIF NU 01 JATIBARANG',
                    'npsn' => '12345678',
                    'alamat' => 'Jl. Kebon Jeruk No. 1',
                    'kabupaten' => 'Brebes',
                    'provinsi' => 'Jawa Tengah',
                    'kepala_sekolah' => 'Nama Kepala Sekolah',
                    'nip_kepala' => '-',
                    'jabatan_penandatangan' => 'Kepala Sekolah'
                ]);
            } else {
                // If ID is not 1, we force it to be 1 to maintain consistency
                if ($profile->id != 1) {
                    \Illuminate\Support\Facades\DB::table('school_profiles')->where('id', $profile->id)->update(['id' => 1]);
                    $profile = SchoolProfile::find(1);
                }
            }
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

        $profile = SchoolProfile::find(1) ?? SchoolProfile::first();
        if (!$profile) {
            $profile = new SchoolProfile();
            $profile->id = 1;
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

            // Force update using Query Builder on exact ID 1
            \Illuminate\Support\Facades\DB::table('school_profiles')
                ->updateOrInsert(['id' => 1], $data);

            \Illuminate\Support\Facades\Log::info('School Profile Updated', ['id' => 1, 'data' => $data]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('School Profile Update Failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }

        $profile = SchoolProfile::find(1); // Fresh data
        $debugInfo = " (ID: 1, Saved: " . ($data['jenjang'] ?? 'null') . ")";
        return back()->with('success', 'Identitas ' . $profile->nama_sekolah . ' berhasil diperbarui.' . $debugInfo);
    }
}
