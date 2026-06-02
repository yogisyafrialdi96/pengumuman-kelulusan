<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Pengaturan Halaman')]
class SiteSettings extends Component
{
    use WithFileUploads;

    public string $schoolName = '';
    public string $searchTitle = '';
    public string $searchAnnouncement = '';
    public ?string $currentLogo = null;
    public mixed $logo = null;

    public bool $saved = false;

    public function mount(): void
    {
        $this->schoolName = (string) Setting::get('search_school_name', 'SMPIT AL-ITTIHAD');
        $this->searchTitle = (string) Setting::get('search_title', 'Pengumuman Kelulusan');
        $this->searchAnnouncement = (string) Setting::get('search_announcement', 'Selamat kepada seluruh siswa yang telah menyelesaikan perjuangan belajar di SMPIT AL-ITTIHAD. Tetap rendah hati, terus berprestasi, dan siap melangkah ke jenjang berikutnya.');
        $this->currentLogo = Setting::get('search_logo');
    }

    protected function rules(): array
    {
        return [
            'schoolName' => ['required', 'string', 'max:100'],
            'searchTitle' => ['required', 'string', 'max:100'],
            'searchAnnouncement' => ['required', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        Setting::set('search_school_name', $this->schoolName);
        Setting::set('search_title', $this->searchTitle);
        Setting::set('search_announcement', $this->searchAnnouncement);

        if ($this->logo) {
            $path = $this->logo->store('settings', 'public');
            Setting::set('search_logo', $path);
            $this->currentLogo = $path;
            $this->logo = null;
        }

        $this->saved = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.site-settings');
    }
}
