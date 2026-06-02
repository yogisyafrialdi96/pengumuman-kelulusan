<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Services\StudentService;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        $activeYear = AcademicYear::active()->first();
        $stats = $activeYear ? app(StudentService::class)->getStatistics($activeYear) : null;
        $years = AcademicYear::withCount('students')->orderByDesc('created_at')->take(5)->get();

        return view('livewire.admin.dashboard-stats', [
            'activeYear' => $activeYear,
            'stats' => $stats,
            'recentYears' => $years,
        ]);
    }
}
