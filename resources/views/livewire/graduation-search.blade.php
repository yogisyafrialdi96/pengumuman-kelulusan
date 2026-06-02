@php
    use Illuminate\Support\Facades\Storage;
    $months = [
        1=>'Januari', 2=>'Februari', 3=>'Maret',    4=>'April',
        5=>'Mei',     6=>'Juni',     7=>'Juli',      8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November', 12=>'Desember',
    ];
@endphp

{{-- ================================================================
     FORM STATE
     ================================================================ --}}
@if($step === 'form')
<div class="min-h-svh overflow-hidden bg-linear-to-br  flex items-center justify-center px-3 py-2 sm:px-4">

    <div class="w-full max-w-3xl">

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden max-h-[calc(100svh-1rem)]">

            {{-- Accent bar --}}
            <div class="h-1 bg-linear-to-r from-blue-600 via-indigo-500 to-blue-700"></div>

            <div class="px-6 sm:px-8 pt-6 sm:pt-7 pb-5 sm:pb-6">

                {{-- Logo + Identity --}}
                <div class="flex flex-col items-center text-center mb-5 sm:mb-6">
                    @if($siteLogo)
                        <img src="{{ Storage::url($siteLogo) }}" alt="Logo" class="h-15 sm:h-17 w-auto object-contain mb-3 sm:mb-4">
                    @else
                        <div class="w-15 h-15 sm:w-17 sm:h-17 rounded-2xl bg-linear-to-br from-blue-600 to-indigo-700 flex items-center justify-center mb-3 sm:mb-4 shadow-lg shadow-blue-600/30">
                            <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3zm0 2.18L20.35 9 12 13.82 3.65 9 12 5.18zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                            </svg>
                        </div>
                    @endif
                    <p class="text-[10px] sm:text-[10.5px] font-extrabold tracking-[0.18em] text-blue-600 uppercase mb-1">
                        {{ $schoolName }}
                    </p>
                    <h1 class="text-[20px] sm:text-[22px] font-bold text-gray-900 leading-snug">
                        {{ $siteTitle }}
                    </h1>
                    @if($activeYear)
                        <div class="mt-2 inline-flex items-center gap-1.5 bg-blue-50 border border-blue-100 text-blue-700 text-[11px] font-semibold px-3 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            Tahun Ajaran {{ $activeYear->name }}
                        </div>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 mb-4"></div>

                <form wire:submit.prevent="search">
                {{-- NIS / NISN --}}
                <div class="mb-3.5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">NIS / NISN</label>
                    <input
                        wire:model="identifier"
                        type="text"
                        placeholder="Masukkan NIS atau NISN"
                        autofocus
                        class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 outline-none transition text-sm text-gray-900 placeholder-gray-400"
                    />
                    @error('identifier')
                        <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Lahir — satu baris 3 kolom --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Lahir</label>
                    <div class="flex gap-2">
                        <input
                            wire:model="hari"
                            type="number"
                            min="1" max="31"
                            placeholder="DD"
                            class="w-14 px-1.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 outline-none transition text-center text-sm text-gray-900"
                        />
                        <select
                            wire:model="bulan"
                            class="flex-1 px-2.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 outline-none transition text-sm text-gray-900"
                        >
                            <option value="">Bulan</option>
                            @foreach($months as $num => $nama)
                                <option value="{{ $num }}" @selected((int)$bulan === $num)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <input
                            wire:model="tahun"
                            type="number"
                            min="1990"
                            max="{{ date('Y') }}"
                            placeholder="YYYY"
                            class="w-19 px-1.5 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/15 outline-none transition text-center text-sm text-gray-900"
                        />
                    </div>
                    <div class="mt-1 space-y-0.5">
                        @error('hari')  <p class="text-[11px] text-red-500">{{ $message }}</p> @enderror
                        @error('bulan') <p class="text-[11px] text-red-500">{{ $message }}</p> @enderror
                        @error('tahun') <p class="text-[11px] text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Error message --}}
                @if($errorMessage)
                    <div class="mb-4 flex items-start gap-2.5 p-3 rounded-lg bg-red-50 border border-red-100">
                        <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                        <p class="text-xs text-red-600 leading-relaxed">{{ $errorMessage }}</p>
                    </div>
                @endif

                {{-- Submit --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    class="w-full py-2.5 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:bg-blue-400 text-white font-semibold text-sm shadow-sm shadow-blue-600/20 transition-colors flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="search" class="inline-flex items-center gap-2">
                        Cek Hasil
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="search" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Sedang mencari...
                    </span>
                </button>
                </form>
            </div>

            {{-- Card footer --}}
            <div class="px-8 py-3.5 bg-slate-50 border-t border-gray-100">
                <p class="text-[11px] text-gray-400 text-center">
                    Data kelulusan bersifat rahasia dan hanya diperuntukkan bagi yang bersangkutan.
                </p>
            </div>
        </div>

        {{-- Copyright --}}
        <p class="text-center text-gray-400 text-[11px] mt-3">
            &copy; {{ date('Y') }} {{ $schoolName }} &mdash; Hak cipta dilindungi.
        </p>
    </div>
</div>


{{-- ================================================================
     RESULT STATE
     ================================================================ --}}
@elseif($step === 'result')
@php
    $status    = $result->status;
    $isLulus   = $status->value === 'lulus';
    $isTidakLulus = $status->value === 'tidak_lulus';
    $heroBg    = $isLulus
        ? 'from-emerald-600 via-green-700 to-green-900'
        : ($isTidakLulus
            ? 'from-red-600 via-rose-700 to-red-900'
            : 'from-amber-500 via-orange-600 to-amber-800');
    $accentBg  = $isLulus ? 'bg-green-500/20' : ($isTidakLulus ? 'bg-red-500/20' : 'bg-amber-400/20');
    $badgeBg   = $isLulus ? 'bg-green-400/20 text-green-100 border-green-400/30'
                          : ($isTidakLulus ? 'bg-red-400/20 text-red-100 border-red-400/30'
                                           : 'bg-amber-400/20 text-amber-100 border-amber-400/30');
@endphp

<div class="min-h-svh bg-gray-100 print:bg-white">

    {{-- Hero section --}}
    <div class="mx-3 sm:mx-4 mt-3 rounded-3xl bg-linear-to-br {{ $heroBg }} relative overflow-hidden pb-8 sm:pb-10 print:pb-8">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full {{ $accentBg }} blur-3xl"></div>
            <div class="absolute -bottom-10 -left-20 w-60 h-60 rounded-full {{ $accentBg }} blur-3xl"></div>
        </div>

        <div class="relative max-w-6xl mx-auto px-3 sm:px-4">
            {{-- Back button --}}
            <div class="pt-3 pb-4 print:hidden">
                {{-- <button wire:click="resetSearch"
                    class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-sm transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Cari Ulang
                </button> --}}
            </div>

            {{-- Status layout 1/5 + 4/5 --}}
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 sm:gap-6 items-center">
                <div class="sm:col-span-1 flex justify-center sm:justify-start">
                    @if($siteLogo)
                        <img src="{{ Storage::url($siteLogo) }}" alt="Logo" class="h-16 sm:h-24 w-auto object-contain drop-shadow-md">
                    @else
                        <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-2xl {{ $accentBg }} border border-white/20 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3zm0 2.18L20.35 9 12 13.82 3.65 9 12 5.18zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="sm:col-span-4 text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 border {{ $badgeBg }} rounded-full px-4 py-1.5 text-sm font-medium mb-3 backdrop-blur-sm">
                        {{ $status->label() }}
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2">
                        {{ $result->nama_siswa }}
                    </h1>

                    @if($isLulus)
                        <p class="text-white font-extrabold text-xl sm:text-2xl leading-tight tracking-tight pb-2">
                            Selamat! Anda dinyatakan lulus.
                        </p>
                        <p class="text-green-100/90 text-sm sm:text-base pb-2">
                            Tahun ajaran {{ $result->academicYear->name }}.
                        </p>
                    @elseif($isTidakLulus)
                        <p class="text-red-100/90 text-sm sm:text-base pb-2">
                            Anda belum dinyatakan lulus pada tahun ajaran {{ $result->academicYear->name }}.
                        </p>
                    @else
                        <p class="text-amber-100/90 text-sm sm:text-base pb-2">
                            Status kelulusan Anda pada tahun ajaran {{ $result->academicYear->name }} sedang diproses.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Data card (overlaps hero) --}}
    <div class="max-w-6xl mx-auto px-3 sm:px-4 -mt-10 sm:-mt-12 pb-4 sm:pb-6 print:mt-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-h-[calc(100svh-14rem)]">

            {{-- Header --}}
            <div class="px-5 sm:px-6 pt-4 pb-3 border-b border-gray-100">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-widest">Informasi Peserta</p>
            </div>

            {{-- Data grid --}}
            <div class="px-5 sm:px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">NIS</p>
                    <p class="text-gray-900 font-semibold font-mono">{{ $result->nis }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">NISN</p>
                    <p class="text-gray-900 font-semibold font-mono">{{ $result->nisn }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">Tempat, Tanggal Lahir</p>
                    <p class="text-gray-900 font-semibold">
                        {{ $result->tempat_lahir }},
                        {{ $result->tanggal_lahir->translatedFormat('d F Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">Nama Orang Tua / Wali</p>
                    <p class="text-gray-900 font-semibold">{{ $result->nama_orang_tua }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">Tahun Ajaran</p>
                    <p class="text-gray-900 font-semibold">{{ $result->academicYear->name }}</p>
                </div>
                @if($result->keterangan)
                    <div class="sm:col-span-2">
                        <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Keterangan</p>
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $result->keterangan }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Actions footer --}}
            <div class="px-5 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between print:hidden">
                <button wire:click="resetSearch"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Cari Siswa Lain
                </button>
                <button onclick="window.print()"
                    class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                    Cetak
                </button>
            </div>
        </div>

        @if($siteAnnouncement)
            <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50/95 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white">
                        <svg class="h-4 w-4" fill="none"3251233 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25h1.5v4.5h-1.5zm0-3h1.5v1.5h-1.5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">Pengumuman</p>
                        <p class="mt-1 text-sm sm:text-base leading-relaxed text-blue-950 whitespace-pre-line">
                            {{ $siteAnnouncement }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Timestamp --}}
        <p class="text-center text-gray-400 text-xs mt-3">
            Data diambil pada {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </p>
    </div>
</div>

@endif
