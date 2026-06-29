<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DokumenExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    private $filters;

    private $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Document::query()
            ->with(['category', 'uploader', 'bank']);

        // ── Smart Filters via Scopes ────────────────────────
        $query->search($this->filters['search'] ?? null)
            ->byCategory($this->filters['category_id'] ?? null)
            ->byBank($this->filters['bank_id'] ?? null)
            ->byUploader($this->filters['uploader_id'] ?? null)
            ->dateRange(
                $this->filters['tanggal_dari'] ?? null,
                $this->filters['tanggal_sampai'] ?? null
            );

        return $query->orderBy('tanggal_dokumen', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Dokumen',
            'Nama Dokumen',
            'Nama Bank',
            'Kategori',
            'Tanggal Dokumen',
            'Uploader',
            'Deskripsi',
            'Tanggal Upload',
        ];
    }

    public function map($dokumen): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $dokumen->nomor_dokumen,
            $dokumen->nama_dokumen,
            $dokumen->bank->nama ?? '-',
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
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF3B6D11'], // Warna hijau PTPN
                ],
            ],
        ];
    }
}
