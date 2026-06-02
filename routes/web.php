<?php

use App\Http\Controllers\ExportController;
use App\Livewire\Admin\AcademicYearManager;
use App\Livewire\Admin\ExcelImport;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\StudentManager;
use App\Livewire\GraduationSearch;
use Illuminate\Support\Facades\Route;

Route::get('/', GraduationSearch::class)->name('home')
    ->middleware('throttle:30,1');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Admin – Tahun Ajaran
    Route::get('/admin/tahun-ajaran', AcademicYearManager::class)->name('admin.academic-years.index');

    // Admin – Siswa per Tahun Ajaran
    Route::get('/admin/tahun-ajaran/{academicYear}/siswa', StudentManager::class)->name('admin.students.index');

    // Admin – Import Excel per Tahun Ajaran
    Route::get('/admin/tahun-ajaran/{academicYear}/import', ExcelImport::class)->name('admin.students.import');

    // Admin – Pengaturan
    Route::get('/admin/pengaturan', SiteSettings::class)->name('admin.settings');

    // Admin – Export
    Route::get('/admin/export/template', [ExportController::class, 'downloadTemplate'])->name('admin.export.template');
    Route::get('/admin/tahun-ajaran/{academicYear}/export/excel', [ExportController::class, 'exportExcel'])->name('admin.export.excel');
    Route::get('/admin/tahun-ajaran/{academicYear}/export/pdf', [ExportController::class, 'exportPdf'])->name('admin.export.pdf');
});

require __DIR__.'/settings.php';
