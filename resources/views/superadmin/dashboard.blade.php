@extends('layouts.app')

@section('title', 'Superadmin BPKH Dashboard')

@section('sidebar-nav')
    @if(Auth::user()->role === 'admin_haji')
        <button onclick="switchTab('agents')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-white bg-bpkh-navy border border-bpkh-navy shadow-sm" id="tab-btn-agents">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Manajemen Agen</span>
        </button>

        <button onclick="switchTab('institutions')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-institutions">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span>Institusi B2B</span>
        </button>

        <button onclick="switchTab('jemaahs')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-jemaahs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span>CRM Jemaah</span>
        </button>
    @elseif(Auth::user()->role === 'superadmin')
        <button onclick="switchTab('commissions')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-white bg-bpkh-navy border border-bpkh-navy shadow-sm" id="tab-btn-commissions">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Aturan & Target Komisi</span>
        </button>

        <button onclick="switchTab('settings')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-settings">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            </svg>
            <span>Parameter API & Kunci</span>
        </button>

        <button onclick="switchTab('backgrounds')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-backgrounds">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Slideshow Latar</span>
        </button>

        <button onclick="switchTab('audit')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-audit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Log Audit Trail</span>
        </button>

        <button onclick="switchTab('users')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-users">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>Manajemen User</span>
        </button>
    @endif
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Agents Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
            <div class="p-3 bg-bpkh-navy/5 text-bpkh-navy rounded-lg border border-bpkh-navy/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Agen</span>
                <span class="block text-xl font-extrabold text-slate-800 mt-0.5">{{ $stats['total_agents'] }}</span>
            </div>
        </div>

        <!-- Pending Approvals Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
            <div class="p-3 bg-bpkh-gold/5 text-bpkh-gold-hover rounded-lg border border-bpkh-gold/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Persetujuan Pending</span>
                <span class="block text-xl font-extrabold text-slate-800 mt-0.5">{{ $stats['pending_agents'] }}</span>
            </div>
        </div>

        <!-- Total Prospects Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
            <div class="p-3 bg-teal-500/5 text-teal-600 rounded-lg border border-teal-500/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Jemaah</span>
                <span class="block text-xl font-extrabold text-slate-800 mt-0.5">{{ $stats['total_prospects'] }}</span>
            </div>
        </div>

        <!-- Commission Disbursed Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
            <div class="p-3 bg-blue-500/5 text-blue-600 rounded-lg border border-blue-500/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Komisi Dicairkan</span>
                <span class="block text-base font-extrabold text-slate-800 mt-0.5">Rp {{ number_format($stats['total_disbursed'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Tab 1: Manajemen Agen -->
    @if(Auth::user()->role === 'admin_haji')
    <div id="tab-agents" class="tab-panel">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Daftar Seluruh Agen Nasional</h3>
                <p class="text-slate-500 text-xs mt-1">Mengaktifkan/menangguhkan akun, serta mengevaluasi promosi level secara manual.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Agen</th>
                            <th class="px-6 py-4">NIK</th>
                            <th class="px-6 py-4">Tipe Agen</th>
                            <th class="px-6 py-4">Level</th>
                            <th class="px-6 py-4">WhatsApp</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    <a href="{{ route('agent.profile', $agent->referral_code) }}" class="text-bpkh-navy hover:underline">{{ $agent->user->name }}</a>
                                    <span class="block text-slate-400 text-[10px] font-normal mt-0.5">{{ $agent->user->email }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono">{{ $agent->nik }}</td>
                                <td class="px-6 py-4">
                                    @if($agent->type === 'institution')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-teal-50 border border-teal-150 text-teal-700 text-[10px] font-bold">
                                            Institusi (B2B)
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">{{ $agent->institution->name ?? '-' }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 border border-blue-150 text-blue-700 text-[10px] font-bold">
                                            Freelance
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800">{{ $agent->level->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-mono">{{ $agent->whatsapp_number }}</td>
                                <td class="px-6 py-4">
                                    @if($agent->status === 'suspended')
                                        <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold uppercase">Ditangguhkan</span>
                                    @elseif($agent->is_ktp_verified)
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase">Aktif</span>
                                    @elseif($agent->is_submitted)
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-150 text-[10px] font-bold uppercase animate-pulse">Menunggu Verifikasi</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-bold uppercase">Belum Lengkap</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Toggle Status Form -->
                                        <form action="{{ route('superadmin.agents.status', $agent->id) }}" method="POST" class="inline">
                                            @csrf
                                            @if($agent->status === 'active')
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">Tangguhkan</button>
                                            @else
                                                <input type="hidden" name="status" value="active">
                                                @if($agent->is_submitted && !$agent->is_ktp_verified)
                                                    <button type="submit" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white border border-amber-600 rounded-lg font-bold cursor-pointer transition-all">Verifikasi & Aktifkan</button>
                                                @else
                                                    <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg font-bold cursor-pointer transition-all">Aktifkan</button>
                                                @endif
                                            @endif
                                        </form>

                                        <!-- Manual Evaluation -->
                                        <form action="{{ route('superadmin.agents.evaluate', $agent->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg font-bold cursor-pointer transition-all">Audit Level</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada agen haji terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 2: Institusi B2B -->
    @if(Auth::user()->role === 'admin_haji')
    <div id="tab-institutions" class="tab-panel hidden">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Mitra Institusi Haji (B2B)</h3>
                <p class="text-slate-500 text-xs mt-1">Mengelola mitra KBIHU atau perusahaan travel haji nasional.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Institusi</th>
                            <th class="px-6 py-4">Nomor Izin Kemenag / Reg</th>
                            <th class="px-6 py-4">Alamat</th>
                            <th class="px-6 py-4">Jumlah Sub-Agen</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($institutions as $inst)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $inst->name }}</td>
                                <td class="px-6 py-4 font-mono">{{ $inst->registration_number }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $inst->address }}</td>
                                <td class="px-6 py-4 font-bold text-slate-700">{{ $inst->agents_count }} Agen</td>
                                <td class="px-6 py-4">
                                    @if($inst->status === 'active')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold uppercase">Ditangguhkan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('superadmin.institutions.status', $inst->id) }}" method="POST">
                                        @csrf
                                        @if($inst->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">Tangguhkan</button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg font-bold cursor-pointer transition-all">Aktifkan</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">Tidak ada institusi mitra terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 3: CRM Jemaah -->
    @if(Auth::user()->role === 'admin_haji')
    <div id="tab-jemaahs" class="tab-panel hidden">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">CRM Calon Jemaah Haji</h3>
                <p class="text-slate-500 text-xs mt-1">Daftar calon jemaah haji nasional yang didaftarkan melalui agen.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Calon Jemaah</th>
                            <th class="px-6 py-4">NIK</th>
                            <th class="px-6 py-4">Pendaftar (Agen)</th>
                            <th class="px-6 py-4">Jenis Pendaftaran</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4">Nomor Porsi</th>
                            <th class="px-6 py-4">Status Pendaftaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($prospects as $prospect)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $prospect->name }}
                                    <span class="block text-slate-400 text-[10px] font-normal mt-0.5">{{ $prospect->address }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono">{{ $prospect->nik }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-800">{{ $prospect->agent->user->name }}</span>
                                    <span class="block text-[10px] text-slate-400 font-semibold">{{ $prospect->agent->referral_code }}</span>
                                </td>
                                <td class="px-6 py-4 font-bold">{{ $prospect->registration_type }}</td>
                                <td class="px-6 py-4 font-mono text-slate-500">{{ $prospect->phone_number }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-bpkh-navy">{{ $prospect->porsi_number ?? 'BELUM ADA' }}</td>
                                <td class="px-6 py-4">
                                    @if($prospect->status_pendaftaran === 'Verified')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase">Terverifikasi</span>
                                    @elseif($prospect->status_pendaftaran === 'Pending_Verification')
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-150 text-[10px] font-bold uppercase">Pending</span>
                                    @elseif($prospect->status_pendaftaran === 'Canceled')
                                        <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold uppercase">Batal</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">Draft</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400">Belum ada data jemaah yang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 4: Aturan & Target Komisi -->
    @if(Auth::user()->role === 'superadmin')
    <div id="tab-commissions" class="tab-panel">
        <div class="max-w-3xl bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Parameter Tingkatan & Nilai Insentif</h3>
                <p class="text-slate-500 text-xs mt-1">Atur target minimal jemaah yang diverifikasi dan nilai insentif per jemaah untuk masing-masing level.</p>
            </div>

            <form action="{{ route('superadmin.levels.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($levels as $idx => $lvl)
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <input type="hidden" name="levels[{{ $idx }}][id]" value="{{ $lvl->id }}">
                            <div class="w-full md:w-1/4 font-extrabold text-bpkh-navy text-sm">Level {{ $lvl->name }}</div>
                            <div class="w-full md:w-3/8">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Target Minimal Jemaah</label>
                                <input type="number" name="levels[{{ $idx }}][target_prospects]" value="{{ $lvl->target_prospects }}" required
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-xs outline-none focus:border-bpkh-navy/50">
                            </div>
                            <div class="w-full md:w-3/8">
                                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Komisi Per Jemaah (Rupiah)</label>
                                <input type="number" name="levels[{{ $idx }}][commission_per_prospect]" value="{{ intval($lvl->commission_per_prospect) }}" required
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-xs outline-none focus:border-bpkh-navy/50">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-bpkh-gold hover:bg-bpkh-gold-hover text-slate-950 font-bold rounded-lg text-xs cursor-pointer transition-all shadow-sm">
                        Simpan Perubahan Parameter
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Tab 5: Parameter API & Kunci -->
    @if(Auth::user()->role === 'superadmin')
    <div id="tab-settings" class="tab-panel hidden">
        <div class="max-w-3xl bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Parameter API Pihak Ketiga & Private Keys</h3>
                <p class="text-slate-500 text-xs mt-1">Mengelola kredensial verifikasi Dukcapil, Payment Gateway, dan modul WhatsApp.</p>
            </div>

            <form action="{{ route('superadmin.settings.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($settingsRaw as $setting)
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                                {{ $setting->description }} 
                                <span class="font-mono text-[9px] text-slate-400">({{ $setting->key }})</span>
                            </label>
                            @if($setting->is_encrypted)
                                <input type="password" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" required
                                    class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" required
                                    class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-bpkh-gold hover:bg-bpkh-gold-hover text-slate-950 font-bold rounded-lg text-xs cursor-pointer transition-all shadow-sm">
                        Simpan Pengaturan Aman
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Tab 5.5: Slideshow Latar -->
    @if(Auth::user()->role === 'superadmin')
    <div id="tab-backgrounds" class="tab-panel hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upload Form -->
            <div class="lg:col-span-1 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="border-b border-slate-100 pb-4 mb-5">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Unggah Gambar Latar</h3>
                    <p class="text-slate-500 text-xs mt-1">Unggah berkas foto beresolusi tinggi (maksimal 4MB) untuk ditambahkan ke slideshow halaman masuk.</p>
                </div>

                <form action="{{ route('superadmin.backgrounds.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih File Gambar</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-bpkh-navy/50 transition-colors relative group">
                            <input type="file" name="background_image" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 text-slate-350 group-hover:text-bpkh-navy transition-colors mb-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="block text-xs font-bold text-slate-600">Klik / Tarik File ke Sini</span>
                                <span class="block text-[10px] text-slate-400 mt-1">JPEG, PNG, WEBP hingga 4MB</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all shadow-sm">
                        Unggah Gambar
                    </button>
                </form>
            </div>

            <!-- Existing Backgrounds Gallery -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="border-b border-slate-100 pb-4 mb-5">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Daftar Gambar Latar Aktif</h3>
                    <p class="text-slate-500 text-xs mt-1">Daftar seluruh gambar latar belakang login yang sedang berotasi saat ini.</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @forelse($loginBackgrounds as $bg)
                        <div class="relative group border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-slate-50">
                            <div class="h-32 w-full bg-cover bg-center" style="background-image: url('{{ asset($bg->image_path) }}')"></div>
                            
                            <div class="p-3 flex items-center justify-between border-t border-slate-100 bg-white">
                                <div class="truncate mr-2">
                                    <span class="block text-[9px] font-mono text-slate-400 truncate" title="{{ basename($bg->image_path) }}">{{ basename($bg->image_path) }}</span>
                                </div>
                                
                                @if($bg->image_path === 'uploads/backgrounds/bg_default.png')
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-400 text-[8px] font-bold uppercase tracking-wider">Default</span>
                                @else
                                    <form action="{{ route('superadmin.backgrounds.delete', $bg->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus gambar latar ini?')" 
                                                class="text-red-500 hover:text-red-750 hover:bg-red-50 p-1 rounded transition-colors cursor-pointer" title="Hapus Gambar">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center text-slate-400 py-10 text-xs">Belum ada gambar latar belakang kustom yang diunggah.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 6: Log Audit Trail -->
    @if(Auth::user()->role === 'superadmin')
    <div id="tab-audit" class="tab-panel hidden">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Log Audit Keamanan BPKH</h3>
                <p class="text-slate-500 text-xs mt-1">Mengawasi aktivitas login, perubahan status, kenaikan tingkat level agen, serta komisi.</p>
            </div>

            <div class="relative border-l border-slate-200 ml-3 space-y-6">
                @forelse($auditLogs as $log)
                    <div class="relative pl-6">
                        <div class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full bg-bpkh-navy border-2 border-white"></div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d/m/Y H:i:s') }} (IP: {{ $log->ip_address }})</div>
                        <div class="text-xs font-bold text-slate-800 mt-1">
                            {{ $log->user->name ?? 'SYSTEM' }} - 
                            <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[9px] text-slate-500 font-mono uppercase">{{ $log->action }}</span>
                        </div>
                        
                        @if($log->before_payload || $log->after_payload)
                            <div class="mt-2 bg-slate-50 border border-slate-200 rounded-xl p-3 text-[10px] font-mono overflow-x-auto space-y-1 max-w-2xl text-slate-500">
                                @if($log->before_payload)
                                    <div><span class="text-red-600 font-bold">- SEBELUM:</span> {{ json_encode($log->before_payload) }}</div>
                                @endif
                                @if($log->after_payload)
                                    <div><span class="text-emerald-600 font-bold">+ SESUDAH:</span> {{ json_encode($log->after_payload) }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-slate-400 py-8 text-xs">Belum ada rekaman log audit keamanan.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 7: Manajemen User -->
    @if(Auth::user()->role === 'superadmin')
    <div id="tab-users" class="tab-panel hidden">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Manajemen Pengguna Sistem</h3>
                    <p class="text-slate-500 text-xs mt-1">Tambah, ubah, dan hapus akun pengguna sistem dengan hak akses tertentu.</p>
                </div>
                <button onclick="openAddUserModal()" class="self-start sm:self-center px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all shadow-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah User Baru</span>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role / Hak Akses</th>
                            <th class="px-6 py-4">Terdaftar Pada</th>
                            <th class="px-6 py-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $user->name }}</td>
                                <td class="px-6 py-4 font-mono text-slate-500">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if($user->role === 'superadmin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-50 border border-red-150 text-red-700 text-[10px] font-bold">
                                            Superadmin BPKH
                                        </span>
                                    @elseif($user->role === 'admin_haji')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 border border-blue-150 text-blue-700 text-[10px] font-bold">
                                            Admin Agen Haji
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 border border-emerald-150 text-emerald-700 text-[10px] font-bold">
                                            Agen Haji
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-400 font-mono">{{ $user->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')" 
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg font-bold cursor-pointer transition-all">
                                            Ubah
                                        </button>
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('superadmin.users.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 text-[10px] font-semibold italic px-2 py-1">Akun Anda</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada user terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Tambah User -->
    <div id="modal-add-user" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeAddUserModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-250 z-10 transition-all transform scale-100">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Tambah User Baru</h3>
                    <button onclick="closeAddUserModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('superadmin.users.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                        <input type="email" name="email" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Role / Hak Akses</label>
                        <select name="role" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                            <option value="superadmin">Superadmin BPKH</option>
                            <option value="admin_haji">Administrator Agen Haji BPKH</option>
                            <option value="agent">Agen Haji</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs cursor-pointer transition-all">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ubah User -->
    <div id="modal-edit-user" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeEditUserModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-250 z-10 transition-all transform scale-100">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Ubah User</h3>
                    <button onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="edit-user-form" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="edit-user-id">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="edit-user-name" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                        <input type="email" name="email" id="edit-user-email" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Role / Hak Akses</label>
                        <select name="role" id="edit-user-role" required class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                            <option value="superadmin">Superadmin BPKH</option>
                            <option value="admin_haji">Administrator Agen Haji BPKH</option>
                            <option value="agent">Agen Haji</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Password Baru <span class="text-slate-400 font-normal lowercase">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-4 py-2.5 text-slate-800 text-xs outline-none transition-all">
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs cursor-pointer transition-all">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            const activePanel = document.getElementById('tab-' + tabId);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            const buttons = ['agents', 'institutions', 'jemaahs', 'commissions', 'settings', 'backgrounds', 'audit', 'users'];

            buttons.forEach(id => {
                const el = document.getElementById('tab-btn-' + id);
                if (el) {
                    if (id === tabId) {
                        el.className = "flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-white bg-bpkh-navy border border-bpkh-navy shadow-sm";
                    } else {
                        el.className = "flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200";
                    }
                }
            });
        }

        function openAddUserModal() {
            document.getElementById('modal-add-user').classList.remove('hidden');
        }

        function closeAddUserModal() {
            document.getElementById('modal-add-user').classList.add('hidden');
        }

        function openEditUserModal(id, name, email, role) {
            document.getElementById('edit-user-id').value = id;
            document.getElementById('edit-user-name').value = name;
            document.getElementById('edit-user-email').value = email;
            document.getElementById('edit-user-role').value = role;
            document.getElementById('edit-user-form').action = '/superadmin/users/' + id + '/update';
            document.getElementById('modal-edit-user').classList.remove('hidden');
        }

        function closeEditUserModal() {
            document.getElementById('modal-edit-user').classList.add('hidden');
        }
    </script>
@endsection
