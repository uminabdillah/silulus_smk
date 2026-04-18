<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Kelulusan & Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Tambah Pengaturan Baru') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Setiap tahun, Anda perlu mengatur tanggal dan upload master Word SKL terbaru.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('settings.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="tahun_ajaran" :value="__('Tahun Ajaran')" />
                                <x-text-input id="tahun_ajaran" name="tahun_ajaran" type="text" class="mt-1 block w-full" placeholder="Misal: 2025/2026" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tahun_ajaran')" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_pleno" :value="__('Tanggal Pleno / Rapat')" />
                                <x-text-input id="tanggal_pleno" name="tanggal_pleno" type="date" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_pleno')" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_kelulusan" :value="__('Tanggal Kelulusan (Tertera di SKL)')" />
                                <x-text-input id="tanggal_kelulusan" name="tanggal_kelulusan" type="date" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_kelulusan')" />
                            </div>

                            <div>
                                <x-input-label for="tempat" :value="__('Tempat Tanda Tangan')" />
                                <x-text-input id="tempat" name="tempat" type="text" class="mt-1 block w-full" placeholder="Misal: Brebes" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tempat')" />
                            </div>

                            <div>
                                <x-input-label for="nomor_skl_template" :value="__('Nomor Surat SKL (opsional)')" />
                                <x-text-input id="nomor_skl_template" name="nomor_skl_template" type="text" class="mt-1 block w-full" placeholder="Misal: 421.5/{nomor_urut}/SKL-SMK/VI/2026" />
                                <p class="mt-1 text-xs text-blue-600">Gunakan tag <b>{nomor_urut}</b> untuk penomoran otomatis bertambah (001, 002, dst) sesuai urutan abjad siswa lulus.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('nomor_skl_template')" />
                            </div>

                            <hr class="border-gray-50">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="announcement_start" :value="__('Waktu Pengumuman Dibuka')" />
                                    <x-text-input id="announcement_start" name="announcement_start" type="datetime-local" class="mt-1 block w-full" />
                                    <p class="mt-1 text-xs text-gray-500 italic">Siswa baru bisa cek kelulusan setelah jam ini.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('announcement_start')" />
                                </div>
                                <div>
                                    <x-input-label for="announcement_end" :value="__('Waktu Pengumuman Ditutup')" />
                                    <x-text-input id="announcement_end" name="announcement_end" type="datetime-local" class="mt-1 block w-full" />
                                    <p class="mt-1 text-xs text-gray-500 italic">Kosongkan jika tidak ingin ditutup otomatis.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('announcement_end')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="hold_message" :value="__('Pesan Penangguhan (Hold)')" />
                                <textarea id="hold_message" name="hold_message" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Misal: Mohon maaf, status kelulusan Anda ditangguhkan..."></textarea>
                                <p class="mt-1 text-xs text-blue-600 italic">Pesan ini muncul jika akses siswa 'Ditahan' di menu Manajemen Siswa.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('hold_message')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Daftar Pengaturan Kelulusan') }}
                        </h2>
                    </header>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full bg-white border border-gray-300 text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b border-gray-300">
                                    <th class="py-2 px-4 border text-left">Tahun Ajaran</th>
                                    <th class="py-2 px-4 border text-left">Tgl Pleno</th>
                                    <th class="py-2 px-4 border text-left">Tgl Lulus</th>
                                    <th class="py-2 px-4 border text-center">Status</th>
                                    <th class="py-2 px-4 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($academicYears as $year)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4 border">{{ $year->tahun_ajaran }}</td>
                                        <td class="py-2 px-4 border">{{ $year->tanggal_pleno }}</td>
                                        <td class="py-2 px-4 border">{{ $year->tanggal_kelulusan }}</td>
                                        <td class="py-2 px-4 border text-center">
                                            @if($year->is_active)
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Aktif</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border text-center whitespace-nowrap">
                                            @if(!$year->is_active)
                                            <form action="{{ route('settings.set_active', $year->id) }}" method="POST" class="inline-block mr-1">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded text-xs" title="Jadikan Aktif">Aktifkan</button>
                                            </form>
                                            @endif
                                            
                                            <a href="{{ route('settings.edit', $year->id) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 px-2 py-1 rounded text-xs inline-block mr-1">Edit</a>
                                            
                                            <form action="{{ route('settings.destroy', $year->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Tahun Ajaran ini secara permanen? Seluruh siswa mungkin akan kehilangan relasi datanya.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-2 py-1 rounded text-xs">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-4">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
