<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading level="1" class="!text-2xl">Kelola Tahun Ajaran</flux:heading>
            <flux:text class="text-zinc-500 mt-1">Buat dan kelola tahun ajaran kelulusan.</flux:text>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Tahun Ajaran Baru
        </flux:button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif
    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle">
            {{ session('error') }}
        </flux:callout>
    @endif

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <flux:table>
            <flux:table.columns>
                    <flux:table.column>Nama Tahun Ajaran</flux:table.column>
                    <flux:table.column>Total Siswa</flux:table.column>
                    <flux:table.column>Status Aktif</flux:table.column>
                    <flux:table.column>Status Publikasi</flux:table.column>
                    <flux:table.column>Jadwal Pengumuman</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($years as $year)
                    <flux:table.row wire:key="year-{{ $year->id }}">
                        <flux:table.cell class="font-medium">
                            {{ $year->name }}
                            @if($year->description)
                                <p class="text-xs text-zinc-400 mt-0.5">{{ Str::limit($year->description, 50) }}</p>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $year->students_count }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                wire:click="toggleActive({{ $year->id }})"
                                size="sm"
                                variant="{{ $year->is_active ? 'primary' : 'ghost' }}"
                            >
                                {{ $year->is_active ? 'Aktif' : 'Nonaktif' }}
                            </flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                wire:click="togglePublish({{ $year->id }})"
                                size="sm"
                                variant="{{ $year->is_published ? 'filled' : 'ghost' }}"
                            >
                                {{ $year->is_published ? 'Dipublikasikan' : 'Draft' }}
                            </flux:button>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($year->announcement_datetime)
                                <flux:text class="text-sm">
                                    {{ $year->announcement_datetime->translatedFormat('d M Y, H:i') }}
                                </flux:text>
                            @else
                                <flux:text class="text-sm text-zinc-400">—</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    :href="route('admin.students.index', $year)"
                                    size="sm"
                                    variant="ghost"
                                    icon="users"
                                    wire:navigate
                                    title="Kelola Siswa"
                                />
                                <flux:button
                                    wire:click="edit({{ $year->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    title="Edit"
                                />
                                <flux:button
                                    wire:click="confirmDelete({{ $year->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                    title="Hapus"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-12">
                            <flux:icon.calendar-days class="size-10 text-zinc-300 dark:text-zinc-600 mx-auto mb-2" />
                            <flux:text class="text-zinc-500">Belum ada tahun ajaran.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- Create Modal --}}
    <flux:modal wire:model="showCreateModal" class="md:w-96">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">Tambah Tahun Ajaran</flux:heading>
                <flux:text class="text-zinc-500 text-sm mt-1">Buat tahun ajaran kelulusan baru.</flux:text>
            </div>

            <flux:input
                wire:model="name"
                label="Nama Tahun Ajaran"
                placeholder="Contoh: 2024/2025"
                :error="$errors->first('name')"
            />

            <flux:input
                wire:model="announcement_datetime"
                type="datetime-local"
                label="Jadwal Pengumuman (Opsional)"
                description="Pengumuman otomatis terbuka pada waktu ini."
                :error="$errors->first('announcement_datetime')"
            />

            <flux:textarea
                wire:model="description"
                label="Deskripsi (Opsional)"
                rows="3"
                placeholder="Keterangan tambahan..."
                :error="$errors->first('description')"
            />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">Batal</flux:button>
                <flux:button wire:click="create" variant="primary">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Edit Modal --}}
    <flux:modal wire:model="showEditModal" class="md:w-96">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">Edit Tahun Ajaran</flux:heading>
            </div>

            <flux:input
                wire:model="name"
                label="Nama Tahun Ajaran"
                placeholder="Contoh: 2024/2025"
                :error="$errors->first('name')"
            />

            <flux:input
                wire:model="announcement_datetime"
                type="datetime-local"
                label="Jadwal Pengumuman (Opsional)"
                description="Pengumuman otomatis terbuka pada waktu ini."
                :error="$errors->first('announcement_datetime')"
            />

            <flux:textarea
                wire:model="description"
                label="Deskripsi (Opsional)"
                rows="3"
                placeholder="Keterangan tambahan..."
                :error="$errors->first('description')"
            />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showEditModal', false)" variant="ghost">Batal</flux:button>
                <flux:button wire:click="update" variant="primary">Perbarui</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" class="md:w-80">
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center shrink-0">
                    <flux:icon.trash class="size-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <flux:heading level="2">Hapus Tahun Ajaran?</flux:heading>
                    <flux:text class="text-zinc-500 text-sm mt-1">
                        Semua data siswa di tahun ajaran ini akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.
                    </flux:text>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showDeleteModal', false)" variant="ghost">Batal</flux:button>
                <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

</div>
