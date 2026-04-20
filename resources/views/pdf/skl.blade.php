<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Keterangan Lulus - {{ $student->nama_lengkap }}</title>
    <style>
        @page {
            margin: 0.8cm 1.5cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            text-align: center;
        }
        .kop-image {
            width: 100%;
            max-height: 115px;
            object-fit: contain;
        }
        .header-bottom-line {
            border-bottom: 2px solid black;
            border-top: 0.5px solid black;
            height: 1.5px;
            width: 100%;
            margin: 2px 0 10px 0;
        }
        .title {
            text-align: center;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 10px;
        }
        .title span {
            display: block;
        }
        .content {
            text-align: justify;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .content table td {
            vertical-align: top;
            padding: 1px 0;
            border: none;
        }
        .lulus-status {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 12px 0;
            text-transform: uppercase;
        }

        .signature-section {
            width: 100%;
            margin-top: 10px;
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
            padding: 5px 15px;
            text-align: left;
        }
        .qr-code {
            width: 65px;
            height: 65px;
            margin: 3px 0;
        }

        /* Grade Table Styles */
        table.table-nilai {
            width: 100%;
            border-collapse: collapse !important;
            margin: 5px 0;
            font-size: 10pt;
        }
        .content table.table-nilai th, .content table.table-nilai td {
            border: 1px solid black !important;
            padding: 3px 6px;
            vertical-align: middle;
        }
        .content table.table-nilai th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }
        .content table.table-nilai tr.row-kelompok td {
            background-color: #f9f9f9;
            padding: 2px 6px;
        }
        .content table.table-nilai tr.row-rata-rata td {
            background-color: #f2f2f2;
        }

        /* Photo Box - Fixed Centering */
        .foto-box {
            width: 3cm;
            height: 4cm;
            border: 1.5px solid black;
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            font-size: 10pt;
            color: #444;
            background-color: #fff;
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

    <!-- Section: Digital Signature & Photo Block -->
    <div style="width: 100%; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Spacer to push the block to the right -->
                <td style="width: 45%;"></td>
                
                <!-- Main Block Container -->
                <td style="width: 55%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <!-- Photo Box (Left side of the block) -->
                            <td style="width: 3.2cm; vertical-align: top; padding-right: 15px;">
                                <table style="width: 3cm; height: 4cm; border: 1px solid black; background-color: #fff; border-collapse: collapse;">
                                    <tr>
                                        <td style="height: 4cm; vertical-align: middle; text-align: center; font-size: 9pt; color: #555; font-family: 'Times New Roman';">
                                            PAS FOTO<br>3 &times; 4
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <!-- Signature Info (Right side of the block) -->
                            <td style="vertical-align: top; text-align: center; font-family: 'Times New Roman';">
                                <div style="margin-bottom: 2px;">{{ $school->kabupaten ?? 'Brebes' }}, {{ \Carbon\Carbon::parse($academicYear->tanggal_kelulusan)->translatedFormat('j F Y') }}</div>
                                <div style="margin-bottom: 5px;">{{ $school->jabatan_penandatangan ?? 'Kepala Sekolah' }},</div>

                                <!-- QR CODE as Digital Signature (In the middle) -->
                                @if(file_exists($qrCodePath))
                                <div style="margin: 8px auto;">
                                    <img src="{{ $qrCodePath }}" style="width: 75px; height: 75px;" alt="Digital Signature">
                                </div>
                                @else
                                <div style="height: 80px;"></div> {{-- Placeholder for physical sign --}}
                                @endif

                                <div style="font-weight: bold; text-decoration: underline; margin-bottom: 0;">{{ $school->kepala_sekolah ?? 'Nama Kepala Sekolah' }}</div>
                                <div style="margin-top: 0;">NIP. {{ $school->nip_kepala ?? '-' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
