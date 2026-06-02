<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AcademicYear;
use App\Models\Setting;
use App\Models\Student;
use App\Services\StudentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Pengumuman Kelulusan')]
class GraduationSearch extends Component
{
    public string $identifier  = '';
    public string $hari        = '';
    public string $bulan       = '';
    public string $tahun       = '';
    public string $step        = 'form'; // 'form' | 'result'
    public ?Student $result    = null;
    public ?string $errorMessage = null;
    public ?AcademicYear $activeYear = null;

    public function mount(): void
    {
        $this->activeYear = AcademicYear::active()->first();
    }

    protected function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:20'],
            'hari'       => ['required', 'integer', 'between:1,31'],
            'bulan'      => ['required', 'integer', 'between:1,12'],
            'tahun'      => ['required', 'integer', 'min:1990', 'max:' . now()->year],
        ];
    }

    protected $messages = [
        'identifier.required' => 'NIS atau NISN wajib diisi.',
        'hari.required'       => 'Tanggal lahir wajib diisi.',
        'hari.between'        => 'Tanggal tidak valid (1-31).',
        'bulan.required'      => 'Bulan lahir wajib diisi.',
        'bulan.between'       => 'Bulan tidak valid.',
        'tahun.required'      => 'Tahun lahir wajib diisi.',
        'tahun.min'           => 'Tahun lahir tidak valid.',
        'tahun.max'           => 'Tahun lahir tidak valid.',
    ];

    public function search(StudentService $service): void
    {
        $this->resetValidation();
        $this->validate();
        $this->errorMessage = null;
        $this->result = null;

        if (!checkdate((int) $this->bulan, (int) $this->hari, (int) $this->tahun)) {
            $this->errorMessage = 'Tanggal lahir tidak valid. Periksa kembali tanggal yang Anda masukkan.';
            return;
        }

        $tanggalLahir = sprintf('%04d-%02d-%02d', $this->tahun, $this->bulan, $this->hari);

        if (!$this->activeYear) {
            $this->errorMessage = 'Belum ada tahun ajaran yang aktif.';
            return;
        }

        if (!$this->activeYear->is_accessible) {
            if ($this->activeYear->announcement_datetime) {
                $this->errorMessage = 'Pengumuman belum dibuka. Akan dibuka pada ' .
                    $this->activeYear->announcement_datetime->translatedFormat('d F Y, H:i') . ' WIB.';
            } else {
                $this->errorMessage = 'Pengumuman belum dipublikasikan. Silakan coba lagi nanti.';
            }
            return;
        }

        $student = $service->publicSearch(trim($this->identifier), $tanggalLahir);

        if (!$student) {
            $this->errorMessage = 'Data tidak ditemukan. Pastikan NIS/NISN dan tanggal lahir Anda benar.';
            return;
        }

        $this->result = $student;
        $this->step   = 'result';
    }

    public function resetSearch(): void
    {
        $this->reset(['identifier', 'hari', 'bulan', 'tahun', 'result', 'errorMessage']);
        $this->step = 'form';
    }

    public function render()
    {
        return view('livewire.graduation-search', [
            'siteLogo' => Setting::get('search_logo'),
            'schoolName' => Setting::get('search_school_name', 'SMPIT AL-ITTIHAD'),
            'siteTitle' => Setting::get('search_title', 'Pengumuman Kelulusan'),
            'siteAnnouncement' => Setting::get('search_announcement', 'Selamat kepada seluruh siswa yang telah menyelesaikan perjuangan belajar di SMPIT AL-ITTIHAD. Tetap rendah hati, terus berprestasi, dan siap melangkah ke jenjang berikutnya.'),
        ]);
    }
}