<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengaturan Kelulusan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Edit Tahun Ajaran: ') . $academicYear->tahun_ajaran }}
                            </h2>
                        </header>

                        <form method="post" action="{{ route('settings.update', $academicYear->id) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <x-input-label for="tahun_ajaran" :value="__('Tahun Ajaran')" />
                                <x-text-input id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', $academicYear->tahun_ajaran) }}" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tahun_ajaran')" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_pleno" :value="__('Tanggal Pleno / Rapat')" />
                                <x-text-input id="tanggal_pleno" name="tanggal_pleno" value="{{ old('tanggal_pleno', $academicYear->tanggal_pleno) }}" type="date" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_pleno')" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_kelulusan" :value="__('Tanggal Kelulusan (Tertera di SKL)')" />
                                <x-text-input id="tanggal_kelulusan" name="tanggal_kelulusan" value="{{ old('tanggal_kelulusan', $academicYear->tanggal_kelulusan) }}" type="date" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tanggal_kelulusan')" />
                            </div>

                            <div>
                                <x-input-label for="tempat" :value="__('Tempat Tanda Tangan')" />
                                <x-text-input id="tempat" name="tempat" value="{{ old('tempat', $academicYear->tempat) }}" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('tempat')" />
                            </div>

                            <div>
                                <x-input-label for="nomor_skl_template" :value="__('Nomor Surat SKL (opsional)')" />
                                <x-text-input id="nomor_skl_template" name="nomor_skl_template" value="{{ old('nomor_skl_template', $academicYear->nomor_skl_template) }}" type="text" class="mt-1 block w-full" />
                                <p class="mt-1 text-xs text-blue-600">Gunakan tag <b>{nomor_urut}</b> untuk penomoran otomatis bertambah (001, 002, dst).</p>
                                <x-input-error class="mt-2" :messages="$errors->get('nomor_skl_template')" />
                            </div>

                            <hr class="border-gray-50">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="announcement_start" :value="__('Waktu Pengumuman Dibuka')" />
                                    <x-text-input id="announcement_start" name="announcement_start" value="{{ old('announcement_start', $academicYear->announcement_start ? date('Y-m-d\TH:i', strtotime($academicYear->announcement_start)) : '') }}" type="datetime-local" class="mt-1 block w-full" />
                                    <p class="mt-1 text-xs text-gray-500 italic">Siswa baru bisa cek kelulusan setelah jam ini.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('announcement_start')" />
                                </div>
                                <div>
                                    <x-input-label for="announcement_end" :value="__('Waktu Pengumuman Ditutup')" />
                                    <x-text-input id="announcement_end" name="announcement_end" value="{{ old('announcement_end', $academicYear->announcement_end ? date('Y-m-d\TH:i', strtotime($academicYear->announcement_end)) : '') }}" type="datetime-local" class="mt-1 block w-full" />
                                    <p class="mt-1 text-xs text-gray-500 italic">Kosongkan jika tidak ingin ditutup otomatis.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('announcement_end')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="hold_message" :value="__('Pesan Penangguhan (Hold)')" />
                                <textarea id="hold_message" name="hold_message" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Misal: Mohon maaf, status kelulusan Anda ditangguhkan...">{{ old('hold_message', $academicYear->hold_message) }}</textarea>
                                <p class="mt-1 text-xs text-blue-600 italic">Pesan ini muncul jika akses siswa 'Ditahan' di menu Manajemen Siswa.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('hold_message')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Perbarui Data') }}</x-primary-button>
                                <a href="{{ route('settings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
