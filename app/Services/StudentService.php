<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StudentStatus;
use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

final class StudentService
{
    public function create(array $data): Student
    {
        $student = Student::create($data);
        $this->clearStatsCache((int) $data['academic_year_id']);

        return $student;
    }

    public function update(Student $student, array $data): Student
    {
        // Clear cache with old values before update
        $this->clearSearchCache($student->nis, $student->nisn, $student->tanggal_lahir->format('Y-m-d'));

        $student->update($data);
        $this->clearStatsCache($student->academic_year_id);

        // Clear cache with new values in case NIS/NISN/birthdate changed
        $updated = $student->fresh();
        $this->clearSearchCache($updated->nis, $updated->nisn, $updated->tanggal_lahir->format('Y-m-d'));

        return $updated;
    }

    public function delete(Student $student): void
    {
        $yearId = $student->academic_year_id;
        $this->clearSearchCache($student->nis, $student->nisn, $student->tanggal_lahir->format('Y-m-d'));
        $student->delete();
        $this->clearStatsCache($yearId);
    }

    public function updateStatus(Student $student, StudentStatus $status): Student
    {
        $student->update(['status' => $status]);
        $this->clearStatsCache($student->academic_year_id);
        $this->clearSearchCache($student->nis, $student->nisn, $student->tanggal_lahir->format('Y-m-d'));

        return $student;
    }

    public function bulkUpdateStatus(array $ids, StudentStatus $status, int $academicYearId): int
    {
        $students = Student::whereIn('id', $ids)
            ->where('academic_year_id', $academicYearId)
            ->get();

        $count = Student::whereIn('id', $ids)
            ->where('academic_year_id', $academicYearId)
            ->update(['status' => $status->value]);

        foreach ($students as $student) {
            $this->clearSearchCache($student->nis, $student->nisn, $student->tanggal_lahir->format('Y-m-d'));
        }

        $students->pluck('academic_year_id')->unique()->each(
            fn (int $yearId) => $this->clearStatsCache($yearId)
        );

        return $count;
    }

    public function getStudents(
        int $academicYearId,
        ?string $search = null,
        ?StudentStatus $status = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Student::where('academic_year_id', $academicYearId);

        if ($search) {
            $query->search($search);
        }

        if ($status) {
            $query->byStatus($status);
        }

        return $query->orderBy('nama_siswa')->paginate($perPage);
    }

    public function getStatistics(AcademicYear $year): array
    {
        return Cache::remember("stats:{$year->id}", 900, function () use ($year) {
            $total = $year->students()->count();
            $lulus = $year->students()->byStatus(StudentStatus::Lulus)->count();
            $tidakLulus = $year->students()->byStatus(StudentStatus::TidakLulus)->count();
            $pending = $year->students()->byStatus(StudentStatus::Pending)->count();

            return [
                'total' => $total,
                'lulus' => $lulus,
                'tidak_lulus' => $tidakLulus,
                'pending' => $pending,
                'persentase_lulus' => $total > 0 ? round(($lulus / $total) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Search student for public announcement.
     * Cache only the student ID to avoid __PHP_Incomplete_Class on deserialization.
     */
    public function publicSearch(string $identifier, string $tanggalLahir): ?Student
    {
        $cacheKey = "student_search:" . md5("{$identifier}:{$tanggalLahir}");

        $studentId = Cache::remember($cacheKey, 1800, function () use ($identifier, $tanggalLahir) {
            return Student::with('academicYear')
                ->whereHas('academicYear', fn ($q) => $q->accessible()->active())
                ->publicSearch($identifier, $tanggalLahir)
                ->first()
                ?->id;
        });

        if (!$studentId) {
            return null;
        }

        return Student::with('academicYear')->find($studentId);
    }

    private function clearSearchCache(string $nis, string $nisn, string $tanggalLahir): void
    {
        Cache::forget("student_search:" . md5("{$nis}:{$tanggalLahir}"));
        Cache::forget("student_search:" . md5("{$nisn}:{$tanggalLahir}"));
    }

    private function clearStatsCache(int $academicYearId): void
    {
        Cache::forget("stats:{$academicYearId}");
    }
}
