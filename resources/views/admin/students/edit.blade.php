<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN *</label>
                                <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nisn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="nis_lokal" class="block text-sm font-medium text-gray-700 mb-1">NIS Lokal</label>
                                <input type="text" name="nis_lokal" id="nis_lokal" value="{{ old('nis_lokal', $student->nis_lokal) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nis_lokal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas (misal: XII TKJ 1) *</label>
                                <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $student->kelas) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('kelas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $student->nama_lengkap) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $student->tempat_lahir) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tempat_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $student->tanggal_lahir) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tanggal_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="program_keahlian" class="block text-sm font-medium text-gray-700 mb-1">Program Keahlian</label>
                                <input type="text" name="program_keahlian" id="program_keahlian" value="{{ old('program_keahlian', $student->program_keahlian) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('program_keahlian') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="konsentrasi_keahlian" class="block text-sm font-medium text-gray-700 mb-1">Konsentrasi Keahlian</label>
                                <input type="text" name="konsentrasi_keahlian" id="konsentrasi_keahlian" value="{{ old('konsentrasi_keahlian', $student->konsentrasi_keahlian) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('konsentrasi_keahlian') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="status_lulus" class="block text-sm font-medium text-gray-700 mb-1">Status Lulus *</label>
                                <select name="status_lulus" id="status_lulus" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="0" {{ old('status_lulus', $student->status_lulus) == '0' ? 'selected' : '' }}>Belum / Tidak Lulus</option>
                                    <option value="1" {{ old('status_lulus', $student->status_lulus) == '1' ? 'selected' : '' }}>Lulus</option>
                                </select>
                                @error('status_lulus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('students.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
