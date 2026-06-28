<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dokumen SIPSR</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3B6D11;
            padding-bottom: 10px;
        }
        .header h3 {
            margin: 0;
            font-size: 18px;
            color: #3B6D11;
        }
        .header h4 {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>PTPN IV REGIONAL IV</h3>
        <h4>Bidang PSR Bagian Tanaman</h4>
        <p>LAPORAN DOKUMEN ARSIP — SIPSR</p>
        <div style="margin-top: 5px; font-weight: normal; font-size: 11px;">
            Periode: {{ $rentangWaktu }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="13%">Nomor Dokumen</th>
                <th width="20%">Nama Dokumen</th>
                <th width="12%">Nama Bank</th>
                <th width="10%">Kategori</th>
                <th width="10%">Tanggal Dokumen</th>
                <th width="12%">Uploader</th>
                <th width="20%">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dokumen as $index => $doc)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $doc->nomor_dokumen }}</td>
                <td>{{ $doc->nama_dokumen }}</td>
                <td>{{ $doc->bank->nama ?? '-' }}</td>
                <td>{{ $doc->category->nama ?? '-' }}</td>
                <td>{{ $doc->tanggal_dokumen?->format('d/m/Y') }}</td>
                <td>{{ $doc->uploader->nama_lengkap ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($doc->deskripsi, 50) ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data dokumen pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="border: none; padding: 0; margin: 0;">
            <tr>
                <td style="border: none; text-align: left; padding: 0;">Dicetak pada: {{ date('d/m/Y H:i') }}</td>
                <td style="border: none; text-align: right; padding: 0;">Halaman <span class="page-number"></span></td>
            </tr>
        </table>
    </div>

    <!-- Script for page numbers in DomPDF (if needed, but css counter works better) -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 760;
            $y = 570;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 9;
            $color = array(0.4, 0.4, 0.4);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>
