<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MajorProgram;
use App\Models\MajorConcentration;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $programs = MajorProgram::with('concentrations')->get();
        return view('admin.majors.index', compact('programs'));
    }

    public function storeProgram(Request $request)
    {
        $request->validate(['nama_program' => 'required|string|max:255']);
        MajorProgram::create($request->all());
        return back()->with('success', 'Program Keahlian berhasil ditambahkan!');
    }

    public function updateProgram(Request $request, MajorProgram $program)
    {
        $request->validate(['nama_program' => 'required|string|max:255']);
        $program->update($request->all());
        return back()->with('success', 'Program Keahlian berhasil diperbarui!');
    }

    public function destroyProgram(MajorProgram $program)
    {
        $program->delete();
        return back()->with('success', 'Program Keahlian berhasil dihapus!');
    }

    public function storeConcentration(Request $request)
    {
        $request->validate([
            'major_program_id' => 'required|exists:major_programs,id',
            'nama_konsentrasi'  => 'required|string|max:255',
        ]);
        MajorConcentration::create($request->all());
        return back()->with('success', 'Konsentrasi berhasil ditambahkan!');
    }

    public function destroyConcentration(MajorConcentration $concentration)
    {
        $concentration->delete();
        return back()->with('success', 'Konsentrasi berhasil dihapus!');
    }

    /**
     * API endpoint: return concentrations for a given program (used in student form JS).
     */
    public function concentrationsByProgram(MajorProgram $program)
    {
        return response()->json($program->concentrations);
    }
}
