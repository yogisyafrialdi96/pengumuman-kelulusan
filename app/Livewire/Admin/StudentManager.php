<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\StudentStatus;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\StudentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Data Siswa')]
class StudentManager extends Component
{
    use WithPagination;

    public AcademicYear $academicYear;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    public int $perPage = 15;

    // Form fields
    public string $nis = '';
    public string $nisn = '';
    public string $nama_siswa = '';
    public string $nama_orang_tua = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $status = 'pending';
    public string $keterangan = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Bulk actions
    public array $selected = [];
    public bool $selectAll = false;
    public string $bulkStatus = '';

    public function mount(AcademicYear $academicYear): void
    {
        $this->academicYear = $academicYear;
    }

    protected function rules(): array
    {
        $nisUnique = 'unique:students,nis,NULL,id,academic_year_id,' . $this->academicYear->id;
        $nisnUnique = 'unique:students,nisn,NULL,id,academic_year_id,' . $this->academicYear->id;

        if ($this->editingId) {
            $nisUnique = 'unique:students,nis,' . $this->editingId . ',id,academic_year_id,' . $this->academicYear->id;
            $nisnUnique = 'unique:students,nisn,' . $this->editingId . ',id,academic_year_id,' . $this->academicYear->id;
        }

        return [
            'nis' => ['required', 'string', 'max:20', $nisUnique],
            'nisn' => ['required', 'string', 'max:10', $nisnUnique],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nama_orang_tua' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'status' => ['required', 'in:lulus,tidak_lulus,pending'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected $messages = [
        'nis.required' => 'NIS wajib diisi.',
        'nis.unique' => 'NIS sudah terdaftar di tahun ajaran ini.',
        'nisn.required' => 'NISN wajib diisi.',
        'nisn.unique' => 'NISN sudah terdaftar di tahun ajaran ini.',
        'nama_siswa.required' => 'Nama siswa wajib diisi.',
        'nama_orang_tua.required' => 'Nama orang tua wajib diisi.',
        'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
        'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
        'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = Student::where('academic_year_id', $this->academicYear->id)
                ->when($this->search, fn ($q) => $q->search($this->search))
                ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function create(StudentService $service): void
    {
        $this->validate();

        $service->create([
            'academic_year_id' => $this->academicYear->id,
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'nama_siswa' => $this->nama_siswa,
            'nama_orang_tua' => $this->nama_orang_tua,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'status' => $this->status,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(int $id): void
    {
        $student = $this->academicYear->students()->findOrFail($id);
        $this->editingId = $id;
        $this->nis = $student->nis;
        $this->nisn = $student->nisn;
        $this->nama_siswa = $student->nama_siswa;
        $this->nama_orang_tua = $student->nama_orang_tua;
        $this->tempat_lahir = $student->tempat_lahir;
        $this->tanggal_lahir = $student->tanggal_lahir->format('Y-m-d');
        $this->status = $student->status->value;
        $this->keterangan = $student->keterangan ?? '';
        $this->showEditModal = true;
    }

    public function update(StudentService $service): void
    {
        $this->validate();

        $student = $this->academicYear->students()->findOrFail($this->editingId);
        $service->update($student, [
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'nama_siswa' => $this->nama_siswa,
            'nama_orang_tua' => $this->nama_orang_tua,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'status' => $this->status,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('success', 'Data siswa berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        $this->academicYear->students()->findOrFail($id); // verify ownership
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(StudentService $service): void
    {
        $student = $this->academicYear->students()->findOrFail($this->deletingId);
        $service->delete($student);

        $this->showDeleteModal = false;
        $this->deletingId = null;
        session()->flash('success', 'Data siswa berhasil dihapus.');
    }

    public function bulkUpdateStatus(StudentService $service): void
    {
        if (empty($this->selected) || empty($this->bulkStatus)) {
            return;
        }

        $status = StudentStatus::from($this->bulkStatus);
        $count = $service->bulkUpdateStatus(
            array_map('intval', $this->selected),
            $status,
            $this->academicYear->id,
        );

        $this->selected = [];
        $this->selectAll = false;
        $this->bulkStatus = '';
        session()->flash('success', "{$count} siswa berhasil diperbarui statusnya.");
    }

    private function resetForm(): void
    {
        $this->reset([
            'nis', 'nisn', 'nama_siswa', 'nama_orang_tua',
            'tempat_lahir', 'tanggal_lahir', 'keterangan', 'editingId',
        ]);
        $this->status = 'pending';
    }

    public function render()
    {
        $students = Student::where('academic_year_id', $this->academicYear->id)
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('nama_siswa')
            ->paginate($this->perPage);

        $stats = app(StudentService::class)->getStatistics($this->academicYear);

        return view('livewire.admin.student-manager', [
            'students' => $students,
            'stats' => $stats,
            'statuses' => StudentStatus::cases(),
        ]);
    }
}
