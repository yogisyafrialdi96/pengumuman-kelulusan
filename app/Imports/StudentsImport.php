<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\StudentStatus;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function __construct(
        private readonly int $academicYearId,
    ) {}

    public function model(array $row): Student
    {
        $status = $this->parseStatus($row['status'] ?? 'pending');
        $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? '');

        return new Student([
            'academic_year_id' => $this->academicYearId,
            'nis' => trim((string) $row['nis']),
            'nisn' => trim((string) $row['nisn']),
            'nama_siswa' => trim((string) $row['nama_siswa']),
            'nama_orang_tua' => trim((string) $row['nama_orang_tua']),
            'tempat_lahir' => trim((string) $row['tempat_lahir']),
            'tanggal_lahir' => $tanggalLahir,
            'status' => $status,
            'keterangan' => trim((string) ($row['keterangan'] ?? '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:20'],
            'nisn' => ['required', 'string', 'max:10'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nama_orang_tua' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required'],
            'status' => ['nullable'],
            'keterangan' => ['nullable'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nis.required' => 'Kolom NIS wajib diisi.',
            'nisn.required' => 'Kolom NISN wajib diisi.',
            'nama_siswa.required' => 'Kolom Nama Siswa wajib diisi.',
            'nama_orang_tua.required' => 'Kolom Nama Orang Tua wajib diisi.',
            'tempat_lahir.required' => 'Kolom Tempat Lahir wajib diisi.',
            'tanggal_lahir.required' => 'Kolom Tanggal Lahir wajib diisi.',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function parseStatus(string $status): StudentStatus
    {
        $normalized = strtolower(trim($status));

        return match (true) {
            str_contains($normalized, 'lulus') && !str_contains($normalized, 'tidak') => StudentStatus::Lulus,
            str_contains($normalized, 'tidak') => StudentStatus::TidakLulus,
            default => StudentStatus::Pending,
        };
    }

    private function parseDate(mixed $date): string
    {
        if ($date instanceof \DateTimeInterface) {
            return Carbon::instance($date)->format('Y-m-d');
        }

        if (is_numeric($date)) {
            return Carbon::createFromTimestamp(($date - 25569) * 86400)->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $date)->format('Y-m-d');
        } catch (\Exception) {
            return now()->format('Y-m-d');
        }
    }
}
