<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with([])->orderBy('kelompok')->orderBy('nama_mapel')->get();
        // Load Programs with their Concentrations for dependent dropdowns
        $programs = \App\Models\MajorProgram::with('concentrations')->orderBy('nama_program')->get();
        
        return view('admin.subjects.index', compact('subjects', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kelompok' => 'required|in:A,B',
            'program_keahlian' => 'nullable|string',
            'konsentrasi_keahlian' => 'nullable|string'
        ]);

        Subject::create($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kelompok' => 'required|in:A,B',
            'program_keahlian' => 'nullable|string',
            'konsentrasi_keahlian' => 'nullable|string'
        ]);

        $subject->update($request->all());

        return back()->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}
