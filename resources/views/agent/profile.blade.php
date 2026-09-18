@extends('layouts.app')

@section('title', 'Profil Agen Haji')

@section('sidebar-nav')
    @if(Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin_haji')
        <a href="{{ route('superadmin.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    @endif
@endsection

@section('content')
@php
    $getFileUrl = function($path) {
        if (!$path) return null;
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        $cleanPath = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'uploads/')) {
            return asset($cleanPath);
        }
        return asset('storage/' . $cleanPath);
    };

    $hasFile = function($path) {
        if (!$path) return false;
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return true;
        }
        $cleanPath = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'storage/')) {
            $relativePath = substr($cleanPath, 8);
            return file_exists(public_path('storage/' . $relativePath)) || file_exists(storage_path('app/public/' . $relativePath));
        }
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'uploads/')) {
            return file_exists(public_path($cleanPath));
        }
        return file_exists(public_path($cleanPath)) || file_exists(storage_path('app/public/' . $cleanPath));
    };

    $isImage = function($path) {
        if (!$path) return false;
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
    };
@endphp

    <div class="mb-4">
        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Profil Anda</h2>
    </div>

    @if((Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin_haji') && ($agent->status !== 'active' || !$agent->is_ktp_verified))
        <!-- Admin Verification & Validation Header Banner -->
        <div class="mb-6 p-5 rounded-3xl bg-slate-900 border border-slate-800 text-white shadow-xl flex flex-col gap-4" style="background-color: #0f172a !important; color: #ffffff !important;">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-black uppercase tracking-wider mb-2">Panel Verifikasi & Validasi Admin</span>
                    <h3 class="text-base font-black text-white" style="color: #ffffff !important;">Verifikasi Kelayakan Berkas Pendaftaran Agen</h3>
                    <p class="text-xs text-slate-300 font-medium mt-1 max-w-xl" style="color: #cbd5e1 !important;">
                        Periksa kelengkapan foto KTP, alamat domisili, foto lokasi/bangunan, dan dokumen wajib syariah sebelum memberikan persetujuan pendaftaran.
                    </p>
                    @if($agent->rejection_reason)
                        <div class="mt-2 text-xs text-red-300 bg-red-950/60 p-2.5 rounded-xl border border-red-800/60">
                            <strong>Catatan Penolakan Sebelumnya:</strong> "{{ $agent->rejection_reason }}"
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <!-- Tombol Verifikasi OK -->
                    <form action="{{ route('superadmin.agents.status', $agent->id) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl text-xs transition-all shadow-lg flex items-center gap-2 cursor-pointer border border-emerald-400">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Verifikasi OK (Setuju & Aktifkan)</span>
                        </button>
                    </form>

                    <!-- Tombol Tolak Dengan Catatan -->
                    <button type="button" onclick="toggleInlineReject()" class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-black rounded-xl text-xs transition-all shadow-lg flex items-center gap-2 cursor-pointer border border-red-400">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Tolak Dengan Catatan</span>
                    </button>
                </div>
            </div>

            <!-- Inline Form Tolak Catatan -->
            <div id="inline-reject-box" class="hidden pt-4 border-t border-slate-800">
                <form action="{{ route('superadmin.agents.reject', $agent->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Catatan / Alasan Penolakan untuk Agen <span class="text-red-400">*</span></label>
                        <textarea name="rejection_reason" required rows="3" placeholder="Tuliskan catatan perbaikan (misal: Foto KTP buram, mohon diunggah ulang)..." class="w-full bg-slate-950 border border-slate-700 text-white placeholder-slate-500 rounded-xl p-3 text-xs outline-none focus:border-red-500 transition-all"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="toggleInlineReject()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-bold cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg text-xs transition-all shadow-md cursor-pointer">Kirim Catatan Penolakan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Agent Main Profile Header Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-bpkh-navy text-white rounded-2xl flex items-center justify-center font-black text-xl shadow-sm">
                    {{ substr($agent->full_name ?? $agent->user->name, 0, 2) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-lg font-black text-slate-800">{{ $agent->full_name ?? $agent->user->name }}</h3>
                        @if($agent->status === 'suspended')
                            <span class="px-2.5 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold uppercase tracking-wider">
                                Ditangguhkan
                            </span>
                        @elseif($agent->is_ktp_verified)
                            <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase tracking-wider">
                                Terverifikasi
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-150 text-[10px] font-bold uppercase tracking-wider">
                                Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                    <div class="flex gap-1.5 text-slate-400 text-[10px] font-bold uppercase tracking-wide mt-1.5">
                        <span>Role: Agen</span>
                        <span>•</span>
                        <span>{{ $agent->is_ktp_verified ? 'Terverifikasi' : 'Belum Terverifikasi' }}</span>
                        <span>•</span>
                        <span>Jenis Agen: {{ $agent->type === 'institution' ? 'Institusi (B2B)' : 'Freelance' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Credentials Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pt-6">
            <!-- Referral Code -->
            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3 relative group">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kode Referral</span>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-slate-700" id="refCode">{{ $agent->referral_code }}</span>
                    <button onclick="copyReferral()" class="text-slate-400 hover:text-bpkh-navy cursor-pointer transition-colors" title="Copy Kode Referral">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-3 8h3m-3 3h3m-9-4h.01M9 16h.01"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Email -->
            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</span>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-xs font-semibold text-slate-700 truncate" title="{{ $agent->user->email }}">{{ $agent->user->email }}</span>
                </div>
            </div>

            <!-- Phone -->
            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Handphone</span>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="text-xs font-mono font-semibold text-slate-700">+{{ $agent->whatsapp_number }}</span>
                </div>
            </div>

            <!-- NIK -->
            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIK</span>
                <div class="flex items-center gap-1.5 font-mono">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    <span class="text-xs font-semibold text-slate-700">{{ $agent->nik }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs (Profile Pengguna / Ringkasan) -->
    <div class="flex gap-2 border-b border-slate-200 mb-6">
        <button onclick="switchProfileTab('info')" id="tab-btn-info" class="flex items-center gap-2 px-4 py-2 text-xs font-bold border-b-2 border-bpkh-navy text-bpkh-navy cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Profile Pengguna</span>
        </button>
        <button onclick="switchProfileTab('summary')" id="tab-btn-summary" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-750 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Ringkasan</span>
        </button>
    </div>

    <!-- Alert Verification Banner -->
    @if($agent->is_ktp_verified)
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start justify-between gap-3 shadow-sm relative" id="alertVerif">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="block font-bold text-xs uppercase tracking-wider">Selamat! Anda telah terverifikasi sebagai Agen Haji.</span>
                <span class="block text-[10px] mt-1 text-slate-500 font-medium">
                    Verifikasi berhasil dikonfirmasi berdasarkan pencocokan data: 
                    <strong class="text-emerald-700">1. Email Valid</strong>, 
                    <strong class="text-emerald-700">2. Nomor WhatsApp Terkoneksi</strong>, dan 
                    <strong class="text-emerald-700">3. Validasi KTP via Dukcapil Hub</strong>.
                </span>
            </div>
        </div>
        <button onclick="document.getElementById('alertVerif').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @else
    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start justify-between gap-3 shadow-sm relative" id="alertVerif">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <span class="block font-bold text-xs uppercase tracking-wider">Menunggu Verifikasi Berkas</span>
                <span class="block text-[10px] mt-1 text-slate-500 font-medium">
                    Berkas pendaftaran Anda telah berhasil dikirim dan saat ini sedang menunggu tinjauan dan persetujuan oleh administrator.
                </span>
            </div>
        </div>
        <button onclick="document.getElementById('alertVerif').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    <!-- Tab Content 1: Profile Pengguna -->
    <div id="tab-profile-info" class="space-y-6">
        <!-- Section 1: Informasi Profile dan Alamat Lokasi -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-6 pb-2 border-b border-slate-100">
                Informasi Profile dan Alamat Lokasi
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Column 1: KTP Details -->
                <div>
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">KTP</span>
                    @if($agent->foto_ktp)
                        @php
                            $isImg = $isImage($agent->foto_ktp);
                            $url = $getFileUrl($agent->foto_ktp);
                        @endphp
                        <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group mb-4">
                            <div class="h-32 flex items-center justify-center bg-slate-100 overflow-hidden relative">
                                @if($isImg)
                                    <img src="{{ $url }}" class="w-full h-full group-hover:scale-105 transition-transform duration-300" style="object-fit: contain; object-position: center;">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <svg class="w-8 h-8 mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-[9px] font-bold truncate max-w-[150px]">{{ basename($agent->foto_ktp) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ $url }}" target="_blank" class="text-[10px] font-bold text-slate-700 truncate flex-1 mr-2 hover:text-bpkh-navy" title="{{ basename($agent->foto_ktp) }}">
                                    {{ basename($agent->foto_ktp) }}
                                </a>
                                <span class="text-[9px] text-emerald-600 font-bold shrink-0">Lihat Berkas</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2.5 p-3 bg-slate-50 border border-slate-150 rounded-xl mb-4">
                            <div class="w-10 h-8 bg-slate-100 text-slate-400 flex items-center justify-center rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="overflow-hidden">
                                <span class="block text-xs font-bold text-slate-450">Belum Diunggah</span>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3.5">
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->full_name ?? $agent->user->name }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lahir</span>
                            <span class="text-xs font-bold text-slate-800">
                                {{ $agent->birth_date ? \Carbon\Carbon::parse($agent->birth_date)->format('d/m/Y') : 'Belum Diisi' }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Alamat KTP</span>
                            <span class="text-xs font-bold text-slate-800 block leading-relaxed">{{ $agent->alamat_ktp ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Provinsi</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->provinsi_ktp ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kota/Kabupaten</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->kota_ktp ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kecamatan</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->kecamatan_ktp ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Desa/Kelurahan</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->kelurahan_ktp ?? 'Belum Diisi' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Domisili/Tinggal Details -->
                <div>
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Domisili</span>
                    <div class="p-3 bg-blue-50/50 border border-blue-100 rounded-xl mb-4 flex items-center justify-between">
                        <div>
                            <span class="block text-[9px] font-bold text-blue-500 uppercase tracking-wider">Koordinat Tinggal</span>
                            <span class="text-xs font-mono font-bold text-slate-700">
                                @if($agent->latitude_tinggal && $agent->longitude_tinggal)
                                    {{ $agent->latitude_tinggal }}, {{ $agent->longitude_tinggal }}
                                @else
                                    Belum Diisi
                                @endif
                            </span>
                        </div>
                        @if($agent->latitude_tinggal && $agent->longitude_tinggal)
                            <a href="https://maps.google.com/?q={{ $agent->latitude_tinggal }},{{ $agent->longitude_tinggal }}" 
                               target="_blank" 
                               class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg cursor-pointer transition-colors"
                               title="Lihat Peta Google">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->full_name ?? $agent->user->name }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Alamat Domisili</span>
                            <span class="text-xs font-bold text-slate-800 block leading-relaxed">{{ $agent->alamat_tinggal ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Provinsi</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->provinsi_tinggal ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kota/Kabupaten</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->kota_tinggal ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kecamatan</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->kecamatan_tinggal ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Desa/Kelurahan</span>
                            <span class="text-xs font-bold text-slate-800">{{ $agent->kelurahan_tinggal ?? 'Belum Diisi' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Documentation Photos -->
                <div class="space-y-6">
                    <div>
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Foto Bangunan (Kantor/Lokasi)</span>
                        <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                            <div class="h-32 flex items-center justify-center bg-slate-100 overflow-hidden">
                                @if($agent->foto_bangunan && $hasFile($agent->foto_bangunan))
                                    <img src="{{ $getFileUrl($agent->foto_bangunan) }}" class="w-full h-full" style="object-fit: cover; object-position: center;">
                                @elseif($agent->foto_bangunan)
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-[9px] font-bold">{{ basename($agent->foto_bangunan) }}</span>
                                    </div>
                                @else
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between">
                                @if($agent->foto_bangunan)
                                    <a href="{{ $getFileUrl($agent->foto_bangunan) }}" target="_blank" class="text-[10px] font-bold text-slate-700 truncate hover:text-bpkh-navy flex-1 mr-2" title="{{ basename($agent->foto_bangunan) }}">
                                        {{ basename($agent->foto_bangunan) }}
                                    </a>
                                    <span class="text-[9px] text-emerald-600 font-bold shrink-0">Lihat Foto</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400">Belum Diunggah</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Foto Diri</span>
                        <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                            <div class="h-32 flex items-center justify-center bg-slate-100 overflow-hidden">
                                @if($agent->foto_diri && $hasFile($agent->foto_diri))
                                    <img src="{{ $getFileUrl($agent->foto_diri) }}" class="w-full h-full" style="object-fit: cover; object-position: center;">
                                @elseif($agent->foto_diri)
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-[9px] font-bold">{{ basename($agent->foto_diri) }}</span>
                                    </div>
                                @else
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between">
                                @if($agent->foto_diri)
                                    <a href="{{ $getFileUrl($agent->foto_diri) }}" target="_blank" class="text-[10px] font-bold text-slate-700 truncate hover:text-bpkh-navy flex-1 mr-2" title="{{ basename($agent->foto_diri) }}">
                                        {{ basename($agent->foto_diri) }}
                                    </a>
                                    <span class="text-[9px] text-emerald-600 font-bold shrink-0">Lihat Foto</span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400">Belum Diunggah</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Dokumen Wajib -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-6 pb-2 border-b border-slate-100">
                Dokumen Wajib Syariah & Administrasi
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Column 1: Buku Tabungan -->
                <div class="border border-slate-150 rounded-2xl p-5 bg-slate-50/50">
                    <span class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-4">Buku Tabungan Rekening Komisi</span>
                    
                    @if($agent->foto_buku_tabungan)
                        @php
                            $isImg = $isImage($agent->foto_buku_tabungan);
                            $url = $getFileUrl($agent->foto_buku_tabungan);
                        @endphp
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50 relative group mb-4">
                            <div class="h-24 flex items-center justify-center bg-slate-100 overflow-hidden relative">
                                @if($isImg)
                                    <img src="{{ $url }}" class="w-full h-full group-hover:scale-105 transition-transform duration-300" style="object-fit: contain; object-position: center;">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <svg class="w-6 h-6 mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-[8px] font-bold truncate max-w-[120px]">{{ basename($agent->foto_buku_tabungan) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ $url }}" target="_blank" class="text-[9px] font-bold text-slate-700 truncate flex-1 mr-2 hover:text-bpkh-navy" title="{{ basename($agent->foto_buku_tabungan) }}">
                                    {{ basename($agent->foto_buku_tabungan) }}
                                </a>
                                <span class="text-[8px] text-amber-600 font-bold shrink-0">Lihat Berkas</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-white border border-slate-150 rounded-xl mb-5">
                            <div class="w-12 h-10 bg-slate-100 text-slate-400 flex items-center justify-center rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="overflow-hidden">
                                <span class="block text-xs font-bold text-slate-400">Belum Diunggah</span>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nama Bank Syariah</span>
                            <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $agent->nama_bank ?? 'Belum Diisi' }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Cabang Bank</span>
                            <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $agent->cabang_bank ?? 'Belum Diisi' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor Rekening</span>
                            <span class="text-xs font-mono font-bold text-bpkh-navy block mt-0.5 tracking-wider">{{ $agent->nomor_rekening ?? 'Belum Diisi' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: NPWP -->
                <div class="border border-slate-150 rounded-2xl p-5 bg-slate-50/50">
                    <span class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-4">NPWP (Nomor Pokok Wajib Pajak)</span>
                    
                    @if($agent->foto_npwp)
                        @php
                            $isImg = $isImage($agent->foto_npwp);
                            $url = $getFileUrl($agent->foto_npwp);
                        @endphp
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50 relative group mb-4">
                            <div class="h-24 flex items-center justify-center bg-slate-100 overflow-hidden relative">
                                @if($isImg)
                                    <img src="{{ $url }}" class="w-full h-full group-hover:scale-105 transition-transform duration-300" style="object-fit: contain; object-position: center;">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <svg class="w-6 h-6 mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-[8px] font-bold truncate max-w-[120px]">{{ basename($agent->foto_npwp) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ $url }}" target="_blank" class="text-[9px] font-bold text-slate-700 truncate flex-1 mr-2 hover:text-bpkh-navy" title="{{ basename($agent->foto_npwp) }}">
                                    {{ basename($agent->foto_npwp) }}
                                </a>
                                <span class="text-[8px] text-blue-600 font-bold shrink-0">Lihat Berkas</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-3 bg-white border border-slate-150 rounded-xl mb-5">
                            <div class="w-12 h-10 bg-slate-100 text-slate-400 flex items-center justify-center rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="overflow-hidden">
                                <span class="block text-xs font-bold text-slate-400">Belum Diunggah</span>
                            </div>
                        </div>
                    @endif

                    <div>
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor NPWP</span>
                        <span class="text-xs font-mono font-bold text-slate-800 block mt-0.5 tracking-wider">{{ $agent->nomor_npwp ?? 'Belum Diisi' }}</span>
                    </div>
                </div>

                <!-- Extra B2B / Freelance Documents -->
                @if($agent->foto_pakta_integritas || $agent->bukti_pekerja || $agent->sk_pengangkatan)
                <div class="col-span-1 md:col-span-2 border border-slate-150 rounded-2xl p-5 bg-slate-50/50 mt-4">
                    <span class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-4">Dokumen Pendukung Lainnya</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Pakta Integritas -->
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pakta Integritas</span>
                            @if($agent->foto_pakta_integritas)
                                @php
                                    $isImg = $isImage($agent->foto_pakta_integritas);
                                    $url = $getFileUrl($agent->foto_pakta_integritas);
                                @endphp
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50 relative group mb-2">
                                    <div class="h-20 flex items-center justify-center bg-slate-100 overflow-hidden relative">
                                        @if($isImg)
                                            <img src="{{ $url }}" class="w-full h-full group-hover:scale-105 transition-transform duration-300" style="object-fit: contain; object-position: center;">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-slate-400 p-1 text-center">
                                                <svg class="w-5 h-5 mb-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-[7px] font-bold truncate max-w-[100px]">{{ basename($agent->foto_pakta_integritas) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                        <a href="{{ $url }}" target="_blank" class="text-[8px] font-bold text-slate-700 truncate flex-1 mr-1 hover:text-bpkh-navy" title="{{ basename($agent->foto_pakta_integritas) }}">
                                            {{ basename($agent->foto_pakta_integritas) }}
                                        </a>
                                        <span class="text-[8px] text-purple-650 font-bold shrink-0">Lihat</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2.5 p-2.5 bg-white border border-slate-150 rounded-xl opacity-60">
                                    <div class="w-10 h-8 bg-slate-100 text-slate-400 flex items-center justify-center rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">Belum Diunggah</span>
                                </div>
                            @endif
                        </div>

                        <!-- Bukti Pekerja (B2B Only) -->
                        @if($agent->type === 'institution')
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bukti Anggota / Pegawai</span>
                            @if($agent->bukti_pekerja)
                                @php
                                    $isImg = $isImage($agent->bukti_pekerja);
                                    $url = $getFileUrl($agent->bukti_pekerja);
                                @endphp
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50 relative group mb-2">
                                    <div class="h-20 flex items-center justify-center bg-slate-100 overflow-hidden relative">
                                        @if($isImg)
                                            <img src="{{ $url }}" class="w-full h-full group-hover:scale-105 transition-transform duration-300" style="object-fit: contain; object-position: center;">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-slate-400 p-1 text-center">
                                                <svg class="w-5 h-5 mb-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-[7px] font-bold truncate max-w-[100px]">{{ basename($agent->bukti_pekerja) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                        <a href="{{ $url }}" target="_blank" class="text-[8px] font-bold text-slate-700 truncate flex-1 mr-1 hover:text-bpkh-navy" title="{{ basename($agent->bukti_pekerja) }}">
                                            {{ basename($agent->bukti_pekerja) }}
                                        </a>
                                        <span class="text-[8px] text-teal-650 font-bold shrink-0">Lihat</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2.5 p-2.5 bg-white border border-slate-150 rounded-xl opacity-60">
                                    <div class="w-10 h-8 bg-slate-100 text-slate-400 flex items-center justify-center rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">Belum Diunggah</span>
                                </div>
                            @endif
                        </div>

                        <!-- SK Pengangkatan (B2B Only) -->
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">SK Pengangkatan</span>
                            @if($agent->sk_pengangkatan)
                                @php
                                    $isImg = $isImage($agent->sk_pengangkatan);
                                    $url = $getFileUrl($agent->sk_pengangkatan);
                                @endphp
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50 relative group mb-2">
                                    <div class="h-20 flex items-center justify-center bg-slate-100 overflow-hidden relative">
                                        @if($isImg)
                                            <img src="{{ $url }}" class="w-full h-full group-hover:scale-105 transition-transform duration-300" style="object-fit: contain; object-position: center;">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-slate-400 p-1 text-center">
                                                <svg class="w-5 h-5 mb-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-[7px] font-bold truncate max-w-[100px]">{{ basename($agent->sk_pengangkatan) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                        <a href="{{ $url }}" target="_blank" class="text-[8px] font-bold text-slate-700 truncate flex-1 mr-1 hover:text-bpkh-navy" title="{{ basename($agent->sk_pengangkatan) }}">
                                            {{ basename($agent->sk_pengangkatan) }}
                                        </a>
                                        <span class="text-[8px] text-indigo-650 font-bold shrink-0">Lihat</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2.5 p-2.5 bg-white border border-slate-150 rounded-xl opacity-60">
                                    <div class="w-10 h-8 bg-slate-100 text-slate-400 flex items-center justify-center rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">Belum Diunggah</span>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab Content 2: Ringkasan (Statistics) -->
    <div id="tab-profile-summary" class="hidden bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-6 pb-2 border-b border-slate-100">
            Ringkasan Kinerja Keagenan
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="p-5 border border-slate-150 rounded-2xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Pilgrim Direkrut</span>
                <span class="text-2xl font-black text-slate-800">{{ $agent->prospects()->count() }} Calon Jemaah</span>
            </div>
            <div class="p-5 border border-slate-150 rounded-2xl">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tingkat Pencapaian Komisi</span>
                <span class="text-2xl font-black text-bpkh-navy">Rp {{ number_format($agent->commission_balance, 0, ',', '.') }}</span>
            </div>
        </div>
    <!-- Modal Tolak Pendaftaran Agen -->
    @if(Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin_haji')
    <div id="modal-reject-agent" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeRejectModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-250 z-10 transition-all transform scale-100">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Tolak Berkas & Minta Perbaikan</h3>
                    <button onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="rejectForm" action="{{ route('superadmin.agents.reject', $agent->id) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Agen</label>
                        <input type="text" id="rejectAgentName" value="{{ $agent->full_name ?? $agent->user->name }}" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-600 text-xs outline-none cursor-not-allowed font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" required rows="4" placeholder="Tuliskan catatan perbaikan untuk agen (misal: Foto KTP buram, mohon unggah ulang)..." class="w-full bg-white border border-slate-200 focus:border-red-500 rounded-xl p-3 text-slate-800 text-xs outline-none transition-all"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all border border-slate-200 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm cursor-pointer">
                            Kirim Catatan & Minta Perbaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
    <script>
        function copyReferral() {
            const ref = document.getElementById('refCode').innerText;
            navigator.clipboard.writeText(ref).then(() => {
                alert('Kode Referral berhasil disalin ke papan klip: ' + ref);
            }).catch(err => {
                console.error('Gagal menyalin: ', err);
            });
        }

        function switchProfileTab(tab) {
            if (tab === 'info') {
                document.getElementById('tab-profile-info').classList.remove('hidden');
                document.getElementById('tab-profile-summary').classList.add('hidden');
                
                document.getElementById('tab-btn-info').className = "flex items-center gap-2 px-4 py-2 text-xs font-bold border-b-2 border-bpkh-navy text-bpkh-navy cursor-pointer";
                document.getElementById('tab-btn-summary').className = "flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-750 cursor-pointer";
            } else {
                document.getElementById('tab-profile-info').classList.add('hidden');
                document.getElementById('tab-profile-summary').classList.remove('hidden');
                
                document.getElementById('tab-btn-info').className = "flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-750 cursor-pointer";
                document.getElementById('tab-btn-summary').className = "flex items-center gap-2 px-4 py-2 text-xs font-bold border-b-2 border-bpkh-navy text-bpkh-navy cursor-pointer";
            }
        }

        function openRejectModal(agentId, agentName) {
            const form = document.getElementById('rejectForm');
            if (form) {
                form.action = `/superadmin/agents/${agentId}/reject`;
            }
            const nameInput = document.getElementById('rejectAgentName');
            if (nameInput) {
                nameInput.value = agentName;
            }
            const modal = document.getElementById('modal-reject-agent');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeRejectModal() {
            const modal = document.getElementById('modal-reject-agent');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function toggleInlineReject() {
            const box = document.getElementById('inline-reject-box');
            if (box) {
                box.classList.toggle('hidden');
            }
            openRejectModal({{ $agent->id }}, '{{ addslashes($agent->user->name) }}');
        }
    </script>
@endsection
