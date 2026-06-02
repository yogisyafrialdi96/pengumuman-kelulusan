<div class="space-y-6">
    @if($activeYear)
        {{-- Stats Overview --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total --}}
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center shrink-0">
                        <flux:icon.users class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:text class="!text-xs text-zinc-500 dark:text-zinc-400">Total Siswa</flux:text>
                        <p class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </flux:card>

            {{-- Lulus --}}
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center shrink-0">
                        <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <flux:text class="!text-xs text-zinc-500 dark:text-zinc-400">Lulus</flux:text>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['lulus'] ?? 0 }}</p>
                    </div>
                </div>
            </flux:card>

            {{-- Tidak Lulus --}}
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900 flex items-center justify-center shrink-0">
                        <flux:icon.x-circle class="size-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <flux:text class="!text-xs text-zinc-500 dark:text-zinc-400">Tidak Lulus</flux:text>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['tidak_lulus'] ?? 0 }}</p>
                    </div>
                </div>
            </flux:card>

            {{-- Pending --}}
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center shrink-0">
                        <flux:icon.clock class="size-5 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div>
                        <flux:text class="!text-xs text-zinc-500 dark:text-zinc-400">Pending</flux:text>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Active Year Info --}}
        <flux:card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:text class="!text-xs text-zinc-500 dark:text-zinc-400 font-medium uppercase tracking-wide mb-1">Tahun Ajaran Aktif</flux:text>
                    <flux:heading level="3" class="!text-lg">{{ $activeYear->name }}</flux:heading>
                    @if($activeYear->description)
                        <flux:text class="text-zinc-500 mt-1 text-sm">{{ $activeYear->description }}</flux:text>
                    @endif
                    <div class="flex items-center gap-2 mt-3">
                        @if($activeYear->is_published)
                            <flux:badge color="green" size="sm">Dipublikasikan</flux:badge>
                        @elseif($activeYear->announcement_datetime)
                            <flux:badge color="yellow" size="sm">
                                Dijadwalkan: {{ $activeYear->announcement_datetime->translatedFormat('d M Y, H:i') }}
                            </flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">Belum dipublikasikan</flux:badge>
                        @endif
                    </div>
                </div>
                @if(($stats['total'] ?? 0) > 0)
                    <div class="text-right shrink-0">
                        <flux:text class="!text-xs text-zinc-500 dark:text-zinc-400">Persentase Lulus</flux:text>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['persentase_lulus'] ?? 0 }}%</p>
                    </div>
                @endif
            </div>

            <div class="mt-4 flex gap-2 flex-wrap">
                <flux:button
                    :href="route('admin.students.index', $activeYear)"
                    variant="primary"
                    size="sm"
                    wire:navigate
                >
                    Kelola Siswa
                </flux:button>
                <flux:button
                    :href="route('admin.academic-years.index')"
                    variant="ghost"
                    size="sm"
                    wire:navigate
                >
                    Semua Tahun Ajaran
                </flux:button>
            </div>
        </flux:card>
    @else
        <flux:card>
            <div class="text-center py-8">
                <flux:icon.academic-cap class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
                <flux:heading level="3" class="!text-lg">Belum Ada Tahun Ajaran Aktif</flux:heading>
                <flux:text class="text-zinc-500 mt-1">Buat tahun ajaran baru untuk mulai mengelola data siswa.</flux:text>
                <div class="mt-4">
                    <flux:button :href="route('admin.academic-years.index')" variant="primary" wire:navigate>
                        Kelola Tahun Ajaran
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @endif

    {{-- Recent Academic Years --}}
    @if($recentYears->isNotEmpty())
        <div>
            <flux:heading level="3" class="!text-base mb-3">Tahun Ajaran Terkini</flux:heading>
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <flux:table>
                    <flux:table.columns>
                            <flux:table.column>Nama</flux:table.column>
                            <flux:table.column>Total Siswa</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column></flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($recentYears as $year)
                            <flux:table.row>
                                <flux:table.cell class="font-medium">
                                    {{ $year->name }}
                                    @if($year->is_active)
                                        <flux:badge color="blue" size="sm" class="ml-1">Aktif</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>{{ $year->students_count }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($year->is_published)
                                        <flux:badge color="green" size="sm">Publik</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Draft</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:button
                                        :href="route('admin.students.index', $year)"
                                        variant="ghost"
                                        size="sm"
                                        wire:navigate
                                    >
                                        Lihat
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @endif
</div>
