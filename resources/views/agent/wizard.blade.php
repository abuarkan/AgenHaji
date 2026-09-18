@extends('layouts.app')

@section('title', 'Pendaftaran Verifikasi')

@section('sidebar-nav')
    <a href="#" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg text-bpkh-navy bg-bpkh-navy/5 border border-bpkh-navy/15 cursor-default">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Pendaftaran & Verifikasi Agen</span>
    </a>
@endsection

@section('content')
<div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm mb-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-extrabold text-slate-800">{{ $agent->full_name ?? Auth::user()->name }}</h1>
                <span class="px-2 py-0.5 text-[10px] font-black rounded bg-slate-100 text-slate-500 border border-slate-200">Belum Terverifikasi</span>
            </div>
            <div class="flex items-center gap-3 mt-1 text-[11px] font-bold text-slate-400">
                <span>Role: Agen</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <span>Tingkat: {{ $agent->level->name ?? 'Silver' }}</span>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                <span class="text-xs font-bold text-slate-700">{{ Auth::user()->email }}</span>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor Handphone</span>
                <span class="text-xs font-bold text-slate-700">{{ $agent->whatsapp_number }}</span>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">NIK</span>
                <span class="text-xs font-bold text-slate-700">{{ $agent->nik }}</span>
            </div>
        </div>
    </div>


    @if($agent->rejection_reason)
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-start gap-3 shadow-sm relative animate-fade-in">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <span class="block font-bold text-sm">Verifikasi Berkas Ditolak</span>
                <span class="block text-xs mt-0.5 text-slate-650 font-medium">
                    Mohon perhatian: Pendaftaran berkas Anda sebelumnya ditolak/gagal verifikasi oleh Admin Haji.
                    <br><strong class="font-bold text-red-750">Alasan Penolakan:</strong> {{ $agent->rejection_reason }}
                    <br>Silakan periksa kembali data/dokumen Anda di bawah ini, perbaiki berkas yang tidak sesuai, dan kirimkan kembali untuk proses verifikasi ulang.
                </span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-start gap-3 shadow-sm relative animate-fade-in">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <span class="block font-bold text-sm">Gagal Mengirim Verifikasi</span>
                <ul class="list-disc list-inside text-xs mt-1 space-y-1 font-medium text-red-700/90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Alert / Status -->
    <div class="mt-5">
        <div class="flex items-start gap-3 p-4 bg-slate-100 border border-slate-200 text-slate-600 rounded-2xl shadow-sm text-xs font-semibold mb-6">
            <svg class="w-5 h-5 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                @if($agent->is_submitted)
                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                        <span>Pendaftaran Anda telah dikirim dan sedang menunggu verifikasi oleh administrator Institusi Anda. Silakan periksa kembali pratinjau dokumen Anda di bawah ini.</span>
                    @else
                        <span>Pendaftaran Anda telah dikirim dan sedang menunggu verifikasi oleh administrator BPKH. Silakan periksa kembali pratinjau dokumen Anda di bawah ini.</span>
                    @endif
                @else
                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                        <span>Anda belum terverifikasi sebagai Agen Haji. Lakukan verifikasi dengan mengunggah dokumen-dokumen yang diperlukan. Lengkapi form pada halaman ini agar dapat diproses oleh admin Institusi Anda.</span>
                    @else
                        <span>Anda belum terverifikasi sebagai Agen Haji. Lakukan verifikasi dengan mengunggah dokumen-dokumen yang diperlukan. Lengkapi form pada halaman ini agar dapat diproses oleh admin BPKH.</span>
                    @endif
                @endif
            </div>
        </div>

        <!-- Steps Wizard Navigator Bar -->
        <div class="flex items-center justify-center max-w-3xl mx-auto mb-8 relative">
            <div class="absolute left-0 right-0 top-1/2 h-[2px] bg-slate-200 -translate-y-1/2 z-0"></div>
            
            <div class="z-10 bg-white px-3 flex flex-col items-center gap-1">
                <div id="step-circle-1" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all bg-bpkh-navy text-white shadow">1</div>
                <span id="step-label-1" class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mt-1">Profil & Alamat</span>
            </div>
            
            <div class="flex-grow"></div>
            
            <div class="z-10 bg-white px-3 flex flex-col items-center gap-1">
                <div id="step-circle-2" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all bg-slate-200 text-slate-500">2</div>
                <span id="step-label-2" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Dokumen Wajib</span>
            </div>
            
            <div class="flex-grow"></div>
            
            <div class="z-10 bg-white px-3 flex flex-col items-center gap-1">
                <div id="step-circle-3" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all bg-slate-200 text-slate-500">3</div>
                <span id="step-label-3" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Pratinjau</span>
            </div>
        </div>

        <!-- Wizard Form -->
        <form id="wizardForm" action="{{ route('agent.verification.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude_tinggal" id="latitude_tinggal" value="-6.200000">
            <input type="hidden" name="longitude_tinggal" id="longitude_tinggal" value="106.816666">

            <!-- STEP 1 PANEL -->
            <div id="panel-step-1" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Column 1 -->
                <div class="space-y-4">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">KTP & Info Diri</span>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto KTP*</label>
                        <input type="file" name="foto_ktp" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png/.webp maksimal 3MB)</span>
                    </div>

                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nama Institusi</label>
                            <input type="text" readonly value="{{ $agent->institution->name ?? '-' }}" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-500 text-xs outline-none font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nomor Induk Pegawai (NIP)*</label>
                            <input type="text" name="nip" required value="{{ old('nip', $agent->nip) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-mono font-bold" placeholder="Masukkan NIP Anda">
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nama Lengkap*</label>
                        <input type="text" name="nama_lengkap" required value="{{ old('nama_lengkap', $agent->full_name ?? Auth::user()->name) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Nama lengkap sesuai KTP">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Jenis Kelamin*</label>
                            <select name="jenis_kelamin" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all font-bold">
                                <option value="Pria" {{ old('jenis_kelamin', $agent->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $agent->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Tempat Lahir*</label>
                            <input type="text" name="tempat_lahir" required value="{{ old('tempat_lahir', $agent->tempat_lahir) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Tempat lahir sesuai KTP">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Tanggal Lahir*</label>
                        <input type="date" name="tanggal_lahir" required value="{{ old('tanggal_lahir', $agent->birth_date ? \Carbon\Carbon::parse($agent->birth_date)->format('Y-m-d') : '') }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Alamat KTP*</label>
                        <input type="text" name="alamat_ktp" required value="{{ old('alamat_ktp', $agent->alamat_ktp) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Jalan, RT/RW sesuai KTP">
                    </div>

                    <div x-data="wilayahAutocomplete('ktp')">
                        <div class="relative">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Cari Desa / Kelurahan / Kota (Sesuai KTP)*</label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="wilayah_search"
                                    x-model="search"
                                    @input.debounce.300ms="fetchWilayah()"
                                    @keydown.escape="isOpen = false"
                                    @click.away="isOpen = false"
                                    placeholder="Ketik minimal 3 karakter (misal: Gambir)..."
                                    class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 focus:ring-1 focus:ring-bpkh-navy/20 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all pr-8 shadow-sm"
                                    autocomplete="off"
                                >
                                <div x-show="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2" style="display: none;">
                                    <svg class="animate-spin h-3.5 w-3.5 text-bpkh-navy" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <button type="button" x-show="search.length > 0 && !isLoading" @click="clearSelection()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-650 transition-colors" style="display: none;">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="isOpen && results.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-slate-150 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100" style="display: none;">
                                <template x-for="item in results" :key="item.kode_kelurahan">
                                    <button type="button" @click="selectItem(item)" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-medium transition-colors duration-150 flex items-center justify-between">
                                        <span x-text="item.label"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="isOpen && results.length === 0 && search.length >= 3 && !isLoading" class="absolute z-50 w-full mt-2 bg-white border border-slate-150 rounded-xl shadow-xl px-4 py-3 text-xs text-slate-400 text-center" style="display: none;">
                                Tidak ada data ditemukan untuk "<span class="font-semibold text-slate-650" x-text="search"></span>"
                            </div>
                        </div>

                        <!-- Hidden Inputs KTP -->
                        <input type="hidden" name="provinsi_ktp" :value="selected.nama_provinsi">
                        <input type="hidden" name="kota_ktp" :value="selected.nama_kota">
                        <input type="hidden" name="kecamatan_ktp" :value="selected.nama_kecamatan">
                        <input type="hidden" name="kelurahan_ktp" :value="selected.nama_kelurahan">
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="space-y-4 border-l border-slate-100 pl-0 lg:pl-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="block text-xs font-black text-slate-800 uppercase tracking-wider">Alamat Tinggal</span>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" id="same_address_check" class="rounded text-bpkh-navy border-slate-300 focus:ring-bpkh-navy/20">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Alamat domisili sama dengan KTP</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Alamat Domisili*</label>
                        <input type="text" name="alamat_tinggal" id="alamat_tinggal" required value="{{ old('alamat_tinggal', $agent->alamat_tinggal) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Alamat lengkap tinggal saat ini">
                    </div>

                    <div x-data="wilayahAutocomplete('tinggal')" @copy-ktp-to-tinggal.window="selected = { ...$event.detail.selected }; search = $event.detail.search; setTimeout(() => { geocodeAddress() }, 100);" @geocode-tinggal.window="geocodeAddress()">
                        <div class="relative">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Cari Desa / Kelurahan / Kota (Sesuai Domisili)*</label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="alamat_tinggal_search"
                                    x-model="search"
                                    @input.debounce.300ms="fetchWilayah()"
                                    @keydown.escape="isOpen = false"
                                    @click.away="isOpen = false"
                                    placeholder="Ketik minimal 3 karakter (misal: Gambir)..."
                                    class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 focus:ring-1 focus:ring-bpkh-navy/20 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all pr-8 shadow-sm"
                                    autocomplete="off"
                                >
                                <div x-show="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2" style="display: none;">
                                    <svg class="animate-spin h-3.5 w-3.5 text-bpkh-navy" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <button type="button" x-show="search.length > 0 && !isLoading" @click="clearSelection()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-650 transition-colors" style="display: none;">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="isOpen && results.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-slate-150 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100" style="display: none;">
                                <template x-for="item in results" :key="item.kode_kelurahan">
                                    <button type="button" @click="selectItem(item)" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs text-slate-700 font-medium transition-colors duration-150 flex items-center justify-between">
                                        <span x-text="item.label"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="isOpen && results.length === 0 && search.length >= 3 && !isLoading" class="absolute z-50 w-full mt-2 bg-white border border-slate-150 rounded-xl shadow-xl px-4 py-3 text-xs text-slate-400 text-center" style="display: none;">
                                Tidak ada data ditemukan untuk "<span class="font-semibold text-slate-650" x-text="search"></span>"
                            </div>
                        </div>

                        <!-- Hidden Inputs Domisili -->
                        <input type="hidden" name="provinsi_tinggal" id="provinsi_tinggal" :value="selected.nama_provinsi">
                        <input type="hidden" name="kota_tinggal" id="kota_tinggal" :value="selected.nama_kota">
                        <input type="hidden" name="kecamatan_tinggal" id="kecamatan_tinggal" :value="selected.nama_kecamatan">
                        <input type="hidden" name="kelurahan_tinggal" id="kelurahan_tinggal" :value="selected.nama_kelurahan">
                    </div>
                </div>

                <!-- Column 3 -->
                <div class="space-y-4 border-l border-slate-100 pl-0 lg:pl-6">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Dokumen Unggahan</span>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto Bangunan (Kantor/Lokasi)*</label>
                        <input type="file" name="foto_bangunan" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.jpeg/.jpg/.png/.webp maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto Diri*</label>
                        <input type="file" name="foto_diri" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.jpeg/.jpg/.png/.webp maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Pakta Integritas*</label>
                        <input type="file" name="foto_pakta_integritas" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png/.webp maksimal 3MB)</span>
                        <a href="#" class="text-[10px] font-bold text-bpkh-navy hover:underline block mt-1.5">Download Template Pakta Integritas</a>
                    </div>

                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto Bukti Pekerja*</label>
                        <input type="file" name="bukti_pekerja" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png/.webp maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">ID Card / SK Pengangkatan*</label>
                        <input type="file" name="sk_pengangkatan" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png/.webp maksimal 3MB)</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- STEP 2 PANEL -->
            <div id="panel-step-2" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bank Info -->
                <div class="space-y-4">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Buku Tabungan</span>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Buku Tabungan (Belakang Sampul)*</label>
                        <input type="file" name="foto_buku_tabungan" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(jpeg/jpg/png/webp/pdf maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nama Bank Syariah*</label>
                        <select name="nama_bank" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all font-semibold">
                            <option value="Bank Syariah Indonesia" {{ old('nama_bank', $agent->nama_bank) === 'Bank Syariah Indonesia' ? 'selected' : '' }}>Bank Syariah Indonesia</option>
                            <option value="Bank Muamalat" {{ old('nama_bank', $agent->nama_bank) === 'Bank Muamalat' ? 'selected' : '' }}>Bank Muamalat</option>
                            <option value="Bank Mega Syariah" {{ old('nama_bank', $agent->nama_bank) === 'Bank Mega Syariah' ? 'selected' : '' }}>Bank Mega Syariah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Cabang Bank*</label>
                        <input type="text" name="cabang_bank" required value="{{ old('cabang_bank', $agent->cabang_bank) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Masukkan Cabang Bank">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nomor Rekening*</label>
                        <input type="text" name="nomor_rekening" required value="{{ old('nomor_rekening', $agent->nomor_rekening) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-mono" placeholder="Masukkan nomor rekening">
                    </div>
                </div>

                <!-- NPWP Info -->
                <div class="space-y-4 border-l border-slate-100 pl-0 md:pl-6">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Nomor NPWP</span>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto NPWP*</label>
                        <input type="file" name="foto_npwp" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(jpeg/jpg/png/webp/pdf maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nomor NPWP*</label>
                        <input type="text" name="nomor_npwp" required value="{{ old('nomor_npwp', $agent->nomor_npwp) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-mono" placeholder="Masukkan nomor NPWP">
                    </div>
                </div>
            </div>

            <!-- STEP 3 PANEL (PREVIEW) -->
            <div id="panel-step-3" class="hidden space-y-6">
                <!-- Preview details grid -->
                <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Step 1 Details -->
                        <div class="space-y-4">
                            <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-1.5">Informasi Profile dan Alamat Lokasi</span>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">KTP File</span>
                                    <div id="preview-ktp-container"></div>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</span>
                                    <span id="preview-nama" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Lahir</span>
                                    <span id="preview-lahir" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Domisili</span>
                                    <span id="preview-alamat-domisili" class="font-bold text-slate-700">-</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Sesuai KTP</span>
                                    <span id="preview-alamat-ktp" class="font-semibold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto Bangunan (Kantor/Lokasi)</span>
                                    <div id="preview-bangunan-container"></div>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto Diri</span>
                                    <div id="preview-diri-container"></div>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pakta Integritas</span>
                                    <div id="preview-pakta-container"></div>
                                </div>
                                @if($agent->type === 'institution' && !$agent->is_institution_admin)
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto Bukti Pekerja</span>
                                    <div id="preview-bukti-pekerja-container"></div>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">ID Card / SK Pengangkatan</span>
                                    <div id="preview-sk-pengangkatan-container"></div>
                                </div>
                                @endif
                                @if($agent->type === 'institution' && !$agent->is_institution_admin)
                                <div class="col-span-2 mt-3 pt-3 border-t border-slate-150 grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Institusi</span>
                                        <span class="font-bold text-slate-700">{{ $agent->institution->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">NIP</span>
                                        <span id="preview-nip" class="font-bold text-slate-700 font-mono">{{ $agent->nip ?? '-' }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Step 2 Details -->
                        <div class="space-y-4 border-l border-slate-100 pl-0 lg:pl-6">
                            <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-1.5">Dokumen Wajib</span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Buku Tabungan</span>
                                    <div id="preview-tabungan-container"></div>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Bank Syariah</span>
                                    <span id="preview-bank" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Cabang Bank</span>
                                    <span id="preview-cabang" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor Rekening</span>
                                    <span id="preview-rekening" class="font-bold text-slate-700 font-mono">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto NPWP</span>
                                    <div id="preview-npwp-container"></div>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor NPWP</span>
                                    <span id="preview-npwp-num" class="font-bold text-slate-700 font-mono">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert -->
                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="text-amber-600">⚠</span>
                        <span>Mohon pastikan dokumen Anda sudah lengkap dan benar.</span>
                    </div>
                </div>
            </div>

            <!-- BUTTON CONTROLS -->
            <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-5">
                <button type="button" id="prevBtn" onclick="nextPrev(-1)" class="px-5 py-2 text-xs font-bold rounded-xl text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all cursor-pointer hidden">
                    Kembali
                </button>
                <div class="flex-grow"></div>
                
                @if(!$agent->is_submitted)
                    <button type="button" id="nextBtn" onclick="nextPrev(1)" class="px-6 py-2.5 text-xs font-bold rounded-xl text-white bg-bpkh-navy hover:bg-bpkh-navy/95 transition-all shadow cursor-pointer">
                        Lanjutkan
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- MODAL CONFIRMATION -->
<div id="confirmModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 hidden transition-opacity">
    <div class="bg-white border border-slate-200 p-6 rounded-3xl max-w-sm w-full mx-4 shadow-xl">
        <div class="w-12 h-12 rounded-full bg-bpkh-navy/10 flex items-center justify-center text-bpkh-navy mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-base font-extrabold text-slate-800">Verifikasi Agen Haji</h3>
        <p class="text-slate-500 text-xs font-medium mt-1.5">Apakah Anda yakin informasi dan dokumen yang Anda masukkan sudah benar?</p>
        <div class="flex items-center gap-3 mt-6">
            <button type="button" onclick="closeConfirmModal()" class="flex-1 py-2 text-xs font-bold rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="submitForm()" class="flex-1 py-2 text-xs font-bold rounded-xl text-white bg-bpkh-navy hover:bg-bpkh-navy/95 cursor-pointer">
                Ya, Kirim
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentTab = 0;
    const isSubmitted = {{ $agent->is_submitted ? 'true' : 'false' }};

    if (isSubmitted) {
        currentTab = 2;
    }

    showTab(currentTab);

    function showTab(n) {
        const tabs = ["panel-step-1", "panel-step-2", "panel-step-3"];
        document.getElementById(tabs[0]).classList.add("hidden");
        document.getElementById(tabs[1]).classList.add("hidden");
        document.getElementById(tabs[2]).classList.add("hidden");
        document.getElementById(tabs[n]).classList.remove("hidden");

        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        if (n === 0) {
            prevBtn.classList.add("hidden");
        } else {
            prevBtn.classList.remove("hidden");
        }

        if (nextBtn) {
            if (n === (tabs.length - 1)) {
                nextBtn.innerHTML = "Kirim Verifikasi";
                nextBtn.classList.remove("bg-bpkh-navy");
                nextBtn.classList.add("bg-bpkh-gold");
            } else {
                nextBtn.innerHTML = "Lanjutkan";
                nextBtn.classList.add("bg-bpkh-navy");
                nextBtn.classList.remove("bg-bpkh-gold");
            }
        }

        updateStepIndicators(n);

        if (n === 2) {
            generatePreview();
        }
    }

    function nextPrev(n) {
        const tabs = ["panel-step-1", "panel-step-2", "panel-step-3"];
        if (n === 1 && !validateForm()) return false;

        currentTab = currentTab + n;

        if (currentTab >= tabs.length) {
            openConfirmModal();
            currentTab = tabs.length - 1;
            return false;
        }

        showTab(currentTab);
    }

    function validateForm() {
        if (isSubmitted) return true;
        const tabPanels = ["panel-step-1", "panel-step-2", "panel-step-3"];
        const currentPanel = document.getElementById(tabPanels[currentTab]);
        const inputs = currentPanel.querySelectorAll("input[required], select[required]");
        let valid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add("border-red-450");
                valid = false;
            } else {
                input.classList.remove("border-red-455");
            }
        });

        if (!valid) {
            alert("Harap lengkapi semua kolom bertanda bintang (*).");
        }
        return valid;
    }

    function updateStepIndicators(n) {
        for (let i = 1; i <= 3; i++) {
            const circle = document.getElementById("step-circle-" + i);
            const label = document.getElementById("step-label-" + i);

            if (i <= n + 1) {
                circle.classList.add("bg-bpkh-navy", "text-white", "shadow");
                circle.classList.remove("bg-slate-200", "text-slate-500");
                label.classList.add("text-slate-700");
                label.classList.remove("text-slate-400");
            } else {
                circle.classList.remove("bg-bpkh-navy", "text-white", "shadow");
                circle.classList.add("bg-slate-200", "text-slate-500");
                label.classList.remove("text-slate-700");
                label.classList.add("text-slate-400");
            }
        }
    }

    function setupFilePreview(inputElement, previewContainerId, existingFileUrl, defaultIconSvg) {
        const container = document.getElementById(previewContainerId);
        if (!container) return;

        let file = inputElement && inputElement.files ? inputElement.files[0] : null;
        let fileUrl = existingFileUrl ? ('/' + existingFileUrl.replace(/^\/+/, '')) : '';
        let fileName = file ? file.name : (existingFileUrl ? existingFileUrl.substring(existingFileUrl.lastIndexOf('/') + 1) : null);
        let isImage = false;

        if (file) {
            fileUrl = URL.createObjectURL(file);
            isImage = file.type.startsWith('image/');
        } else if (existingFileUrl) {
            const ext = existingFileUrl.split('.').pop().toLowerCase();
            isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
        }

        if (fileName) {
            let mediaContent = '';
            if (isImage) {
                mediaContent = `<img src="${fileUrl}" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-sm" alt="Thumbnail">`;
            } else {
                mediaContent = `<div class="w-12 h-12 bg-slate-100 border border-slate-200 text-slate-500 rounded-lg flex items-center justify-center">${defaultIconSvg}</div>`;
            }

            container.innerHTML = `
                <a href="${fileUrl}" target="_blank" class="flex items-center gap-3 p-2 bg-slate-50 border border-slate-150 hover:bg-slate-100/50 rounded-xl transition-all group max-w-full">
                    ${mediaContent}
                    <div class="overflow-hidden flex-1">
                        <span class="block text-xs font-bold text-slate-700 truncate" title="${fileName}">${fileName}</span>
                        <span class="block text-[8px] text-bpkh-navy font-bold uppercase tracking-wider mt-0.5">Lihat Berkas</span>
                    </div>
                </a>
            `;
        } else {
            container.innerHTML = `
                <div class="flex items-center gap-3 p-2 bg-slate-50 border border-slate-150 rounded-xl max-w-full">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-400">Belum Diunggah</span>
                    </div>
                </div>
            `;
        }
    }

    function generatePreview() {
        const form = document.getElementById("wizardForm");
        
        document.getElementById("preview-nama").innerText = form.elements["nama_lengkap"].value;
        document.getElementById("preview-lahir").innerText = form.elements["tempat_lahir"].value + ", " + form.elements["tanggal_lahir"].value;
        
        const jKtp = [
            form.elements["alamat_ktp"].value,
            form.elements["kelurahan_ktp"].value,
            form.elements["kecamatan_ktp"].value,
            form.elements["kota_ktp"].value,
            form.elements["provinsi_ktp"].value
        ].filter(Boolean).join(", ");
        document.getElementById("preview-alamat-ktp").innerText = jKtp;

        const jDom = [
            form.elements["alamat_tinggal"].value,
            form.elements["kelurahan_tinggal"].value,
            form.elements["kecamatan_tinggal"].value,
            form.elements["kota_tinggal"].value,
            form.elements["provinsi_tinggal"].value
        ].filter(Boolean).join(", ");
        document.getElementById("preview-alamat-domisili").innerText = jDom;

        document.getElementById("preview-bank").innerText = form.elements["nama_bank"].value;
        document.getElementById("preview-cabang").innerText = form.elements["cabang_bank"].value;
        document.getElementById("preview-rekening").innerText = form.elements["nomor_rekening"].value;
        document.getElementById("preview-npwp-num").innerText = form.elements["nomor_npwp"].value;

        if (form.elements["nip"]) {
            document.getElementById("preview-nip").innerText = form.elements["nip"].value;
        }

        // Set up file previews dynamically
        setupFilePreview(form.elements["foto_ktp"], "preview-ktp-container", "{{ $agent->foto_ktp }}", 
            `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5L12 4L10 6z"></path></svg>`);
            
        setupFilePreview(form.elements["foto_bangunan"], "preview-bangunan-container", "{{ $agent->foto_bangunan }}", 
            `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>`);
            
        setupFilePreview(form.elements["foto_diri"], "preview-diri-container", "{{ $agent->foto_diri }}", 
            `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>`);
            
        setupFilePreview(form.elements["foto_pakta_integritas"], "preview-pakta-container", "{{ $agent->foto_pakta_integritas }}", 
            `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`);

        setupFilePreview(form.elements["foto_buku_tabungan"], "preview-tabungan-container", "{{ $agent->foto_buku_tabungan }}", 
            `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`);

        setupFilePreview(form.elements["foto_npwp"], "preview-npwp-container", "{{ $agent->foto_npwp }}", 
            `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`);

        if (form.elements["bukti_pekerja"]) {
            setupFilePreview(form.elements["bukti_pekerja"], "preview-bukti-pekerja-container", "{{ $agent->bukti_pekerja }}", 
                `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`);
        }
        
        if (form.elements["sk_pengangkatan"]) {
            setupFilePreview(form.elements["sk_pengangkatan"], "preview-sk-pengangkatan-container", "{{ $agent->sk_pengangkatan }}", 
                `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const fileInputs = document.querySelectorAll("input[type='file']");
        const maxSize = 3 * 1024 * 1024; // 3MB

        fileInputs.forEach(input => {
            input.addEventListener("change", function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    if (file.size > maxSize) {
                        alert(`Ukuran berkas "${file.name}" melebihi batas maksimal 3MB. Silakan pilih berkas yang lebih kecil.`);
                        this.value = ""; // Clear selection
                        this.classList.add("border-red-450");
                    } else {
                        this.classList.remove("border-red-450");
                    }
                }
            });
        });
    });

    document.getElementById("same_address_check").addEventListener("change", function(e) {
        if (e.target.checked) {
            const form = document.getElementById("wizardForm");
            form.elements["alamat_tinggal"].value = form.elements["alamat_ktp"].value;
            
            // Dispatch copy event to Alpine tinggal component
            if (window.lastKtpAddress) {
                window.dispatchEvent(new CustomEvent('copy-ktp-to-tinggal', {
                    detail: window.lastKtpAddress
                }));
            }

            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('geocode-tinggal'));
            }, 150);
        }
    });

    document.getElementById("alamat_tinggal").addEventListener("blur", function() {
        window.dispatchEvent(new CustomEvent('geocode-tinggal'));
    });

    // Alpine.js Autocomplete component function
    function wilayahAutocomplete(type) {
        let defaultSearch = '';
        let defaultSelected = {
            nama_provinsi: '',
            nama_kota: '',
            nama_kecamatan: '',
            nama_kelurahan: ''
        };

        if (type === 'ktp') {
            @if(old('kelurahan_ktp'))
                defaultSearch = '{{ old("kelurahan_ktp") . " (Kec. " . old("kecamatan_ktp") . ", " . old("kota_ktp") . ", " . old("provinsi_ktp") . ")" }}';
            @else
                defaultSearch = '{{ $agent->kelurahan_ktp ? ($agent->kelurahan_ktp . " (Kec. " . $agent->kecamatan_ktp . ", " . $agent->kota_ktp . ", " . $agent->provinsi_ktp . ")") : "" }}';
            @endif
            defaultSelected.nama_provinsi = '{{ old("provinsi_ktp", $agent->provinsi_ktp) }}';
            defaultSelected.nama_kota = '{{ old("kota_ktp", $agent->kota_ktp) }}';
            defaultSelected.nama_kecamatan = '{{ old("kecamatan_ktp", $agent->kecamatan_ktp) }}';
            defaultSelected.nama_kelurahan = '{{ old("kelurahan_ktp", $agent->kelurahan_ktp) }}';
            
            window.lastKtpAddress = {
                selected: { ...defaultSelected },
                search: defaultSearch
            };
        } else if (type === 'tinggal') {
            @if(old('kelurahan_tinggal'))
                defaultSearch = '{{ old("kelurahan_tinggal") . " (Kec. " . old("kecamatan_tinggal") . ", " . old("kota_tinggal") . ", " . old("provinsi_tinggal") . ")" }}';
            @else
                defaultSearch = '{{ $agent->kelurahan_tinggal ? ($agent->kelurahan_tinggal . " (Kec. " . $agent->kecamatan_tinggal . ", " . $agent->kota_tinggal . ", " . $agent->provinsi_tinggal . ")") : "" }}';
            @endif
            defaultSelected.nama_provinsi = '{{ old("provinsi_tinggal", $agent->provinsi_tinggal) }}';
            defaultSelected.nama_kota = '{{ old("kota_tinggal", $agent->kota_tinggal) }}';
            defaultSelected.nama_kecamatan = '{{ old("kecamatan_tinggal", $agent->kecamatan_tinggal) }}';
            defaultSelected.nama_kelurahan = '{{ old("kelurahan_tinggal", $agent->kelurahan_tinggal) }}';
        }

        return {
            search: defaultSearch,
            isLoading: false,
            isOpen: false,
            results: [],
            selected: defaultSelected,
            async fetchWilayah() {
                if (this.search.length < 3) {
                    this.results = [];
                    this.isOpen = false;
                    return;
                }

                this.isLoading = true;
                this.isOpen = true;

                try {
                    const response = await fetch(`/wilayah/search?q=${encodeURIComponent(this.search)}`);
                    if (response.ok) {
                        this.results = await response.json();
                    } else {
                        this.results = [];
                    }
                } catch (error) {
                    console.error('Error fetching wilayah data:', error);
                    this.results = [];
                } finally {
                    this.isLoading = false;
                }
            },
            selectItem(item) {
                this.search = item.label;
                this.isOpen = false;
                
                this.selected.nama_provinsi = item.nama_provinsi;
                this.selected.nama_kota = item.nama_kota;
                this.selected.nama_kecamatan = item.nama_kecamatan;
                this.selected.nama_kelurahan = item.nama_kelurahan;

                if (type === 'ktp') {
                    window.lastKtpAddress = {
                        selected: { ...this.selected },
                        search: this.search
                    };
                }

                if (type === 'tinggal') {
                    this.geocodeAddress();
                }
            },
            async geocodeAddress() {
                if (type !== 'tinggal') return;
                
                const alamatInput = document.getElementById("alamat_tinggal");
                if (!alamatInput) return;
                const alamat = alamatInput.value;
                if (!alamat || !this.selected.nama_kelurahan) return;
                
                const query = `${alamat}, ${this.selected.nama_kelurahan}, ${this.selected.nama_kecamatan}, ${this.selected.nama_kota}, ${this.selected.nama_provinsi}, Indonesia`;
                const fallbackQuery = `${this.selected.nama_kelurahan}, ${this.selected.nama_kecamatan}, ${this.selected.nama_kota}, ${this.selected.nama_provinsi}, Indonesia`;
                
                try {
                    let response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    let data = await response.json();
                    
                    if (!data || data.length === 0) {
                        response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(fallbackQuery)}&format=json&limit=1`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        data = await response.json();
                    }
                    
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        
                        document.getElementById("latitude_tinggal").value = lat.toFixed(6);
                        document.getElementById("longitude_tinggal").value = lon.toFixed(6);
                        
                        console.log(`Geocoded tinggal address: ${lat}, ${lon}`);
                    }
                } catch (error) {
                    console.error("Geocoding error:", error);
                }
            },
            clearSelection() {
                this.search = '';
                this.results = [];
                this.isOpen = false;
                
                for (let key in this.selected) {
                    this.selected[key] = '';
                }

                if (type === 'ktp') {
                    window.lastKtpAddress = {
                        selected: { ...this.selected },
                        search: this.search
                    };
                }
            }
        }
    }

    function openConfirmModal() {
        document.getElementById("confirmModal").classList.remove("hidden");
    }

    function closeConfirmModal() {
        document.getElementById("confirmModal").classList.add("hidden");
    }

    function submitForm() {
        closeConfirmModal();
        document.getElementById("wizardForm").submit();
    }
</script>
@endsection
