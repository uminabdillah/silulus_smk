<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Identitas Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Data Induk Sekolah') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Perbarui identitas profil sekolah Anda yang akan digunakan untuk pencetakan SKL dan tampilan aplikasi.") }}
                            </p>
                        </header>

                        @if (session('success'))
                            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        <form method="post" action="{{ route('school_profile.store') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf

                            <div>
                                <x-input-label for="nama_sekolah" :value="__('Nama Sekolah')" />
                                <x-text-input id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah', $profile->nama_sekolah) }}" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('nama_sekolah')" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="npsn" :value="__('NPSN')" />
                                    <x-text-input id="npsn" name="npsn" value="{{ old('npsn', $profile->npsn) }}" type="text" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('npsn')" />
                                </div>

                                <div>
                                    <x-input-label for="kop_surat" :value="__('Logo / Kop Surat (.png, .jpg)')" />
                                    <input id="kop_surat" name="kop_surat" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    <x-input-error class="mt-2" :messages="$errors->get('kop_surat')" />
                                </div>
                            </div>

                            @if($profile->kop_surat)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-2">Pratinjau Kop Surat:</p>
                                    <div class="border rounded-lg p-2 bg-gray-50">
                                        <img src="{{ asset('storage/' . $profile->kop_surat) }}" class="max-h-32 w-auto object-contain mx-auto">
                                    </div>
                                </div>
                            @endif

                            <div>
                                <x-input-label for="kepala_sekolah" :value="__('Nama Kepala Sekolah (Beserta gelar)')" />
                                <x-text-input id="kepala_sekolah" name="kepala_sekolah" value="{{ old('kepala_sekolah', $profile->kepala_sekolah) }}" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('kepala_sekolah')" />
                            </div>

                            <div>
                                <x-input-label for="nip_kepala" :value="__('NIP Kepala Sekolah')" />
                                <x-text-input id="nip_kepala" name="nip_kepala" value="{{ old('nip_kepala', $profile->nip_kepala) }}" type="text" class="mt-1 block w-full" />
                                <x-input-error class="mt-2" :messages="$errors->get('nip_kepala')" />
                            </div>

                            <div>
                                <x-input-label for="alamat" :value="__('Alamat Sekolah')" />
                                <textarea id="alamat" name="alamat" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" rows="3">{{ old('alamat', $profile->alamat) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan Identitas') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
