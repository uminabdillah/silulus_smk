<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\SklController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\SchoolProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('students/{student}/download-skl', [SklController::class, 'download'])->name('students.download_skl');

Route::get('/verify/{nisn}', function ($nisn) {
    $student = \App\Models\Student::with('academicYear')->where('nisn', $nisn)->first();
    return view('verify_skl', compact('student'));
})->name('verify.skl');

Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    


    Route::get('identitas-sekolah', [SchoolProfileController::class, 'index'])->name('school_profile.index');
    Route::post('identitas-sekolah', [SchoolProfileController::class, 'store'])->name('school_profile.store');

    Route::get('template-skl', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('template-skl', [TemplateController::class, 'store'])->name('templates.store');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('settings/{academicYear}/edit', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings/{academicYear}', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/{academicYear}', [SettingController::class, 'destroy'])->name('settings.destroy');
    Route::patch('settings/{academicYear}/active', [SettingController::class, 'setActive'])->name('settings.set_active');
    
    // Removed from here to make it public
    // Route::get('students/{student}/download-skl', [SklController::class, 'download'])->name('students.download_skl');
    
    Route::get('students/template', [StudentController::class, 'template'])->name('students.template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::post('students/bulk-release', [StudentController::class, 'bulkRelease'])->name('students.bulk_release');
    Route::post('students/bulk-hold', [StudentController::class, 'bulkHold'])->name('students.bulk_hold');
    Route::post('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk_delete');
    Route::patch('students/{student}/toggle-release', [StudentController::class, 'toggleRelease'])->name('students.toggle_release');
    Route::resource('students', StudentController::class);
});

require __DIR__.'/auth.php';
