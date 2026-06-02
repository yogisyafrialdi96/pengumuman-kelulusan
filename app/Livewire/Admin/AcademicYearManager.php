<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kelola Tahun Ajaran')]
class AcademicYearManager extends Component
{
    public string $name = '';
    public ?string $announcement_datetime = '';
    public string $description = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        $uniqueRule = 'unique:academic_years,name';
        if ($this->editingId) {
            $uniqueRule .= ',' . $this->editingId;
        }

        return [
            'name' => ['required', 'string', 'max:20', $uniqueRule],
            'announcement_datetime' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected $messages = [
        'name.required' => 'Nama tahun ajaran wajib diisi.',
        'name.unique' => 'Tahun ajaran ini sudah ada.',
        'name.max' => 'Nama tahun ajaran maksimal 20 karakter.',
    ];

    public function openCreate(): void
    {
        $this->reset(['name', 'announcement_datetime', 'description', 'editingId']);
        $this->showCreateModal = true;
    }

    public function create(AcademicYearService $service): void
    {
        $this->validate();

        $service->create([
            'name' => $this->name,
            'announcement_datetime' => $this->announcement_datetime ?: null,
            'description' => $this->description ?: null,
        ]);

        $this->showCreateModal = false;
        $this->reset(['name', 'announcement_datetime', 'description']);
        session()->flash('success', 'Tahun ajaran berhasil dibuat.');
    }

    public function edit(int $id): void
    {
        $year = AcademicYear::findOrFail($id);
        $this->editingId = $id;
        $this->name = $year->name;
        $this->announcement_datetime = $year->announcement_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->description = $year->description ?? '';
        $this->showEditModal = true;
    }

    public function update(AcademicYearService $service): void
    {
        $this->validate();

        $year = AcademicYear::findOrFail($this->editingId);
        $service->update($year, [
            'name' => $this->name,
            'announcement_datetime' => $this->announcement_datetime ?: null,
            'description' => $this->description ?: null,
        ]);

        $this->showEditModal = false;
        $this->reset(['name', 'announcement_datetime', 'description', 'editingId']);
        session()->flash('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(AcademicYearService $service): void
    {
        $year = AcademicYear::findOrFail($this->deletingId);
        $service->delete($year);

        $this->showDeleteModal = false;
        $this->deletingId = null;
        session()->flash('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function toggleActive(int $id, AcademicYearService $service): void
    {
        $year = AcademicYear::findOrFail($id);

        if ($year->is_active) {
            $service->deactivate($year);
            session()->flash('success', "Tahun ajaran {$year->name} dinonaktifkan.");
        } else {
            $service->activate($year);
            session()->flash('success', "Tahun ajaran {$year->name} diaktifkan.");
        }
    }

    public function togglePublish(int $id, AcademicYearService $service): void
    {
        $year = AcademicYear::findOrFail($id);

        if ($year->is_published) {
            $service->unpublish($year);
            session()->flash('success', "Pengumuman {$year->name} ditutup.");
        } else {
            $service->publish($year);
            session()->flash('success', "Pengumuman {$year->name} dipublikasikan.");
        }
    }

    public function render()
    {
        $years = AcademicYear::withCount('students')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.admin.academic-year-manager', [
            'years' => $years,
        ]);
    }
}
