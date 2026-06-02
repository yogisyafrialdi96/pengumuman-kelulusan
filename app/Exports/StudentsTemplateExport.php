<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    public function array(): array
    {
        return [
            ['12345', '0012345678', 'Ahmad Fauzi', 'Budi Santoso', 'Jakarta', '15/05/2011', 'lulus', ''],
            ['12346', '0012345679', 'Siti Aisyah', 'Ahmad Rahman', 'Bandung', '22/08/2011', 'lulus', ''],
            ['12347', '0012345680', 'Muhammad Rizki', 'Hendra Wijaya', 'Surabaya', '03/12/2011', 'pending', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'nis',
            'nisn',
            'nama_siswa',
            'nama_orang_tua',
            'tempat_lahir',
            'tanggal_lahir',
            'status',
            'keterangan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 30,
            'D' => 30,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Example data style
        $sheet->getStyle('A2:H4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Note
        $sheet->setCellValue('A6', 'CATATAN:');
        $sheet->setCellValue('A7', '- Kolom status: lulus, tidak_lulus, atau pending');
        $sheet->setCellValue('A8', '- Format tanggal_lahir: DD/MM/YYYY atau YYYY-MM-DD');
        $sheet->setCellValue('A9', '- Hapus baris contoh (baris 2-4) sebelum mengisi data asli');
        $sheet->setCellValue('A10', '- Kolom keterangan bersifat opsional');

        $sheet->getStyle('A6:A6')->getFont()->setBold(true);
        $sheet->getStyle('A7:A10')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF6B7280'));

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
