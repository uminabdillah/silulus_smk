<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Template Bodi SKL') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col md:flex-row gap-6">
                    
                    <!-- Form Editor -->
                    <div class="w-full md:w-3/4">
                        <form action="{{ route('templates.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Bodi Text dan Tabel SKL</label>
                                <textarea name="content" id="editor" class="w-full">{!! old('content', $template->content) !!}</textarea>
                            </div>
                            <x-primary-button>Simpan Template</x-primary-button>
                        </form>
                    </div>

                    <!-- Cheat Sheet -->
                    <div class="w-full md:w-1/4 bg-gray-50 p-4 border rounded">
                        <h4 class="font-bold text-sm mb-3">Kode Variabel (Tags)</h4>
                        <p class="text-xs text-gray-600 mb-3">Copy-paste tag di bawah ini ke dalam editor teks. Sistem akan mengubahnya otomatis menjadi data siswa asli setiap kali SKL dicetak.</p>
                        
                        <div class="text-xs space-y-2 font-mono text-indigo-700 bg-white p-3 border rounded shadow-inner">
                            <div>{nama_lengkap}</div>
                            <div>{nisn}</div>
                            <div>{tempat_lahir}</div>
                            <div>{tgl_lahir}</div>
                            <div>{program_keahlian}</div>
                            <div>{konsentrasi_keahlian}</div>
                            <div class="border-t pt-2 mt-2">{nama_sekolah}</div>
                            <div>{nomor_skl}</div>
                            <div>{tanggal_pleno}</div>
                            <div>{tanggal_kelulusan}</div>
                            <div>{tahun_ajaran}</div>
                            <div>{lulus_tidak}</div>
                        </div>

                        <div class="mt-4 text-xs text-red-600 font-bold bg-red-50 p-2 rounded">
                            Peringatan:
                            <span class="font-normal block mt-1 text-gray-700">Jangan merusak letak Tanda Kurung Kurawal `{ }` gara-gara salah format font ya. Terus pastikan saat membikin tabel biodata, garis ketebalannya diatur ke <b>0 (nol)</b> agar sama seperti aslinya.</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Script TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#editor',
            height: 600,
            plugins: 'table lists link fullscreen code',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | table | numlist bullist | code fullscreen',
            content_style: "body { font-family: 'Times New Roman', serif; font-size: 12pt; text-align: justify; } table { width: 100%; border-collapse: collapse; } td { padding: 5px; vertical-align: top; } .col-colon { width: 3%; text-align:center; } .col-label { width: 35%; } .col-val { width: 62% }",
            valid_elements: '*[*]',
            promotion: false // Disable premium promo banner
        });
    </script>
</x-app-layout>
