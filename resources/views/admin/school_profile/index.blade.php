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

                        @if ($errors->any())
                            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong class="font-bold">Ada kesalahan!</strong>
                                <ul class="mt-2 list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
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
                                    <x-input-label for="jenjang" :value="__('Jenjang Sekolah')" />
                                    <select id="jenjang" name="jenjang" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                        <option value="">-- Pilih Jenjang --</option>
                                        @foreach(['SD','MI','SMP','MTs','SMA','MA','SMK','MAK'] as $j)
                                            <option value="{{ $j }}" {{ old('jenjang', $profile->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Menentukan tampilan kolom jurusan di form siswa.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('jenjang')" />
                                </div>

                                <div>
                                    <x-input-label for="jabatan_penandatangan" :value="__('Jabatan Penandatangan')" />
                                    <select id="jabatan_penandatangan" name="jabatan_penandatangan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                        <option value="Kepala Sekolah" {{ old('jabatan_penandatangan', $profile->jabatan_penandatangan) == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                        <option value="Kepala Madrasah" {{ old('jabatan_penandatangan', $profile->jabatan_penandatangan) == 'Kepala Madrasah' ? 'selected' : '' }}>Kepala Madrasah</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('jabatan_penandatangan')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="logo_path" :value="__('Logo Sekolah (.png, .jpg, .svg)')" />
                                    <input id="logo_path" name="logo_path" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    <x-input-error class="mt-2" :messages="$errors->get('logo_path')" />
                                    
                                    @if($profile->logo_path)
                                        <div class="mt-2">
                                            <div class="border rounded-lg p-2 bg-gray-50 w-fit">
                                                <img src="{{ asset('storage/' . $profile->logo_path) }}" class="max-h-20 w-auto object-contain">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <x-input-label for="kop_surat" :value="__('Kop Surat / Header SKL (.png, .jpg)')" />
                                    <input id="kop_surat" name="kop_surat" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    <x-input-error class="mt-2" :messages="$errors->get('kop_surat')" />

                                    @if($profile->kop_surat)
                                        <div class="mt-2">
                                            <div class="border rounded-lg p-2 bg-gray-50">
                                                <img src="{{ asset('storage/' . $profile->kop_surat) }}" class="max-h-24 w-auto object-contain mx-auto">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="kepala_sekolah" :value="__('Nama Kepala Sekolah / Madrasah')" />
                                    <x-text-input id="kepala_sekolah" name="kepala_sekolah" value="{{ old('kepala_sekolah', $profile->kepala_sekolah) }}" type="text" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('kepala_sekolah')" />
                                </div>

                                <div>
                                    <x-input-label for="nip_kepala" :value="__('NIP')" />
                                    <x-text-input id="nip_kepala" name="nip_kepala" value="{{ old('nip_kepala', $profile->nip_kepala) }}" type="text" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('nip_kepala')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="kabupaten" :value="__('Kabupaten / Kota')" />
                                    <x-text-input id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $profile->kabupaten) }}" type="text" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('kabupaten')" />
                                </div>

                                <div>
                                    <x-input-label for="provinsi" :value="__('Provinsi')" />
                                    <x-text-input id="provinsi" name="provinsi" value="{{ old('provinsi', $profile->provinsi) }}" type="text" class="mt-1 block w-full" />
                                    <x-input-error class="mt-2" :messages="$errors->get('provinsi')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                                <textarea id="alamat" name="alamat" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" rows="2">{{ old('alamat', $profile->alamat) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>
</x-app-layout>
