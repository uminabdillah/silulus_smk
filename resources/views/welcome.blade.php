<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Kelulusan - {{ $school->nama_sekolah ?? 'Sekolah' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ $school->logo_path ? asset('storage/' . $school->logo_path) : asset('images/logo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url("{{ asset('images/bg_home.png') }}");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4">
    
    <div class="fixed inset-0 overlay z-0"></div>

    <div class="relative z-10 w-full max-w-4xl animate-fade-in">
        
        <!-- Header / Logo -->
        <div class="flex justify-between items-center mb-6 px-4">
            <div class="flex items-center gap-4">
                @if($school->logo_path)
                    <img src="{{ asset('storage/' . $school->logo_path) }}" class="h-12 w-auto object-contain" alt="Logo">
                @endif
                <div class="text-white">
                    <h1 class="text-2xl font-extrabold tracking-tight uppercase">{{ $school->nama_sekolah ?? 'PORTAL KELULUSAN' }}</h1>
                    <p class="text-xs opacity-70 font-medium tracking-wider">{{ $school->npsn ?? '' }} | {{ $school->alamat ?? '' }}</p>
                </div>
            </div>
            @if(Route::has('login'))
                <div class="text-right">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-200 hover:text-white underline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-200 hover:text-white border px-3 py-1 rounded-full border-white/30 hover:bg-white/10 transition">Admin Login</a>
                    @endauth
                </div>
            @endif
        </div>

        @if($status_code !== 'success')
            <!-- Search Form / Error View -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl overflow-hidden p-8 md:p-12">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-white mb-2">
                        @if($status_code === 'hold')
                            Akses Ditangguhkan
                        @elseif($status_code === 'outside_time')
                            Jadwal Pengumuman
                        @else
                            Cek Status Kelulusan
                        @endif
                    </h2>
                    <p class="text-blue-100 italic">
                        @if($status_code === 'hold')
                            Data ditemukan, namun akses Anda sedang dikunci.
                        @elseif($status_code === 'outside_time')
                            Pengumuman kelulusan akan segera dibuka.<br>
                            Silakan kembali pada {{ \Carbon\Carbon::parse($academicYear->announcement_start)->translatedFormat('d F Y') }} Pukul {{ \Carbon\Carbon::parse($academicYear->announcement_start)->format('H:i') }} WIB.
                        @else
                            Silakan masukkan NISN untuk melihat hasil seleksi kelulusan Anda.
                        @endif
                    </p>
                </div>

                @if($error && $status_code !== 'outside_time')
                    <div class="mb-6 p-4 {{ $status_code === 'hold' ? 'bg-orange-500/80 border-orange-400' : 'bg-red-500/80 border-red-400' }} border text-white text-center rounded-xl shadow-lg">
                        <p class="font-bold text-lg mb-1">{{ $status_code === 'hold' ? 'PERHATIAN!' : 'INFO' }}</p>
                        <p class="text-sm leading-relaxed">{{ $error }}</p>
                    </div>
                @endif

                @if($status_code !== 'outside_time' || ($status_code === 'outside_time' && $nisn))
                <form action="{{ route('home') }}" method="GET" class="max-w-md mx-auto">
                    <div class="relative group">
                        <input type="text" name="nisn" value="{{ $nisn }}" placeholder="Masukkan NISN Anda..." 
                               class="w-full bg-white/10 border-2 border-white/30 rounded-xl py-4 px-6 text-white text-xl placeholder-white/50 focus:outline-none focus:border-blue-400 focus:bg-white/20 transition-all text-center tracking-widest font-bold uppercase"
                               required @if($status_code === 'landing') autofocus @endif>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transform active:scale-95 transition-all text-lg uppercase tracking-wider">
                        @if($status_code === 'hold') Cek NISN Lain @else Lihat Hasil Seleksi @endif
                    </button>
                </form>
                @elseif($status_code === 'outside_time' && $academicYear->announcement_start && now()->lt($academicYear->announcement_start))
                <!-- Countdown Timer -->
                <div x-data="countdown('{{ $academicYear->announcement_start }}')" x-init="start()" class="max-w-md mx-auto">
                    <div class="grid grid-cols-4 gap-4 mb-8">
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-3 text-center">
                            <span class="block text-3xl font-black text-white" x-text="days">00</span>
                            <span class="text-[10px] text-blue-200 uppercase tracking-widest font-bold">Hari</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-3 text-center">
                            <span class="block text-3xl font-black text-white" x-text="hours">00</span>
                            <span class="text-[10px] text-blue-200 uppercase tracking-widest font-bold">Jam</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-3 text-center">
                            <span class="block text-3xl font-black text-white" x-text="minutes">00</span>
                            <span class="text-[10px] text-blue-200 uppercase tracking-widest font-bold">Menit</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-3 text-center">
                            <span class="block text-3xl font-black text-white" x-text="seconds">00</span>
                            <span class="text-[10px] text-blue-200 uppercase tracking-widest font-bold">Detik</span>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-white/60 text-sm mb-6 font-medium">Pengumuman akan dibuka secara otomatis saat waktu hitung mundur selesai.</p>
                        <button @click="window.location.reload()" class="bg-white/20 hover:bg-white/30 text-white px-8 py-3 rounded-full transition font-bold uppercase text-sm tracking-widest border border-white/10">
                            Refresh Halaman
                        </button>
                    </div>

                    <script>
                        function countdown(expiry) {
                            return {
                                expiry: new Date(expiry).getTime(),
                                days: '00',
                                hours: '00',
                                minutes: '00',
                                seconds: '00',
                                start() {
                                    setInterval(() => {
                                        const now = new Date().getTime();
                                        const distance = this.expiry - now;

                                        if (distance < 0) {
                                            window.location.reload();
                                            return;
                                        }

                                        this.days = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
                                        this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
                                        this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                                        this.seconds = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                                    }, 1000);
                                }
                            }
                        }
                    </script>
                </div>
                @elseif($status_code === 'outside_time')
                <div class="text-center">
                    <a href="{{ route('home') }}" class="inline-block bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-full transition">Refresh Halaman</a>
                </div>
                @endif

                <div class="mt-12 text-center">
                    <p class="text-white/60 text-xs uppercase tracking-[0.2em] font-medium">© {{ date('Y') }} - {{ $school->nama_sekolah ?? 'Sistem Kelulusan' }}</p>
                </div>
            </div>
        @else
            <!-- Result Page -->
            @php
                $isLulus = $student->status_lulus;
                $headerColor = $isLulus ? 'bg-blue-600' : 'bg-red-600';
                $statusText = $isLulus ? 'SELAMAT! ANDA DINYATAKAN LULUS!' : 'MOHON MAAF, ANDA DINYATAKAN TIDAK LULUS.';
            @endphp

            <div class="bg-slate-900/90 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Result Header -->
                <div class="{{ $headerColor }} p-5 md:p-8 flex justify-between items-center border-b border-white/10">
                    <h2 class="text-xl md:text-3xl font-black text-white tracking-wide uppercase">{{ $statusText }}</h2>
                    @if($school->logo_path)
                    <img src="{{ asset('storage/' . $school->logo_path) }}" class="h-12 w-auto object-contain" alt="Logo">
                @endif
                </div>

                <div class="p-6 md:p-10 relative">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        
                        <!-- Student Details -->
                        <div class="flex-grow space-y-8">
                            <div class="border-b border-white/5 pb-8">
                                <p class="text-blue-400 text-[10px] font-black uppercase tracking-[0.3em] mb-3 leading-none">Status Kelulusan Siswa</p>
                                <h3 class="text-4xl md:text-6xl font-black text-white leading-none tracking-tighter uppercase mb-4">{{ $student->nama_lengkap }}</h3>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="px-3 py-1 bg-blue-500 text-white rounded text-[10px] font-black uppercase tracking-widest leading-none">
                                        Kelas {{ $student->kelas ?? '-' }}
                                    </span>
                                    <span class="px-3 py-1 bg-slate-800 border border-slate-700 text-gray-400 rounded text-[10px] font-black uppercase tracking-widest leading-none">
                                        NISN • {{ $student->nisn }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                                <div class="group">
                                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2 group-hover:text-blue-400 transition-colors">Tanggal Lahir</p>
                                    @php
                                        $dateTranslated = \Carbon\Carbon::parse($student->tanggal_lahir)->translatedFormat('d F Y');
                                    @endphp
                                    <p class="text-white font-bold text-xl leading-tight uppercase tracking-tight">{{ $dateTranslated }}</p>
                                </div>
                                <div class="group">
                                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2 group-hover:text-blue-400 transition-colors">Satuan Pendidikan</p>
                                    <p class="text-white font-bold text-xl leading-tight uppercase tracking-tight">{{ $school->nama_sekolah ?? '-' }}</p>
                                </div>
                                <div class="group">
                                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2 group-hover:text-blue-400 transition-colors">Kabupaten/Kota</p>
                                    <p class="text-white font-bold text-xl leading-tight uppercase tracking-tight">{{ $school->kabupaten ?? '-' }}</p>
                                </div>
                                <div class="group">
                                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-2 group-hover:text-blue-400 transition-colors">Provinsi</p>
                                    <p class="text-white font-bold text-xl leading-tight uppercase tracking-tight">{{ $school->provinsi ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Side Actions / Verifikasi -->
                        <div class="w-full md:w-72 flex flex-col justify-between gap-8 border-t md:border-t-0 md:border-l border-white/10 pt-8 md:pt-0 md:pl-10">
                            <div class="space-y-6">
                                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4 md:text-right">Verifikasi Dokumen</p>
                                <div class="flex justify-center md:justify-end">
                                    <div class="bg-white p-2 rounded-2xl shadow-2xl shadow-blue-500/10 hover:scale-105 transition-transform duration-300">
                                        {!! QrCode::size(140)->backgroundColor(255,255,255)->color(15,23,42)->margin(2)->generate(route('verify.skl', $student->nisn)) !!}
                                    </div>
                                </div>
                                <p class="text-slate-600 text-[9px] font-medium text-center md:text-right leading-relaxed italic">Scan untuk verifikasi keaslian dokumen secara online.</p>
                            </div>

                            @if($isLulus)
                            <div class="bg-blue-600 rounded-2xl p-6 shadow-2xl shadow-blue-600/20 group hover:bg-blue-500 transition-all border border-blue-400/20">
                                <h4 class="font-black text-white text-xs uppercase tracking-widest mb-2">Cetak SKL</h4>
                                <p class="text-[10px] text-blue-100 leading-snug mb-4">Surat Keterangan Lulus resmi dapat diunduh sekarang.</p>
                                <a href="{{ route('students.download_skl', $student->id) }}" 
                                   class="flex items-center justify-center w-full bg-white text-blue-600 font-black py-3 rounded-xl text-[10px] uppercase tracking-[0.2em] hover:bg-blue-50 transition active:scale-95 shadow-lg">
                                   Download SKL
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Warning -->
                    <div class="mt-12 pt-6 border-t border-white/10">
                        <p class="text-[10px] text-gray-500 uppercase leading-relaxed text-center md:text-left">
                            Status kelulusan Anda ditetapkan setelah Sekolah melakukan verifikasi data akademik (rapor dan/atau nilai ujian). 
                            Silakan Anda membaca peraturan tentang kelulusan siswa.
                        </p>
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm font-medium border-b border-transparent hover:border-white transition pb-1">
                            Kembali ke Pencarian
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

</body>
</html>
