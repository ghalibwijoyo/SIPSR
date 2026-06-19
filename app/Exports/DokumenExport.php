<?php

namespace App\Exports;

use App\Models\Document;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DokumenExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    private $periode;
    private $rowNumber = 0;

    public function __construct(string $periode = '1_bulan')
    {
        $this->periode = $periode;
    }

    public function query()
    {
        $query = Document::query()
            ->with(['category', 'uploader'])
            ->whereNull('deleted_at');
        
        $sekarang = Carbon::now();
        $tanggalDari = null;
        
        switch ($this->periode) {
            case '1_hari':   $tanggalDari = $sekarang->copy()->subDay(); break;
            case '1_minggu': $tanggalDari = $sekarang->copy()->subWeek(); break;
            case '1_bulan':  $tanggalDari = $sekarang->copy()->subMonth(); break;
            case '1_tahun':  $tanggalDari = $sekarang->copy()->subYear(); break;
            case '5_tahun':  $tanggalDari = $sekarang->copy()->subYears(5); break;
        }

        if ($tanggalDari) {
            $query->where('tanggal_dokumen', '>=', $tanggalDari);
        }

        return $query->orderBy('tanggal_dokumen', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Dokumen',
            'Nama Dokumen',
            'Kategori',
            'Tanggal Dokumen',
            'Uploader',
            'Deskripsi',
            'Tanggal Upload'
        ];
    }

    public function map($dokumen): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $dokumen->nomor_dokumen,
            $dokumen->nama_dokumen,
            $dokumen->category->nama ?? '-',
            $dokumen->tanggal_dokumen ? $dokumen->tanggal_dokumen->format('Y-m-d') : '-',
            $dokumen->uploader->nama_lengkap ?? '-',
            $dokumen->deskripsi ?? '-',
            $dokumen->created_at ? $dokumen->created_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style header baris 1
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF3B6D11'] // Warna hijau PTPN
                ]
            ],
        ];
    }
}
