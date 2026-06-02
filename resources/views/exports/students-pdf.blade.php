<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa - {{ $academicYear->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px double #1e40af; }
        .header h1 { font-size: 16px; color: #1e40af; margin-bottom: 4px; }
        .header h2 { font-size: 13px; color: #374151; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #6b7280; }
        .stats { display: flex; margin-bottom: 15px; }
        .stats-table { width: 100%; margin-bottom: 15px; }
        .stats-table td { padding: 5px 10px; font-size: 10px; }
        .stats-label { font-weight: bold; color: #374151; }
        .stats-value { color: #1e40af; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background-color: #1e40af; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.data td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table.data tr:nth-child(even) { background-color: #f9fafb; }
        .status-lulus { color: #059669; font-weight: bold; }
        .status-tidak_lulus { color: #dc2626; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .filter-info { background-color: #eff6ff; padding: 8px 12px; border-radius: 4px; margin-bottom: 15px; font-size: 10px; color: #1e40af; border-left: 4px solid #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMPIT - DAFTAR KELULUSAN SISWA</h1>
        <h2>Tahun Ajaran {{ $academicYear->name }}</h2>
        @if($academicYear->description)
            <p>{{ $academicYear->description }}</p>
        @endif
    </div>

    @if($filterStatus)
        <div class="filter-info">
            Filter: {{ ucfirst(str_replace('_', ' ', $filterStatus)) }}
        </div>
    @endif

    <table class="stats-table">
        <tr>
            <td class="stats-label">Total Siswa:</td>
            <td class="stats-value">{{ $stats['total'] }}</td>
            <td class="stats-label">Lulus:</td>
            <td class="stats-value" style="color: #059669;">{{ $stats['lulus'] }}</td>
            <td class="stats-label">Tidak Lulus:</td>
            <td class="stats-value" style="color: #dc2626;">{{ $stats['tidak_lulus'] }}</td>
            <td class="stats-label">Pending:</td>
            <td class="stats-value" style="color: #d97706;">{{ $stats['pending'] }}</td>
            <td class="stats-label">% Kelulusan:</td>
            <td class="stats-value">{{ $stats['persentase_lulus'] }}%</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 70px;">NIS</th>
                <th style="width: 85px;">NISN</th>
                <th>Nama Siswa</th>
                <th>Nama Orang Tua</th>
                <th style="width: 100px;">Tempat Lahir</th>
                <th style="width: 80px;">Tgl Lahir</th>
                <th style="width: 70px;">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $student->nis }}</td>
                    <td>{{ $student->nisn }}</td>
                    <td>{{ $student->nama_siswa }}</td>
                    <td>{{ $student->nama_orang_tua }}</td>
                    <td>{{ $student->tempat_lahir }}</td>
                    <td>{{ $student->tanggal_lahir->format('d/m/Y') }}</td>
                    <td class="status-{{ $student->status->value }}">{{ $student->status->label() }}</td>
                    <td>{{ $student->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #9ca3af;">
                        Tidak ada data siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Sistem Pengumuman Kelulusan SMPIT
    </div>
</body>
</html>
