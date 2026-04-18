<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SchoolProfile;
use App\Models\AcademicYear;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $nisn = $request->input('nisn');
        $student = null;
        $error = null;
        $status_code = 'landing'; // landing, searching, success, hold, outside_time

        $school = SchoolProfile::first();
        $academicYear = AcademicYear::where('is_active', true)->first();

        $now = now();
        $start = $academicYear->announcement_start ? \Carbon\Carbon::parse($academicYear->announcement_start) : null;
        $end = $academicYear->announcement_end ? \Carbon\Carbon::parse($academicYear->announcement_end) : null;

        // Global Schedule Check
        if ($start && $now->lt($start)) {
            $status_code = 'outside_time';
            $error = "Pengumuman belum dibuka. Silakan kembali pada " . $start->translatedFormat('d F Y H:i') . " WIB.";
        } elseif ($end && $now->gt($end)) {
            $status_code = 'outside_time';
            $error = "Masa pengumuman telah berakhir.";
        }

        if ($nisn && $status_code !== 'outside_time') {
            $student = Student::with('academicYear')->where('nisn', $nisn)->first();
            if (!$student) {
                $status_code = 'searching';
                $error = "Data tidak ditemukan. Silakan cek kembali NISN Anda.";
            } elseif (!$student->is_released) {
                $status_code = 'hold';
                $error = $academicYear->hold_message ?? "Status kelulusan Anda sedang ditangguhkan. Silakan hubungi bagian administrasi.";
            } else {
                $status_code = 'success';

                // Log the access
                \App\Models\StudentAccessLog::create([
                    'student_id' => $student->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }

        return view('welcome', compact('student', 'school', 'academicYear', 'error', 'nisn', 'status_code'));
    }
}
