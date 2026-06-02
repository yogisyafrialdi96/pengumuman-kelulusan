<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    use Exportable;

    private int $rowNumber = 0;

    public function __construct(
        private readonly int $academicYearId,
        private readonly ?string $status = null,
    ) {}

    public function query()
    {
        $query = Student::where('academic_year_id', $this->academicYearId)
            ->orderBy('nama_siswa');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'NISN',
            'Nama Siswa',
            'Nama Orang Tua',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Status',
            'Keterangan',
        ];
    }

    public function map($student): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $student->nis,
            $student->nisn,
            $student->nama_siswa,
            $student->nama_orang_tua,
            $student->tempat_lahir,
            $student->tanggal_lahir->format('d/m/Y'),
            $student->status->label(),
            $student->keterangan ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 15,
            'D' => 30,
            'E' => 30,
            'F' => 20,
            'G' => 15,
            'H' => 15,
            'I' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // All cells border
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
