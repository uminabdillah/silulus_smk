<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SchoolProfile;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class SklController extends Controller
{
    public function download(Student $student)
    {
        try {
            $pdf = $this->generatePdf($student);
            return $pdf->download('SKL_' . $student->nisn . '_' . $student->nama_lengkap . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkDownload(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || count($ids) === 0) {
            return back()->with('error', 'Pilih minimal satu siswa.');
        }

        $students = Student::whereIn('id', $ids)->get();
        
        $zip = new ZipArchive();
        $zipFileName = 'SKL_Massal_' . date('YmdHis') . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $count = 0;
            foreach ($students as $student) {
                if ($student->status_lulus === 'tidak lulus') continue;

                try {
                    $pdf = $this->generatePdf($student);
                    // Sanitasi nama file untuk ZIP
                    $safeName = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ' '], '_', $student->nama_lengkap);
                    $fileName = 'SKL_' . $student->nisn . '_' . $safeName . '.pdf';
                    $zip->addFromString($fileName, $pdf->output());
                    $count++;
                } catch (\Exception $e) {
                    // Skip if error occurs for a single student
                    continue;
                }
            }
            $zip->close();

            if ($count === 0) {
                if (file_exists($zipFilePath)) unlink($zipFilePath);
                return back()->with('error', 'Tidak ada SKL yang valid untuk diunduh (mungkin siswa terpilih berstatus Tidak Lulus).');
            }
        } else {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        if (file_exists($zipFilePath)) {
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Terjadi kesalahan saat menyiapkan file unduhan.');
    }

    private function generatePdf(Student $student)
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            throw new \Exception('Tahun ajaran aktif belum diatur di menu Pengaturan.');
        }

        $school = SchoolProfile::first();
        if (!$school) {
            throw new \Exception('Profil sekolah belum diisi di database.');
        }

        if ($student->status_lulus === 'tidak lulus') {
            throw new \Exception('Siswa ini dinyatakan Tidak Lulus.');
        }

        // Menghitung Urutan Siswa (Penomoran surat dinamis)
        $allGraduates = Student::where('academic_year_id', $student->academic_year_id)
                                ->whereIn('status_lulus', ['lulus', 'lulus bersyarat'])
                                ->orderBy('nama_lengkap', 'asc')
                                ->pluck('id');
        
        $indexSurat = $allGraduates->search($student->id) + 1; 
        $paddedIndex = str_pad($indexSurat, 3, "0", STR_PAD_LEFT);

        $nomorSklMentah = $academicYear->nomor_skl_template ?? '-';
        $nomor_skl = str_replace('{nomor_urut}', $paddedIndex, $nomorSklMentah);

        // QR Code Generation
        $qrDir = Storage::disk('public')->path('qr');
        if (!file_exists($qrDir)) mkdir($qrDir, 0755, true);
        
        $qrCodePath = $qrDir . '/' . $student->nisn . '.png';
        
        if (!file_exists($qrCodePath)) {
            $qrDataUrl = route('verify.skl', $student->nisn);
            $qrImage = @file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrDataUrl));
            if ($qrImage) {
                file_put_contents($qrCodePath, $qrImage);
            }
        }

        // Date Helper (Indonesian)
        $months = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        $formatDateIndo = function($date) use ($months) {
            if (!$date) return '-';
            $enDate = date('d F Y', strtotime($date));
            return str_replace(array_keys($months), array_values($months), $enDate);
        };

        // Fetch the active Template from DB
        $template = \App\Models\SklTemplate::first();
        $rawHtml = $template ? $template->content : '<p>Admin belum mengkonfigurasi Template SKL.</p>';
        
        $student->load('majorProgram', 'majorConcentration');
        $programKeahlian = $student->majorProgram?->nama_program ?? $student->program_keahlian ?? '-';
        $konsentrasiKeahlian = $student->majorConcentration?->nama_konsentrasi ?? $student->konsentrasi_keahlian ?? '-';

        $replacements = [
            '{nama_sekolah}' => $school->nama_sekolah ?? '-',
            '{npsn_sekolah}' => $school->npsn ?? '-',
            '{nama_lengkap}' => $student->nama_lengkap ?? '-',
            '{tempat_lahir}' => $student->tempat_lahir ?? '-',
            '{tgl_lahir}' => $formatDateIndo($student->tanggal_lahir),
            '{nisn}' => $student->nisn ?? '-',
            '{program_keahlian}' => $programKeahlian,
            '{konsentrasi_keahlian}' => $konsentrasiKeahlian,
            '{tanggal_pleno}' => $formatDateIndo($academicYear->tanggal_pleno),
            '{lulus_tidak}' => strtoupper($student->status_lulus),
            '{tanggal_kelulusan}' => $formatDateIndo($academicYear->tanggal_kelulusan),
            '{tahun_ajaran}' => $academicYear->tahun_ajaran ?? '-',
            '{nomor_skl}' => $nomor_skl ?? '-',
            '{kabupaten_sekolah}' => $school->kabupaten ?? '-',
            '{provinsi_sekolah}' => $school->provinsi ?? '-',
            '{jabatan_kepala}' => $school->jabatan_penandatangan ?? 'Kepala Sekolah',
            '{kelas}' => $student->kelas ?? '-',
            '{tabel_nilai}' => $this->generateGradeTable($student),
        ];

        $body_content = str_replace(array_keys($replacements), array_values($replacements), $rawHtml);

        $pdf = Pdf::loadView('pdf.skl', compact('student', 'school', 'academicYear', 'nomor_skl', 'qrCodePath', 'body_content'));
        $pdf->setPaper(array(0, 0, 609.4488, 935.433), 'portrait');

        return $pdf;
    }

    private function generateGradeTable(Student $student)
    {
        $subjects = Subject::where(function($query) use ($student) {
            $query->whereNull('program_keahlian')->whereNull('konsentrasi_keahlian');
            if ($student->program_keahlian) {
                $query->orWhere(function($subq) use ($student) {
                    $subq->where('program_keahlian', $student->program_keahlian)
                         ->whereNull('konsentrasi_keahlian');
                });
            }
            if ($student->konsentrasi_keahlian) {
                $query->orWhere('konsentrasi_keahlian', $student->konsentrasi_keahlian);
            }
        })->orderBy('kelompok')->orderBy('id')->get();

        $grades = Grade::where('student_id', $student->id)->pluck('nilai', 'subject_id');

        $html = '<table class="table-nilai">
                    <thead>
                        <tr>
                            <th width="5%">No.</th>
                            <th width="75%">Mata Pelajaran</th>
                            <th width="20%">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>';

        $kelompokNames = [
            'A' => 'A. Kelompok Mata Pelajaran Umum',
            'B' => 'B. Kelompok Mata Pelajaran Kejuruan'
        ];

        $currentKelompok = null;
        $no = 1;
        $totalNilai = 0;
        $countMapel = 0;

        foreach ($subjects as $subject) {
            if ($currentKelompok !== $subject->kelompok) {
                $currentKelompok = $subject->kelompok;
                $html .= '<tr class="row-kelompok">
                            <td colspan="3"><b>' . ($kelompokNames[$currentKelompok] ?? $currentKelompok) . '</b></td>
                          </tr>';
                $no = 1;
            }

            $nilai = $grades[$subject->id] ?? '-';
            if (is_numeric($nilai)) {
                $totalNilai += $nilai;
                $countMapel++;
            }

            $html .= '<tr>
                        <td align="center">' . $no++ . '.</td>
                        <td>' . $subject->nama_mapel . '</td>
                        <td align="center">' . $nilai . '</td>
                      </tr>';
        }

        $rataRata = $countMapel > 0 ? number_format($totalNilai / $countMapel, 2) : '-';

        $html .= '<tr class="row-rata-rata">
                    <td colspan="2" align="center"><b>Rata-rata</b></td>
                    <td align="center"><b>' . $rataRata . '</b></td>
                  </tr>';

        $html .= '</tbody></table>';

        return $html;
    }
}
