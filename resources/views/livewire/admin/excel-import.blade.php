<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 max-w-2xl">

    {{-- Header --}}
    <div>
        <div class="flex items-center gap-2 text-sm text-zinc-500 mb-1">
            <a href="{{ route('admin.academic-years.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-300" wire:navigate>
                Tahun Ajaran
            </a>
            <flux:icon.chevron-right class="size-3" />
            <a href="{{ route('admin.students.index', $academicYear) }}" class="hover:text-zinc-700 dark:hover:text-zinc-300" wire:navigate>
                {{ $academicYear->name }}
            </a>
            <flux:icon.chevron-right class="size-3" />
            <span class="text-zinc-700 dark:text-zinc-300 font-medium">Import</span>
        </div>
        <flux:heading level="1" class="!text-2xl">Import Data Siswa</flux:heading>
        <flux:text class="text-zinc-500 mt-1">
            Import data siswa dari file Excel untuk tahun ajaran <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $academicYear->name }}</span>.
        </flux:text>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif
    @if(session('warning'))
        <flux:callout variant="warning" icon="exclamation-triangle">{{ session('warning') }}</flux:callout>
    @endif
    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle">{{ session('error') }}</flux:callout>
    @endif

    {{-- Template Download --}}
    <flux:card>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center shrink-0">
                <flux:icon.document-arrow-down class="size-5 text-green-600 dark:text-green-400" />
            </div>
            <div class="flex-1">
                <flux:heading level="3" class="!text-sm">Template Excel</flux:heading>
                <flux:text class="!text-xs text-zinc-500">
                    Gunakan template ini untuk memastikan format data sesuai.
                </flux:text>
            </div>
            <flux:button
                :href="route('admin.export.template')"
                variant="ghost"
                size="sm"
                icon="arrow-down-tray"
                target="_blank"
            >
                Download Template
            </flux:button>
        </div>
    </flux:card>

    {{-- Upload Form --}}
    @if(!$imported)
        <flux:card>
            <div class="space-y-5">
                <flux:heading level="3" class="!text-base">Upload File</flux:heading>

                <div
                    wire:ignore
                    x-data="{
                        dragging: false,
                        handleDrop(e) {
                            this.dragging = false;
                            const files = e.dataTransfer.files;
                            if (files.length) {
                                @this.upload('file', files[0], () => {}, () => {}, (event) => {});
                            }
                        }
                    }"
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="handleDrop($event)"
                    :class="dragging ? 'border-blue-400 bg-blue-50 dark:bg-blue-950' : 'border-zinc-300 dark:border-zinc-600'"
                    class="border-2 border-dashed rounded-xl p-8 text-center transition-colors"
                >
                    <flux:icon.cloud-arrow-up class="size-10 text-zinc-400 mx-auto mb-3" />
                    <flux:text class="text-sm font-medium">Drag & drop file di sini, atau</flux:text>
                    <label class="cursor-pointer">
                        <input
                            type="file"
                            wire:model="file"
                            accept=".xlsx,.xls,.csv"
                            class="sr-only"
                        />
                        <span class="text-sm text-blue-600 dark:text-blue-400 hover:underline">pilih file</span>
                    </label>
                    <flux:text class="!text-xs text-zinc-400 mt-1">Format: .xlsx, .xls, .csv — Maks 5MB</flux:text>

                    @if($file)
                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-sm">
                            <flux:icon.document class="size-4 text-zinc-500" />
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $file->getClientOriginalName() }}</span>
                            <span class="text-zinc-400">({{ round($file->getSize() / 1024, 1) }} KB)</span>
                        </div>
                    @endif
                </div>

                @error('file')
                    <flux:callout variant="danger" icon="x-circle" class="!py-2">{{ $message }}</flux:callout>
                @enderror

                <div class="flex justify-end gap-2">
                    <flux:button
                        :href="route('admin.students.index', $academicYear)"
                        variant="ghost"
                        wire:navigate
                    >
                        Batal
                    </flux:button>
                    <flux:button
                        wire:click="import"
                        wire:loading.attr="disabled"
                        variant="primary"
                        :disabled="!$file || $importing"
                    >
                        <span wire:loading.remove wire:target="import">Import Sekarang</span>
                        <span wire:loading wire:target="import">Memproses...</span>
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @endif

    {{-- Import Results --}}
    @if($imported)
        <flux:card>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    @if(empty($importErrors))
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <flux:icon.check-circle class="size-5 text-green-600" />
                        </div>
                        <div>
                            <flux:heading level="3" class="!text-base text-green-700 dark:text-green-300">Import Berhasil!</flux:heading>
                            <flux:text class="!text-sm text-zinc-500">Total {{ $successCount }} siswa berhasil diimport.</flux:text>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                            <flux:icon.exclamation-triangle class="size-5 text-yellow-600" />
                        </div>
                        <div>
                            <flux:heading level="3" class="!text-base text-yellow-700 dark:text-yellow-300">Import Selesai dengan Error</flux:heading>
                            <flux:text class="!text-sm text-zinc-500">{{ count($importErrors) }} baris gagal diimport.</flux:text>
                        </div>
                    @endif
                </div>

                @if(!empty($importErrors))
                    <div class="overflow-hidden rounded-lg border border-red-200 dark:border-red-800">
                        <table class="w-full text-sm">
                            <thead class="bg-red-50 dark:bg-red-950">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300">Baris</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300">Kolom</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300">Pesan Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-100 dark:divide-red-900">
                                @foreach($importErrors as $error)
                                    <tr>
                                        <td class="px-3 py-2 text-red-600 dark:text-red-400 font-mono">{{ $error['row'] }}</td>
                                        <td class="px-3 py-2 text-zinc-600 dark:text-zinc-400">{{ $error['attribute'] }}</td>
                                        <td class="px-3 py-2 text-zinc-600 dark:text-zinc-400">{{ implode(', ', $error['errors']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="flex gap-2">
                    <flux:button wire:click="resetImport" variant="ghost" size="sm">
                        Import Lagi
                    </flux:button>
                    <flux:button
                        :href="route('admin.students.index', $academicYear)"
                        variant="primary"
                        size="sm"
                        wire:navigate
                    >
                        Lihat Data Siswa
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @endif

    {{-- Instructions --}}
    <flux:card class="bg-blue-50 dark:bg-blue-950 border-blue-200 dark:border-blue-800">
        <flux:heading level="3" class="!text-sm text-blue-700 dark:text-blue-300 mb-2">
            Petunjuk Format File
        </flux:heading>
        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1 list-disc list-inside">
            <li>Kolom wajib: <strong>nis, nisn, nama_siswa, nama_orang_tua, tempat_lahir, tanggal_lahir</strong></li>
            <li>Kolom opsional: <strong>status</strong> (lulus / tidak_lulus / pending — default: pending), <strong>keterangan</strong></li>
            <li>Format tanggal: <strong>DD/MM/YYYY</strong> atau <strong>YYYY-MM-DD</strong></li>
            <li>NIS dan NISN harus unik dalam satu tahun ajaran</li>
        </ul>
    </flux:card>

</div>
