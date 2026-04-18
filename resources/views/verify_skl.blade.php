<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Kelulusan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
            
            @if(isset($student) && $student->status_lulus)
                <div class="mb-4">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Dokumen Valid</h2>
                <p class="text-gray-600 mb-6">Surat Keterangan Lulus ini tercatat secara resmi di database kami.</p>

                <div class="text-left bg-gray-50 border border-gray-200 rounded p-4 space-y-3">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Nama Lengkap</span>
                        <span class="block text-sm text-gray-900">{{ $student->nama_lengkap }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">NISN / NIS</span>
                        <span class="block text-sm text-gray-900">{{ $student->nisn }} / {{ $student->nis_lokal ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Tahun Ajaran</span>
                        <span class="block text-sm text-gray-900">{{ optional($student->academicYear)->tahun_ajaran ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Status</span>
                        <span class="inline-block px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">LULUS</span>
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <svg class="mx-auto h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Dokumen Tidak Valid</h2>
                <p class="text-gray-600">Dokumen tidak ditemukan atau tidak memiliki status LULUS yang sah.</p>
            @endif

            <div class="mt-8">
                <a href="/" class="text-sm text-indigo-600 hover:text-indigo-900 underline">Ke Beranda Sekolah</a>
            </div>
        </div>
    </div>
</body>
</html>
