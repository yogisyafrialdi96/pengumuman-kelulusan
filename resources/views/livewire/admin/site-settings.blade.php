<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading level="1" class="text-2xl!">Pengaturan Halaman</flux:heading>
            <flux:text class="text-zinc-500 mt-1">Ubah logo, nama sekolah, dan judul halaman pencarian kelulusan.</flux:text>
        </div>
    </div>

    {{-- Success Banner --}}
    @if($saved)
        <flux:callout variant="success" icon="check-circle" x-data x-init="setTimeout(() => $wire.set('saved', false), 3000)">
            Pengaturan berhasil disimpan.
        </flux:callout>
    @endif

    <div class="max-w-2xl">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">

            {{-- Logo --}}
            <div>
                <flux:label>Logo Sekolah</flux:label>
                <flux:text class="text-zinc-500 text-sm mb-3">Format: JPG, PNG, atau SVG. Maks 2 MB.</flux:text>

                @if($currentLogo)
                    <div class="mb-3">
                        <img src="{{ Storage::url($currentLogo) }}"
                             alt="Logo saat ini"
                             class="h-20 w-auto rounded border border-zinc-200 dark:border-zinc-700 object-contain p-1">
                        <p class="text-xs text-zinc-400 mt-1">Logo saat ini</p>
                    </div>
                @endif

                <input type="file"
                       wire:model="logo"
                       accept="image/*"
                       class="block w-full text-sm text-zinc-700 dark:text-zinc-300
                              file:mr-3 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-medium
                              file:bg-zinc-100 file:text-zinc-700
                              dark:file:bg-zinc-800 dark:file:text-zinc-300
                              hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700
                              cursor-pointer">

                @error('logo')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                @if($logo)
                    <div class="mt-3">
                        <p class="text-xs text-zinc-400 mb-1">Pratinjau:</p>
                        <img src="{{ $logo->temporaryUrl() }}"
                             alt="Pratinjau logo baru"
                             class="h-20 w-auto rounded border border-zinc-200 dark:border-zinc-700 object-contain p-1">
                    </div>
                @endif
            </div>

            <flux:separator />

            {{-- School Name --}}
            <div>
                <flux:field>
                    <flux:label>Nama Sekolah</flux:label>
                    <flux:input wire:model="schoolName" placeholder="Contoh: SMPIT AL-ITTIHAD" />
                    <flux:error name="schoolName" />
                </flux:field>
            </div>

            {{-- Search Title --}}
            <div>
                <flux:field>
                    <flux:label>Judul Halaman Pencarian</flux:label>
                    <flux:input wire:model="searchTitle" placeholder="Contoh: Pengumuman Kelulusan" />
                    <flux:error name="searchTitle" />
                </flux:field>
            </div>

            {{-- Announcement --}}
            <div>
                <flux:field>
                    <flux:label>Pengumuman Kelulusan</flux:label>
                    <textarea
                        wire:model="searchAnnouncement"
                        rows="5"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                        placeholder="Tulis pesan pengumuman yang akan tampil di hasil pencarian."
                    ></textarea>
                    <flux:error name="searchAnnouncement" />
                </flux:field>
            </div>

            {{-- Save --}}
            <div class="flex justify-end">
                <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </flux:button>
            </div>

        </div>
    </div>

</div>
