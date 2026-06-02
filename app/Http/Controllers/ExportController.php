<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\StudentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService,
    ) {}

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new StudentsTemplateExport(), 'template_data_siswa.xlsx');
    }

    public function exportExcel(AcademicYear $academicYear, Request $request): BinaryFileResponse
    {
        $statusEnum = $request->enum('status', StudentStatus::class);
        $filename = "data_siswa_{$academicYear->name}" . ($statusEnum ? "_{$statusEnum->value}" : '') . '.xlsx';

        return Excel::download(
            new StudentsExport($academicYear->id, $statusEnum?->value),
            str_replace('/', '-', $filename)
        );
    }

    public function exportPdf(AcademicYear $academicYear, Request $request)
    {
        $statusEnum = $request->enum('status', StudentStatus::class);

        $query = Student::where('academic_year_id', $academicYear->id)
            ->orderBy('nama_siswa');

        if ($statusEnum) {
            $query->byStatus($statusEnum);
        }

        $students = $query->get();
        $stats = $this->studentService->getStatistics($academicYear);

        $pdf = Pdf::loadView('exports.students-pdf', [
            'academicYear' => $academicYear,
            'students' => $students,
            'stats' => $stats,
            'filterStatus' => $statusEnum?->value,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = "data_siswa_{$academicYear->name}" . ($statusEnum ? "_{$statusEnum->value}" : '') . '.pdf';

        return $pdf->download(str_replace('/', '-', $filename));
    }
}
