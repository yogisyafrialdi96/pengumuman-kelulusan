<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 mb-1">
                <a href="{{ route('admin.academic-years.index') }}" class="hover:text-zinc-700 dark:hover:text-zinc-300" wire:navigate>
                    Tahun Ajaran
                </a>
                <flux:icon.chevron-right class="size-3" />
                <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $academicYear->name }}</span>
            </div>
            <flux:heading level="1" class="!text-2xl">Data Siswa</flux:heading>
            <div class="flex items-center gap-2 mt-1">
                @if($academicYear->is_active)
                    <flux:badge color="blue" size="sm">Aktif</flux:badge>
                @endif
                @if($academicYear->is_published)
                    <flux:badge color="green" size="sm">Dipublikasikan</flux:badge>
                @else
                    <flux:badge color="zinc" size="sm">Draft</flux:badge>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap justify-end">
            {{-- Export Buttons --}}
            <flux:dropdown>
                <flux:button variant="ghost" icon-trailing="chevron-down" size="sm">
                    Export
                </flux:button>
                <flux:menu>
                    <flux:menu.item
                        :href="route('admin.export.excel', $academicYear)"
                        icon="table-cells"
                        target="_blank"
                    >
                        Export Excel (Semua)
                    </flux:menu.item>
                    <flux:menu.item
                        :href="route('admin.export.excel', [$academicYear, 'status' => 'lulus'])"
                        icon="table-cells"
                        target="_blank"
                    >
                        Export Excel (Lulus)
                    </flux:menu.item>
                    <flux:menu.item
                        :href="route('admin.export.pdf', $academicYear)"
                        icon="document-text"
                        target="_blank"
                    >
                        Export PDF
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:button
                :href="route('admin.students.import', $academicYear)"
                variant="ghost"
                icon="arrow-up-tray"
                size="sm"
                wire:navigate
            >
                Import
            </flux:button>

            <flux:button wire:click="openCreate" variant="primary" icon="plus" size="sm">
                Tambah Siswa
            </flux:button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif
    @if(session('warning'))
        <flux:callout variant="warning" icon="exclamation-triangle">
            {{ session('warning') }}
        </flux:callout>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <flux:card class="!p-3">
            <flux:text class="!text-xs text-zinc-500">Total</flux:text>
            <p class="text-xl font-bold text-zinc-800 dark:text-zinc-100">{{ $stats['total'] }}</p>
        </flux:card>
        <flux:card class="!p-3">
            <flux:text class="!text-xs text-green-600">Lulus</flux:text>
            <p class="text-xl font-bold text-green-600">{{ $stats['lulus'] }}</p>
        </flux:card>
        <flux:card class="!p-3">
            <flux:text class="!text-xs text-red-600">Tidak Lulus</flux:text>
            <p class="text-xl font-bold text-red-600">{{ $stats['tidak_lulus'] }}</p>
        </flux:card>
        <flux:card class="!p-3">
            <flux:text class="!text-xs text-yellow-600">Pending</flux:text>
            <p class="text-xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </flux:card>
    </div>

    {{-- Search & Filter --}}
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <flux:input
                wire:model.live.debounce.400ms="search"
                placeholder="Cari nama, NIS, NISN..."
                icon="magnifying-glass"
                clearable
            />
        </div>
        <div class="w-44">
            <flux:select wire:model.live="filterStatus" placeholder="Semua Status">
                <flux:select.option value="">Semua Status</flux:select.option>
                @foreach($statuses as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Bulk Actions --}}
    @if(!empty($selected))
        <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-950 rounded-xl border border-blue-200 dark:border-blue-800">
            <flux:text class="text-sm font-medium text-blue-700 dark:text-blue-300">
                {{ count($selected) }} siswa dipilih
            </flux:text>
            <div class="flex items-center gap-2 ml-auto">
                <flux:select wire:model="bulkStatus" placeholder="Ubah status..." class="w-44">
                    @foreach($statuses as $status)
                        <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:button
                    wire:click="bulkUpdateStatus"
                    variant="primary"
                    size="sm"
                    :disabled="empty($bulkStatus)"
                >
                    Terapkan
                </flux:button>
                <flux:button wire:click="$set('selected', [])" variant="ghost" size="sm">Batal</flux:button>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <flux:table>
            <flux:table.columns>
                    <flux:table.column class="w-10">
                        <input
                            type="checkbox"
                            wire:model.live="selectAll"
                            class="rounded border-zinc-300 dark:border-zinc-600"
                        />
                    </flux:table.column>
                    <flux:table.column sortable>Nama Siswa</flux:table.column>
                    <flux:table.column>NIS</flux:table.column>
                    <flux:table.column>NISN</flux:table.column>
                    <flux:table.column>Tgl Lahir</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($students as $student)
                    <flux:table.row wire:key="student-{{ $student->id }}">
                        <flux:table.cell>
                            <input
                                type="checkbox"
                                wire:model.live="selected"
                                value="{{ $student->id }}"
                                class="rounded border-zinc-300 dark:border-zinc-600"
                            />
                        </flux:table.cell>
                        <flux:table.cell class="font-medium">
                            {{ $student->nama_siswa }}
                            <p class="text-xs text-zinc-400 mt-0.5">{{ $student->nama_orang_tua }}</p>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-sm">{{ $student->nis }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-sm">{{ $student->nisn }}</flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ $student->tanggal_lahir->translatedFormat('d M Y') }}
                            <p class="text-xs text-zinc-400">{{ $student->tempat_lahir }}</p>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $student->status->color() }}" size="sm" icon="{{ $student->status->icon() }}">
                                {{ $student->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    wire:click="edit({{ $student->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    title="Edit"
                                />
                                <flux:button
                                    wire:click="confirmDelete({{ $student->id }})"
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
                        <flux:table.cell colspan="7" class="text-center py-12">
                            <flux:icon.users class="size-10 text-zinc-300 dark:text-zinc-600 mx-auto mb-2" />
                            <flux:text class="text-zinc-500">
                                @if($search || $filterStatus)
                                    Tidak ada siswa yang cocok dengan pencarian.
                                @else
                                    Belum ada data siswa. Tambah siswa atau import dari Excel.
                                @endif
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
        <div>{{ $students->links() }}</div>
    @endif

    {{-- Create Modal --}}
    <flux:modal wire:model="showCreateModal" class="md:w-xl">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">Tambah Siswa</flux:heading>
                <flux:text class="text-zinc-500 text-sm mt-1">Tambah data siswa baru ke tahun ajaran {{ $academicYear->name }}.</flux:text>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="nis" label="NIS" placeholder="Nomor Induk Siswa" :error="$errors->first('nis')" />
                <flux:input wire:model="nisn" label="NISN" placeholder="Nomor Induk Siswa Nasional" :error="$errors->first('nisn')" />
            </div>

            <flux:input wire:model="nama_siswa" label="Nama Siswa" placeholder="Nama lengkap siswa" :error="$errors->first('nama_siswa')" />
            <flux:input wire:model="nama_orang_tua" label="Nama Orang Tua" placeholder="Nama lengkap orang tua/wali" :error="$errors->first('nama_orang_tua')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="tempat_lahir" label="Tempat Lahir" placeholder="Kota" :error="$errors->first('tempat_lahir')" />
                <flux:input wire:model="tanggal_lahir" type="date" label="Tanggal Lahir" :error="$errors->first('tanggal_lahir')" />
            </div>

            <flux:select wire:model="status" label="Status Kelulusan" :error="$errors->first('status')">
                @foreach($statuses as $s)
                    <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="keterangan" label="Keterangan (Opsional)" rows="2" placeholder="Catatan tambahan..." :error="$errors->first('keterangan')" />

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">Batal</flux:button>
                <flux:button wire:click="create" variant="primary">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Edit Modal --}}
    <flux:modal wire:model="showEditModal" class="md:w-xl">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">Edit Data Siswa</flux:heading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="nis" label="NIS" :error="$errors->first('nis')" />
                <flux:input wire:model="nisn" label="NISN" :error="$errors->first('nisn')" />
            </div>

            <flux:input wire:model="nama_siswa" label="Nama Siswa" :error="$errors->first('nama_siswa')" />
            <flux:input wire:model="nama_orang_tua" label="Nama Orang Tua" :error="$errors->first('nama_orang_tua')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="tempat_lahir" label="Tempat Lahir" :error="$errors->first('tempat_lahir')" />
                <flux:input wire:model="tanggal_lahir" type="date" label="Tanggal Lahir" :error="$errors->first('tanggal_lahir')" />
            </div>

            <flux:select wire:model="status" label="Status Kelulusan" :error="$errors->first('status')">
                @foreach($statuses as $s)
                    <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="keterangan" label="Keterangan (Opsional)" rows="2" :error="$errors->first('keterangan')" />

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
                    <flux:heading level="2">Hapus Data Siswa?</flux:heading>
                    <flux:text class="text-zinc-500 text-sm mt-1">
                        Data siswa ini akan dihapus secara permanen.
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
