<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Keterangan Lulus - {{ $student->nama_lengkap }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            text-align: center;
        }
        .kop-image {
            width: 100%;
            max-height: 140px;
            object-fit: contain;
        }
        .header-bottom-line {
            border-bottom: 3px solid black;
            border-top: 1px solid black;
            height: 2px;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .title span {
            display: block;
        }
        .content {
            text-align: justify;
            margin-top: 10px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content table td {
            vertical-align: top;
            padding: 3px 0;
            border: none;
        }
        .lulus-status {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin: 25px 0;
            text-transform: uppercase;
        }

        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
        }
        .sign-box {
            border: none;
            padding: 15px;
            text-align: left;
        }
        .sign-box.no-border {
            border: none;
        }
        .qr-code {
            width: 80px;
            height: 80px;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    @if($school->kop_surat)
        <div class="header">
            <img src="{{ public_path('storage/' . $school->kop_surat) }}" class="kop-image">
        </div>
        <div class="header-bottom-line"></div>
    @else
        <div class="header">
            <h2 style="margin:0;">{{ $school->nama_sekolah }}</h2>
            <p style="margin:0;">NPSN: {{ $school->npsn }}</p>
        </div>
        <div class="header-bottom-line"></div>
    @endif

    <div class="title">
        <span>SURAT KETERANGAN LULUS</span>
        <span>Nomor : {{ $nomor_skl }}</span>
    </div>

    <div class="content">
        {!! $body_content !!}
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="sign-box" style="width: 50%; opacity: 0; border: 1px dashed white;">
                    <!-- Spacer -->
                </td>
                <td class="sign-box" style="width: 50%;">
                    <div>{{ $academicYear->tempat ?? 'Tempat' }}, {{ str_replace(array_keys(['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember']), array_values(['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember']), date('d F Y', strtotime($academicYear->tanggal_kelulusan))) }}</div>
                    <div style="margin-top: 5px;">Kepala Sekolah</div>
                    <div style="margin: 10px 0;">
                        <!-- QR Code image injected via base64 or absolute path -->
                        @if(file_exists($qrCodePath))
                            <img src="{{ $qrCodePath }}" class="qr-code" alt="QR Code">
                        @endif
                    </div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $school->kepala_sekolah ?? 'Nama Kepala Sekolah' }}</div>
                    <div>NIP. {{ $school->nip_kepala ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
