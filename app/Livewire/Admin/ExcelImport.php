<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Import Data Siswa')]
class ExcelImport extends Component
{
    use WithFileUploads;

    public AcademicYear $academicYear;
    public $file;
    public bool $importing = false;
    public bool $imported = false;
    public int $successCount = 0;
    public array $importErrors = [];

    public function mount(AcademicYear $academicYear): void
    {
        $this->academicYear = $academicYear;
    }

    protected function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ];
    }

    protected $messages = [
        'file.required' => 'File Excel wajib dipilih.',
        'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
        'file.max' => 'Ukuran file maksimal 5MB.',
    ];

    public function import(): void
    {
        $this->validate();
        $this->importing = true;
        $this->importErrors = [];

        try {
            $import = new StudentsImport($this->academicYear->id);
            Excel::import($import, $this->file->getRealPath());

            $failures = $import->failures();
            $this->importErrors = [];

            foreach ($failures as $failure) {
                $this->importErrors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }

            $totalRows = $this->academicYear->students()->count();
            $this->successCount = $totalRows;
            $this->imported = true;

            if (empty($this->importErrors)) {
                session()->flash('success', "Berhasil mengimpor data siswa.");
            } else {
                session()->flash('warning', "Import selesai dengan " . count($this->importErrors) . " baris error.");
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }

        $this->importing = false;
        $this->reset('file');
    }

    public function resetImport(): void
    {
        $this->reset(['file', 'importing', 'imported', 'successCount', 'importErrors']);
    }

    public function render()
    {
        return view('livewire.admin.excel-import');
    }
}
