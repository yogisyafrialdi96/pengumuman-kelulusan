<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class AcademicYearService
{
    public function create(array $data): AcademicYear
    {
        $year = AcademicYear::create($data);
        $this->clearCache();

        return $year;
    }

    public function update(AcademicYear $year, array $data): AcademicYear
    {
        $year->update($data);
        $this->clearCache();

        return $year->fresh();
    }

    public function delete(AcademicYear $year): void
    {
        $year->delete();
        $this->clearCache();
    }

    public function activate(AcademicYear $year): void
    {
        DB::transaction(function () use ($year) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
            $year->update(['is_active' => true]);
        });

        $this->clearCache();
    }

    public function deactivate(AcademicYear $year): void
    {
        $year->update(['is_active' => false]);
        $this->clearCache();
    }

    public function publish(AcademicYear $year): void
    {
        $year->update(['is_published' => true]);
        $this->clearCache();
        $this->clearStudentCache($year);
    }

    public function unpublish(AcademicYear $year): void
    {
        $year->update(['is_published' => false]);
        $this->clearCache();
        $this->clearStudentCache($year);
    }

    public function schedulePublish(AcademicYear $year, string $datetime): void
    {
        $year->update([
            'announcement_datetime' => $datetime,
            'is_published' => false,
        ]);
        $this->clearCache();
    }

    public function getActive(): ?AcademicYear
    {
        return Cache::remember('active_academic_year', 3600, fn () =>
            AcademicYear::active()->first()
        );
    }

    public function getAccessible(): ?AcademicYear
    {
        return AcademicYear::accessible()->active()->first();
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('all_academic_years', 3600, fn () =>
            AcademicYear::withCount('students')->orderByDesc('created_at')->get()
        );
    }

    private function clearCache(): void
    {
        Cache::forget('active_academic_year');
        Cache::forget('all_academic_years');
        Cache::forget('published_years');
    }

    private function clearStudentCache(AcademicYear $year): void
    {
        Cache::forget("stats:{$year->id}");
    }
}
