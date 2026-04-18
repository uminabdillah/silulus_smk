<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\StudentAccessLog;

class DashboardController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (!$activeYear) {
            return view('dashboard', [
                'totalSiswa' => 0,
                'lulus'      => 0,
                'tidakLulus' => 0,
                'dibuka'     => 0,
                'logs'       => collect([]),
            ]);
        }

        $totalSiswa = Student::where('academic_year_id', $activeYear->id)->count();
        $lulus      = Student::where('academic_year_id', $activeYear->id)->where('status_lulus', 1)->count();
        $tidakLulus = Student::where('academic_year_id', $activeYear->id)->where('status_lulus', 0)->count();

        // Unique students who opened their results in the active year
        $dibuka = StudentAccessLog::whereHas('student', function ($query) use ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        })->distinct('student_id')->count();

        // Recent history
        $logs = StudentAccessLog::with('student')
            ->whereHas('student', function ($query) use ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            })
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact('totalSiswa', 'lulus', 'tidakLulus', 'dibuka', 'logs', 'activeYear'));
    }
}
