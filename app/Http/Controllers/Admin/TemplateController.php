<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SklTemplate;

class TemplateController extends Controller
{
    public function index()
    {
        $template = SklTemplate::first();
        
        if (!$template) {
            $template = SklTemplate::create([
                'content' => '<p>Template belum dikonfigurasi. Silakan ketik isi surat di sini.</p>'
            ]);
        }
        
        return view('admin.templates.index', compact('template'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $template = SklTemplate::first();
        $template->update(['content' => $request->content]);

        return back()->with('success', 'Layout Bodi SKL berhasil diperbarui!');
    }
}
