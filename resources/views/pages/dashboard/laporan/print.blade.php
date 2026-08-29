<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Pengaduan Sekolah ({{ $startDate }} s.d {{ $endDate }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Geist', ui-sans-serif, system-ui, sans-serif;
            color: #09090b;
            background: #fff;
            margin: 0;
            padding: 24px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #09090b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 15px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .header h2 {
            font-size: 13px;
            margin: 0 0 4px 0;
            font-weight: 600;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #52525b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
        }
        .meta-table td {
            padding: 3px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #e4e4e7;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f4f4f5;
            font-weight: 600;
            font-family: 'Geist Mono', monospace;
            font-size: 11px;
            text-transform: uppercase;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 55px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="background: #f6821f; color: #fff; border: none; padding: 7px 14px; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <!-- Official Header (Kop Surat) -->
    <div class="header">
        <h1>Pemerintah Provinsi Dinas Pendidikan</h1>
        <h2>Sistem Informasi Pengaduan & Aspirasi Sekolah Terpadu</h2>
        <p>Jl. Pendidikan No. 45, Kompleks Edukasi &bull; Telp: (021) 555-0199 &bull; Email: pengaduan@sekolah.sch.id</p>
    </div>

    <div style="text-align: center; font-weight: 700; font-size: 13px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: -0.01em;">
        Rekapitulasi Laporan & Tindak Lanjut Pengaduan
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Periode Data</strong></td>
            <td style="width: 35%;">: {{ date('d F Y', strtotime($startDate)) }} s.d {{ date('d F Y', strtotime($endDate)) }}</td>
            <td style="width: 15%;"><strong>Total Laporan</strong></td>
            <td style="width: 35%;">: {{ $complaints->count() }} Kasus</td>
        </tr>
        <tr>
            <td><strong>Status Filter</strong></td>
            <td>: {{ ucfirst(str_replace('_', ' ', $status)) }}</td>
            <td><strong>Dicetak Pada</strong></td>
            <td>: {{ date('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 16%;">No. Tiket</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Pelapor</th>
                <th style="width: 15%;">Kategori</th>
                <th>Uraian Pengaduan & Hasil Penanganan</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $index => $c)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: 'Geist Mono', monospace; font-weight: 600;">{{ $c->ticket_code }}</td>
                    <td>{{ $c->created_at->format('d/m/Y') }}</td>
                    <td>{{ $c->is_anonymous ? 'Anonim' : $c->reporter_name }}</td>
                    <td>{{ $c->category->name }}</td>
                    <td>
                        <strong>{{ $c->title }}</strong><br>
                        <small style="color: #52525b;">{{ Str::limit($c->description, 100) }}</small>
                        @if($c->resolution_notes)
                            <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dashed #d4d4d8; font-size: 10px; color: #15803d;">
                                <strong>Tindak Lanjut:</strong> {{ Str::limit($c->resolution_notes, 80) }}
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $c->status_label }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data pengaduan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Official Signatures -->
    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Guru Piket / Tim Pengaduan</strong>
                <div class="signature-space"></div>
                <strong>( Dra. Endang Sulistyowati )</strong><br>
                <small>NIP. 197605122005012003</small>
            </td>
            <td>
                Menyetujui,<br>
                <strong>Kepala Sekolah</strong>
                <div class="signature-space"></div>
                <strong>( Dr. H. Mulyadi Subagyo, M.Pd. )</strong><br>
                <small>NIP. 196802141994031002</small>
            </td>
        </tr>
    </table>

</body>
</html>
