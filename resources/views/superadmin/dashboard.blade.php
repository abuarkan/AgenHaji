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

        <button onclick="switchTab('gamification')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-gamification">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            <span>Gamifikasi & Racing</span>
        </button>

        <button onclick="switchTab('kinerja')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-kinerja">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span>Kinerja Nasional</span>
        </button>
    @elseif(Auth::user()->role === 'superadmin')
        <button onclick="switchTab('settings')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-white bg-bpkh-navy border border-bpkh-navy shadow-sm" id="tab-btn-settings">
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
            <span>Manajemen Pengguna</span>
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

    @if(Auth::user()->role === 'admin_haji')
    <!-- Tab 5: Kinerja Nasional -->
    <div id="tab-kinerja" class="tab-panel hidden space-y-6 animate-fade-in">
        <!-- Row 1: BPS BPIH Ranking (Podium) & Kinerja Terbaik Perolehan Poin -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- BPS BPIH podium -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-6">Peringkat BPS BPIH Terpopuler (Nasional)</h3>

                <!-- Podium Layout -->
                <div class="flex items-end justify-center gap-4 mb-8 pt-4">
                    <!-- Rank 2 -->
                    <div class="flex flex-col items-center w-24">
                        <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">2</div>
                        <span class="text-[10px] font-bold text-slate-600 mt-2 truncate w-full text-center">Bank Muamalat</span>
                        <div class="w-full h-16 bg-slate-200 rounded-t-lg flex items-center justify-center font-bold text-slate-700 text-xs mt-2 shadow-sm">209.891</div>
                    </div>

                    <!-- Rank 1 -->
                    <div class="flex flex-col items-center w-28">
                        <div class="w-12 h-12 rounded-full bg-amber-50 border-2 border-bpkh-gold flex items-center justify-center text-sm font-black text-slate-800 ring-4 ring-bpkh-gold/10">1</div>
                        <span class="text-[10px] font-extrabold text-slate-800 mt-2 truncate w-full text-center">BSI</span>
                        <div class="w-full h-24 bg-bpkh-navy text-white rounded-t-lg flex items-center justify-center font-extrabold text-xs mt-2 shadow-md">3.417.704</div>
                    </div>

                    <!-- Rank 3 -->
                    <div class="flex flex-col items-center w-24">
                        <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center text-xs font-bold text-slate-700">3</div>
                        <span class="text-[10px] font-bold text-slate-600 mt-2 truncate w-full text-center">BCA Syariah</span>
                        <div class="w-full h-12 bg-slate-200 rounded-t-lg flex items-center justify-center font-bold text-slate-700 text-xs mt-2 shadow-sm">107.865</div>
                    </div>
                </div>

                <!-- Peringkat List -->
                <div class="space-y-3.5 border-t border-slate-100 pt-6">
                    @php
                        $nationalBanks = [
                            ['name' => 'Bank Syariah Indonesia', 'count' => '3.417.704', 'percent' => 90],
                            ['name' => 'Bank Muamalat', 'count' => '209.891', 'percent' => 15],
                            ['name' => 'Bank BCA Syariah', 'count' => '107.865', 'percent' => 10],
                            ['name' => 'Bank Maybank Syariah', 'count' => '98.082', 'percent' => 8],
                            ['name' => 'Bank Mega Syariah', 'count' => '54.934', 'percent' => 5],
                        ];
                    @endphp

                    @foreach($nationalBanks as $idx => $b)
                        <div class="flex items-center gap-4">
                            <span class="w-4 text-xs font-bold text-slate-400 text-right">{{ $idx + 1 }}</span>
                            <div class="flex-1">
                                <div class="flex justify-between text-[11px] font-bold text-slate-700 mb-1">
                                    <span>{{ $b['name'] }}</span>
                                    <span class="font-mono text-slate-500">{{ $b['count'] }} jemaah</span>
                                </div>
                                <div class="w-full h-2 bg-slate-150 rounded overflow-hidden">
                                    <div class="h-full bg-bpkh-navy rounded" style="width: {{ $b['percent'] }}%;"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kinerja Terbaik Perolehan Poin -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-6">Kinerja Terbaik Perolehan Poin (Nasional)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600">
                        <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Peringkat</th>
                                <th class="px-4 py-3">Nama Agen</th>
                                <th class="px-4 py-3">Kode Referral</th>
                                <th class="px-4 py-3">Tingkat Level</th>
                                <th class="px-4 py-3 text-right">Total Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($topPointsAgents as $index => $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 font-bold text-slate-800">
                                        @if($index === 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 font-black">1</span>
                                        @elseif($index === 1)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-700 border border-slate-350 font-black">2</span>
                                        @elseif($index === 2)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-orange-100 text-orange-850 border border-orange-250 font-black">3</span>
                                        @else
                                            <span class="text-slate-400 font-bold pl-1.5">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $item->user->name }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-bpkh-navy">{{ $item->referral_code }}</td>
                                    <td class="px-4 py-3 text-slate-500 font-semibold">{{ $item->level->name }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600">{{ number_format($item->total_points, 0) }} Pts</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">Tidak ada data perolehan poin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 2: Kinerja Referal per Periode & Kinerja Rewards per Periode (Pemenang Racing) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kinerja Referal per Periode -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-6">Kinerja Referal per Periode Program</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-600">
                        <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Nama Program</th>
                                <th class="px-4 py-3">Mulai</th>
                                <th class="px-4 py-3">Selesai</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Jemaah Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($referralProgramsData as $program)
                                @php
                                    $isActive = $program->start_date->lte(now()) && $program->end_date->gte(now()) && $program->is_active;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $program->name }}</td>
                                    <td class="px-4 py-3 text-slate-500 font-mono">{{ $program->start_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-slate-500 font-mono">{{ $program->end_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($isActive)
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-bold">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-bpkh-navy">{{ number_format($program->prospects_count, 0) }} Jemaah</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">Tidak ada program referral terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kinerja Rewards per Periode (Pemenang Racing) -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-6">Kinerja Rewards per Periode Racing</h3>
                <div class="space-y-6">
                    @forelse($racingProgramsData as $program)
                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-3">
                                <div>
                                    <h4 class="font-extrabold text-xs text-slate-800">{{ $program->title }}</h4>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $program->start_date->format('M Y') }} - {{ $program->end_date->format('M Y') }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-bpkh-gold/20 text-bpkh-gold-hover border border-bpkh-gold/30 text-[9px] font-black uppercase tracking-wider">
                                    Reward: {{ $program->reward_type ?? 'Umrah Gratis' }}
                                </span>
                            </div>
                            <div class="space-y-2">
                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kandidat Pemenang Teratas:</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @forelse(array_slice($program->leaderboard_data, 0, 4) as $idx => $candidate)
                                        <div class="flex items-center gap-2 p-2 bg-white border border-slate-150 rounded-xl text-xs">
                                            <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500 shrink-0">{{ $idx + 1 }}</span>
                                            <div class="truncate flex-1">
                                                <span class="block font-bold text-slate-700 truncate" title="{{ $candidate['name'] }}">{{ $candidate['name'] }}</span>
                                                <span class="block text-[10px] text-slate-400 font-mono font-bold">{{ $candidate['portion_count'] }} Porsi Haji</span>
                                            </div>
                                            @if($candidate['is_umrah_winner'])
                                                <span class="text-amber-500" title="Pemenang Umrah">🏆</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-slate-400 font-bold py-1">Belum ada kandidat pemenang.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-6">Tidak ada program racing terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 1: Manajemen Agen -->
    <div id="tab-agents" class="tab-panel">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Daftar Seluruh Agen Nasional</h3>
                    <p class="text-slate-500 text-xs mt-1">Mengaktifkan/menangguhkan akun, serta mengevaluasi promosi level secara manual.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('superadmin.agents.import-template') }}" class="flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition-all shadow-sm">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Template CSV</span>
                    </a>
                    <button onclick="openImportAgentsModal()" class="flex items-center gap-1.5 px-3 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white rounded-lg text-xs font-bold transition-all shadow-sm cursor-pointer border border-bpkh-navy">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Unggah Agen</span>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Agen</th>
                            <th class="px-6 py-4">Kode Referral</th>
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
                                    <span class="block text-slate-400 text-[10px] font-normal mt-0.5 flex items-center gap-1.5">
                                        {{ $agent->user->email }}
                                        <form action="{{ route('superadmin.agents.verify-credentials', $agent->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="field" value="email">
                                            <input type="hidden" name="status" value="{{ $agent->is_email_verified ? '0' : '1' }}">
                                            <button type="submit" title="{{ $agent->is_email_verified ? 'Klik untuk membatalkan verifikasi email' : 'Klik untuk memverifikasi email' }}" class="cursor-pointer border-none bg-transparent p-0 outline-none inline-flex items-center">
                                                @if($agent->is_email_verified)
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 bg-emerald-50 rounded-full p-0.5 border border-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-red-650 bg-red-50 rounded-full p-0.5 border border-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                    <span class="px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono font-bold text-bpkh-navy tracking-wide">
                                        {{ $agent->referral_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    <div class="flex items-center gap-1.5">
                                        {{ $agent->nik }}
                                        <form action="{{ route('superadmin.agents.verify-credentials', $agent->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="field" value="ktp">
                                            <input type="hidden" name="status" value="{{ $agent->is_ktp_verified ? '0' : '1' }}">
                                            <button type="submit" title="{{ $agent->is_ktp_verified ? 'Klik untuk membatalkan verifikasi KTP' : 'Klik untuk memverifikasi KTP' }}" class="cursor-pointer border-none bg-transparent p-0 outline-none inline-flex items-center">
                                                @if($agent->is_ktp_verified)
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 bg-emerald-50 rounded-full p-0.5 border border-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-red-650 bg-red-50 rounded-full p-0.5 border border-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
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
                                <td class="px-6 py-4 text-slate-500 font-mono">
                                    <div class="flex items-center gap-1.5">
                                        {{ $agent->whatsapp_number }}
                                        <form action="{{ route('superadmin.agents.verify-credentials', $agent->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="field" value="whatsapp">
                                            <input type="hidden" name="status" value="{{ $agent->is_whatsapp_verified ? '0' : '1' }}">
                                            <button type="submit" title="{{ $agent->is_whatsapp_verified ? 'Klik untuk membatalkan verifikasi WhatsApp' : 'Klik untuk memverifikasi WhatsApp' }}" class="cursor-pointer border-none bg-transparent p-0 outline-none inline-flex items-center">
                                                @if($agent->is_whatsapp_verified)
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 bg-emerald-50 rounded-full p-0.5 border border-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-red-650 bg-red-50 rounded-full p-0.5 border border-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
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
                                        <!-- Link ke Profil & Berkas Agen -->
                                        <a href="{{ route('agent.profile', $agent->referral_code) }}" class="px-2.5 py-1.5 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs transition-all shadow-sm inline-flex items-center gap-1 cursor-pointer" title="Verifikasi & Validasi Berkas Pendaftaran Agen">
                                            <span>Profil & Berkas</span>
                                        </a>

                                        <!-- Toggle Status Form -->
                                        <form action="{{ route('superadmin.agents.status', $agent->id) }}" method="POST" class="inline">
                                            @csrf
                                            @if($agent->status === 'active')
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">Tangguhkan</button>
                                            @else
                                                <input type="hidden" name="status" value="active">
                                                @if($agent->is_submitted && !$agent->is_ktp_verified)
                                                    <button type="submit" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white border border-amber-600 rounded-lg font-bold cursor-pointer transition-all">Verifikasi OK</button>
                                                    <button type="button" onclick="openRejectModal({{ $agent->id }}, {{ json_encode($agent->user->name) }})" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">Tolak Catatan</button>
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

                                        <!-- Delete Agent Form -->
                                        <form action="{{ route('superadmin.agents.delete', $agent->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agen ini beserta seluruh akun user-nya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-400">Tidak ada agen haji terdaftar.</td>
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
                                <td class="px-6 py-4">
                                    <a href="javascript:void(0)" 
                                       class="btn-institution-dashboard font-extrabold text-bpkh-navy hover:underline block text-xs" 
                                       data-id="{{ $inst->id }}"
                                       data-name="{{ $inst->name }}"
                                       data-agents-count="{{ $inst->agents_count }}"
                                       data-total-jemaah="{{ $inst->total_jemaah }}"
                                       data-total-commission="{{ $inst->total_commission }}"
                                       data-agents="{{ json_encode($inst->agents) }}"
                                       title="Klik untuk melihat resume kinerja institusi">
                                        {{ $inst->name }}
                                    </a>
                                    <!-- List of sub-agents under the institution name -->
                                    <div class="mt-2 pl-2 border-l-2 border-slate-200 space-y-1">
                                        @forelse($inst->agents as $agent)
                                            <div class="text-[10px] text-slate-500 font-medium">
                                                • <a href="{{ route('agent.profile', $agent->referral_code) }}" target="_blank" class="hover:text-bpkh-navy hover:underline font-bold text-slate-700">{{ $agent->user->name }}</a>
                                                <span class="text-[9px] px-1 bg-slate-100 text-slate-500 rounded border border-slate-150 ml-1 uppercase">{{ $agent->status === 'active' ? 'Aktif' : ($agent->status === 'pending' ? 'Pending' : 'Ditangguhkan') }}</span>
                                            </div>
                                        @empty
                                            <div class="text-[10px] text-slate-400 italic">Belum ada sub-agen</div>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono">{{ $inst->registration_number }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $inst->address }}</td>
                                <td class="px-6 py-4 font-bold text-slate-700">
                                    <div>{{ $inst->agents_count }} Agen</div>
                                    <div class="text-[10px] text-slate-400 font-normal mt-1">{{ $inst->total_jemaah }} Jemaah</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($inst->status === 'active')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold uppercase">Ditangguhkan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" 
                                                class="btn-institution-detail px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg font-bold cursor-pointer transition-all"
                                                data-id="{{ $inst->id }}"
                                                data-name="{{ $inst->name }}"
                                                data-reg="{{ $inst->registration_number }}"
                                                data-address="{{ $inst->address }}"
                                                data-npwp="{{ $inst->npwp }}"
                                                data-bank="{{ $inst->bank_account }}"
                                                data-lat="{{ $inst->latitude }}"
                                                data-lng="{{ $inst->longitude }}"
                                                data-legal-doc="{{ $inst->legal_document ? asset($inst->legal_document) : '' }}"
                                                data-agents="{{ json_encode($inst->agents) }}">
                                            Detail
                                        </button>
                                        
                                        <form action="{{ route('superadmin.institutions.status', $inst->id) }}" method="POST" class="inline">
                                            @csrf
                                            @if($inst->status === 'active')
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold cursor-pointer transition-all">Tangguhkan</button>
                                            @else
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg font-bold cursor-pointer transition-all">Aktifkan</button>
                                            @endif
                                        </form>
                                    </div>
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
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
            <!-- Header & Filter Form -->
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">CRM Calon Jemaah Haji</h3>
                <p class="text-slate-500 text-xs mt-1 mb-4">Daftar calon jemaah haji nasional yang didaftarkan melalui agen.</p>

                <!-- Filter Form -->
                <form action="{{ url()->current() }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    <input type="hidden" name="tab" value="jemaahs">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Cari Nama / NIK</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-xs outline-none focus:border-bpkh-navy/50">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Jenis Haji</label>
                        <select name="jenis_haji" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-xs outline-none focus:border-bpkh-navy/50">
                            <option value="">Semua Jenis</option>
                            <option value="Reguler" {{ request('jenis_haji') === 'Reguler' ? 'selected' : '' }}>Reguler</option>
                            <option value="Khusus" {{ request('jenis_haji') === 'Khusus' ? 'selected' : '' }}>Khusus</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-xs outline-none focus:border-bpkh-navy/50 font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-xs outline-none focus:border-bpkh-navy/50 font-bold">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                            Filter
                        </button>
                        <a href="{{ url()->current() }}?tab=jemaahs" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition-all border border-slate-200 text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Jemaah</th>
                            <th class="px-6 py-4">No Porsi Jemaah</th>
                            <th class="px-6 py-4">Jenis Haji</th>
                            <th class="px-6 py-4">Nama Agen</th>
                            <th class="px-6 py-4">Kode Referral</th>
                            <th class="px-6 py-4">Agen Institusi</th>
                            <th class="px-6 py-4">Tanggal Daftar</th>
                            <th class="px-6 py-4">Bank Setoran Awal</th>
                            <th class="px-6 py-4">Status Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($prospects as $prospect)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $prospect->name }}</div>
                                    <div class="text-slate-400 text-[10px] font-normal mt-0.5">NIK: {{ $prospect->nik }}</div>
                                    <div class="text-slate-400 text-[10px] font-normal">{{ $prospect->address }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-bpkh-navy">
                                    {{ $prospect->porsi_number ?? 'BELUM ADA' }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $prospect->registration_type }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $prospect->agent->user->name }}
                                </td>
                                <td class="px-6 py-4 font-mono font-semibold">
                                    {{ $prospect->agent->referral_code }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($prospect->agent->type === 'institution' && $prospect->agent->institution)
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-150 text-[10px] font-bold uppercase">{{ $prospect->agent->institution->name }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">Freelance</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    {{ $prospect->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    {{ $prospect->bps_bpih ?? 'BELUM DITENTUKAN' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($prospect->commission_transfer_status === 'Sudah Ditransfer')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase">Sudah Ditransfer</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-650 border border-slate-200 text-[10px] font-bold uppercase">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-slate-400">Belum ada data jemaah yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Control Footer -->
            @if($prospects->total() > 0)
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-slate-50/50 border-t border-slate-100 text-xs">
                    <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="jemaahs">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('jenis_haji')) <input type="hidden" name="jenis_haji" value="{{ request('jenis_haji') }}"> @endif
                        @if(request('start_date')) <input type="hidden" name="start_date" value="{{ request('start_date') }}"> @endif
                        @if(request('end_date')) <input type="hidden" name="end_date" value="{{ request('end_date') }}"> @endif
                        
                        <span>Tampilkan</span>
                        <select name="per_page" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded px-2.5 py-1.5 text-xs outline-none focus:border-bpkh-navy/50 font-bold text-slate-700">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span>data per halaman</span>
                    </form>
                    
                    <div class="font-semibold text-slate-500">
                        Menampilkan {{ $prospects->firstItem() }} - {{ $prospects->lastItem() }} dari {{ $prospects->total() }} data
                    </div>
                    
                    <div class="superadmin-pagination">
                        {{ $prospects->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- TAB GAMIFIKASI & RACING -->
    <div id="tab-gamification" class="tab-panel space-y-8 {{ request('tab') === 'gamification' ? '' : 'hidden' }}">
            <!-- Header Banner -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6" style="background-color: #0f172a !important; color: #ffffff !important;">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-black uppercase tracking-wider mb-2">Pusat Gamifikasi, Racing & Insentif Agen</span>
                    <h2 class="text-xl font-black text-white" style="color: #ffffff !important;">Program Referral, Insentif #SemuaBisaHaji & Racing</h2>
                    <p class="text-xs text-slate-300 font-medium mt-1 max-w-2xl" style="color: #cbd5e1 !important;">Atur periodisasi monetisasi referral, tingkatkan engagement agen lewat parameter poin, insentif BPKH Apps, dan sinkronisasi API SISKEHAT.</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <form action="{{ route('superadmin.gamification.siskehat') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl text-xs transition-all shadow-lg flex items-center gap-2.5 cursor-pointer border border-emerald-400">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-white font-black">Sinkronkan Data SISKEHAT API</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Grid 2 Columns: Program Referral & Parameter Poin -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Card 1: Periodisasi Program Referral -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Periodisasi Program Referral</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Monetisasi komisi referral hanya aktif pada periode ini</p>
                            </div>
                        </div>

                        <form action="{{ route('superadmin.gamification.referral') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Program Referral*</label>
                                <input type="text" name="name" required placeholder="Contoh: Program Referral Q3 2026" class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-slate-800 text-xs outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Mulai*</label>
                                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl px-3.5 py-2 text-slate-800 text-xs outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Selesai*</label>
                                    <input type="date" name="end_date" required value="{{ date('Y-m-d', strtotime('+3 months')) }}" class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl px-3.5 py-2 text-slate-800 text-xs outline-none transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Program</label>
                                <textarea name="description" rows="2" placeholder="Catatan atau syarat ketentuan program referral..." class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl px-3.5 py-2 text-slate-800 text-xs outline-none transition-all"></textarea>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" name="is_active" id="ref_is_active" value="1" checked class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                                <label for="ref_is_active" class="text-xs font-bold text-slate-700">Set sebagai Program Referral Aktif</label>
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs cursor-pointer transition-all shadow-sm">
                                Simpan & Aktifkan Periode Referral
                            </button>
                        </form>
                    </div>

                    <!-- List Active & Past Referral Programs -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Riwayat Periode Referral</span>
                        <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                            @forelse($referralPrograms as $prog)
                                <div class="p-3 bg-slate-50 border border-slate-150 rounded-2xl flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-slate-800 block">{{ $prog->name }}</span>
                                        <span class="text-[10px] text-slate-500">{{ $prog->start_date->format('d M Y') }} - {{ $prog->end_date->format('d M Y') }}</span>
                                    </div>
                                    @if($prog->is_active && now()->between($prog->start_date, $prog->end_date))
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 font-bold uppercase text-[9px]">Berjalan</span>
                                    @elseif($prog->is_active)
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-150 font-bold uppercase text-[9px]">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-600 font-bold uppercase text-[9px]">Selesai</span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-xs text-slate-400 italic py-2">Belum ada periode referral dibuat.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Card 2: Parameter Sistem Poin Agen -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Parameter Sistem Poin Agen</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Atur jumlah perolehan poin per jenis pendaftaran & milestone</p>
                            </div>
                        </div>

                        <form action="{{ route('superadmin.gamification.points') }}" method="POST" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Poin Pendaftaran Haji Reguler</label>
                                <div class="relative">
                                    <input type="number" name="points_per_reguler" min="0" required value="{{ $settings['points_per_reguler'] ?? 10 }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 rounded-xl px-3.5 py-2.5 text-slate-800 text-xs outline-none transition-all pr-12 font-bold">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400 uppercase">Poin</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Diperoleh agen saat pendaftaran jemaah haji reguler diverifikasi.</span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Poin Pendaftaran Haji Khusus / Plus</label>
                                <div class="relative">
                                    <input type="number" name="points_per_khusus" min="0" required value="{{ $settings['points_per_khusus'] ?? 25 }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 rounded-xl px-3.5 py-2.5 text-slate-800 text-xs outline-none transition-all pr-12 font-bold">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400 uppercase">Poin</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Diperoleh agen saat pendaftaran haji khusus/plus diverifikasi.</span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Bonus Poin Terbit Nomor Porsi Haji</label>
                                <div class="relative">
                                    <input type="number" name="points_per_portion" min="0" required value="{{ $settings['points_per_portion'] ?? 5 }}" class="w-full bg-white border border-slate-200 focus:border-blue-500 rounded-xl px-3.5 py-2.5 text-slate-800 text-xs outline-none transition-all pr-12 font-bold">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400 uppercase">Poin</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Bonus poin tambahan saat Nomor Porsi Haji berhasil diinput.</span>
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-xl text-xs cursor-pointer transition-all shadow-sm">
                                Simpan Parameter Poin
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Card Parameter Insentif BPKH Apps vs Non-BPKH Apps -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Parameter Besaran Insentif Referral (#SemuaBisaHaji)</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Atur tarif insentif progresif per tier untuk channel pendaftaran BPKH Apps & Non-BPKH Apps (Data dari SISKEHAT API)</p>
                    </div>
                </div>

                <form action="{{ route('superadmin.gamification.incentive') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Channel 1: BPKH Apps -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Channel 1: BPKH Apps (Mobile App)</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[9px] uppercase">Prioritas Utama</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Silver (1-20 Porsi)</label>
                                    <input type="number" name="incentive_bpkh_silver" min="0" required value="{{ $incentiveRates['bpkh_apps']['silver'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gold (21-35 Porsi)</label>
                                    <input type="number" name="incentive_bpkh_gold" min="0" required value="{{ $incentiveRates['bpkh_apps']['gold'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Platinum (36-50 Porsi)</label>
                                    <input type="number" name="incentive_bpkh_platinum" min="0" required value="{{ $incentiveRates['bpkh_apps']['platinum'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Diamond (> 50 Porsi)</label>
                                    <input type="number" name="incentive_bpkh_diamond" min="0" required value="{{ $incentiveRates['bpkh_apps']['diamond'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-emerald-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Channel 2: Non-BPKH Apps -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Channel 2: Non-BPKH Apps (Web / Offline)</span>
                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-extrabold text-[9px] uppercase">Channel Reguler</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Silver (1-20 Porsi)</label>
                                    <input type="number" name="incentive_non_bpkh_silver" min="0" required value="{{ $incentiveRates['non_bpkh_apps']['silver'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Gold (21-35 Porsi)</label>
                                    <input type="number" name="incentive_non_bpkh_gold" min="0" required value="{{ $incentiveRates['non_bpkh_apps']['gold'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Platinum (36-50 Porsi)</label>
                                    <input type="number" name="incentive_non_bpkh_platinum" min="0" required value="{{ $incentiveRates['non_bpkh_apps']['platinum'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Diamond (> 50 Porsi)</label>
                                    <input type="number" name="incentive_non_bpkh_diamond" min="0" required value="{{ $incentiveRates['non_bpkh_apps']['diamond'] }}" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-xs transition-all shadow-md flex items-center gap-2 cursor-pointer border border-emerald-500">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-white font-black">Simpan Parameter Insentif BPKH</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Section 3: Program Racing Contest Tenaga Pemasaran & Leaderboard Standings -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Program Racing Contest Tenaga Pemasaran (Reward Umrah)</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Pemeringkatan tenaga pemasar/agen haji berprestasi penerima Reward Umrah BPKH</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Form Program Racing -->
                    <div class="lg:col-span-1 border-r border-slate-100 pr-0 lg:pr-6 space-y-4">
                        <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Luncurkan Racing Contest Baru</h4>
                        <form action="{{ route('superadmin.gamification.racing') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Judul Contest*</label>
                                <input type="text" name="title" required value="Program Racing Contest Tenaga Pemasaran BPS BPIH 2026" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-bold">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tgl Mulai*</label>
                                    <input type="date" name="start_date" required value="2026-08-01" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tgl Selesai*</label>
                                    <input type="date" name="end_date" required value="2026-10-31" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bulan Pengumuman Pemenang</label>
                                <input type="date" name="announcement_date" value="2026-11-30" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bentuk Hadiah Utama</label>
                                    <input type="text" name="reward_type" required value="Paket Umrah Gratis" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jumlah Pemenang (Kuota)</label>
                                    <input type="number" name="winner_quota" required min="1" value="6" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-bold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Threshold Minimal (Kualifikasi Porsi)*</label>
                                <input type="number" name="min_portion_target" required min="1" value="100" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-bold text-purple-700">
                                <span class="text-[9px] text-slate-400 mt-1 block">Agen harus melampaui minimal 100 porsi untuk berhak menjadi calon pemenang Umrah.</span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Target Mitra BPS BPIH</label>
                                <input type="text" name="target_bps_bpih" value="Bank Muamalat Indonesia (dan Seluruh BPS BPIH)" class="w-full bg-white border border-slate-200 focus:border-purple-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all">
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" name="is_active" id="rac_is_active" value="1" checked class="w-4 h-4 text-purple-600 rounded border-slate-300 focus:ring-purple-500">
                                <label for="rac_is_active" class="text-xs font-bold text-slate-700">Set Aktif & Tampilkan di Dashboard Agen</label>
                            </div>

                            <button type="submit" class="w-full py-3 bg-purple-700 hover:bg-purple-800 text-white font-black rounded-2xl text-xs cursor-pointer transition-all shadow-md flex items-center justify-center gap-2 border border-purple-600">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-white font-black">Simpan Perubahan & Luncurkan Racing Contest</span>
                            </button>
                        </form>
                    </div>

                    <!-- Leaderboard Standings Table -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Klasemen Racing Real-time & Proyeksi Pemenang Umrah</h4>
                            @if(!empty($racingLeaderboard['program']))
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-150">
                                    {{ $racingLeaderboard['program']->title }}
                                </span>
                            @endif
                        </div>

                        <div class="overflow-x-auto border border-slate-150 rounded-2xl">
                            <table class="w-full text-xs text-left text-slate-600">
                                <thead class="text-[9px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 text-center">Rank</th>
                                        <th class="px-4 py-3">Nama Agen</th>
                                        <th class="px-4 py-3">Level</th>
                                        <th class="px-4 py-3 text-center">Porsi Terdaftar</th>
                                        <th class="px-4 py-3 text-center">Status Threshold (>=100)</th>
                                        <th class="px-4 py-3 text-center">Status Pemenang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($racingLeaderboard['leaderboard'] ?? [] as $row)
                                        <tr class="hover:bg-slate-50/80 transition-colors {{ !empty($row['is_umrah_winner']) ? 'bg-purple-50/40 font-bold' : '' }}">
                                            <td class="px-4 py-3 text-center">
                                                @if($row['rank'] <= 3)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-600 text-white font-black text-xs shadow-xs">#{{ $row['rank'] }}</span>
                                                @else
                                                    <span class="font-bold text-slate-500">#{{ $row['rank'] }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 font-bold text-slate-800">
                                                <a href="{{ route('agent.profile', $row['referral_code']) }}" target="_blank" class="hover:text-bpkh-navy hover:underline">
                                                    {{ $row['name'] }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $row['level'] }}</td>
                                            <td class="px-4 py-3 text-center font-black text-purple-700 text-sm">{{ $row['portion_count'] }} Porsi</td>
                                            <td class="px-4 py-3 text-center">
                                                @if($row['is_qualified'])
                                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[9px] font-bold uppercase">Lolos (>=100)</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-400 text-[9px] font-bold uppercase">Kurang {{ $row['needed_to_threshold'] }} Porsi</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if(!empty($row['is_umrah_winner']))
                                                    <span class="px-2 py-0.5 rounded bg-amber-400 text-slate-950 font-black text-[9px] uppercase shadow-xs flex items-center justify-center gap-1">
                                                        <span>🕋</span> <span>Pemenang Umrah</span>
                                                    </span>
                                                @elseif($row['is_qualified'])
                                                    <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 text-[9px] font-bold uppercase">Kandidat Cadangan</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-400 text-[9px] font-bold uppercase">Belum Lolos</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada data racing aktif atau belum ada agen yang mendaftarkan nomor porsi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tab 5: Parameter API & Kunci -->
    @if(Auth::user()->role === 'superadmin')
    <div id="tab-settings" class="tab-panel {{ Auth::user()->role === 'superadmin' && !request('tab') ? '' : (request('tab') === 'settings' ? '' : 'hidden') }}">
        <div class="max-w-3xl bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Kredensial API & Mail Server Terenkripsi</h3>
                <p class="text-slate-500 text-xs mt-1">Kelola kredensial mail server (SMTP), autentikasi Google, dan API inti. Semua parameter sensitif disimpan terenkripsi di database.</p>
            </div>

            <form action="{{ route('superadmin.settings.update') }}" method="POST">
                @csrf
                
                <!-- Global 2FA Toggle Switch -->
                <div class="p-5 border border-slate-200 rounded-2xl bg-amber-50/30 border-amber-200/50 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="pr-4">
                            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Autentikasi Dua Faktor (2FA) Global
                            </h4>
                            <p class="text-[11px] text-slate-500 leading-normal">Aktifkan atau nonaktifkan pengiriman kode verifikasi OTP lewat email secara global untuk semua pengguna saat masuk/mendaftar.</p>
                        </div>
                        
                        <!-- Toggle Switch -->
                        <div class="relative inline-flex items-center cursor-pointer">
                            @php
                                $twoFactorEnabledSetting = $settingsRaw->where('key', 'two_factor_enabled')->first();
                                $is2faOn = $twoFactorEnabledSetting && $twoFactorEnabledSetting->value === '1';
                            @endphp
                            <input type="hidden" name="settings[two_factor_enabled]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[two_factor_enabled]" value="1" {{ $is2faOn ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-bpkh-navy"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Group 1: SMTP Settings -->
                    <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/50">
                        <h4 class="text-xs font-extrabold text-bpkh-navy uppercase tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-bpkh-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Konfigurasi Mail Server (SMTP)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($settingsRaw->whereIn('key', ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name']) as $setting)
                                <div class="{{ in_array($setting->key, ['mail_host', 'mail_username', 'mail_password']) ? 'md:col-span-1' : '' }}">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">
                                        {{ $setting->description }}
                                        <span class="font-mono text-[8px] text-slate-400">({{ $setting->key }})</span>
                                    </label>
                                    @if($setting->is_encrypted)
                                        <input type="password" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-xl px-4 py-2 text-slate-800 text-xs outline-none transition-all shadow-sm">
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-xl px-4 py-2 text-slate-800 text-xs outline-none transition-all shadow-sm">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Group 2: Google Client API (OAuth) -->
                    <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/50">
                        <h4 class="text-xs font-extrabold text-bpkh-navy uppercase tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-bpkh-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Konfigurasi Google Client API (OAuth)
                        </h4>
                        <div class="space-y-4">
                            @foreach($settingsRaw->whereIn('key', ['google_client_id', 'google_client_secret', 'google_redirect_uri']) as $setting)
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">
                                        {{ $setting->description }}
                                        <span class="font-mono text-[8px] text-slate-400">({{ $setting->key }})</span>
                                    </label>
                                    @if($setting->is_encrypted)
                                        <input type="password" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-xl px-4 py-2 text-slate-800 text-xs outline-none transition-all shadow-sm">
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-xl px-4 py-2 text-slate-800 text-xs outline-none transition-all shadow-sm">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Group 3: Core API Services -->
                    <div class="p-5 border border-slate-200 rounded-2xl bg-slate-50/50">
                        <h4 class="text-xs font-extrabold text-bpkh-navy uppercase tracking-wider mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-bpkh-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Konfigurasi API Teras (Dukcapil, Payment Gateway, WhatsApp)
                        </h4>
                        <div class="space-y-4">
                            @foreach($settingsRaw->whereIn('key', ['dukcapil_api_url', 'dukcapil_api_key', 'payment_gateway_server_key', 'whatsapp_api_token']) as $setting)
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">
                                        {{ $setting->description }}
                                        <span class="font-mono text-[8px] text-slate-400">({{ $setting->key }})</span>
                                    </label>
                                    @if($setting->is_encrypted)
                                        <input type="password" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-xl px-4 py-2 text-slate-800 text-xs outline-none transition-all shadow-sm">
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-xl px-4 py-2 text-slate-800 text-xs outline-none transition-all shadow-sm">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
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

    <!-- Modal Detail Institusi B2B -->
    <div id="modal-institution-detail" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeInstitutionDetailModal()"></div>
            <div class="relative bg-white rounded-3xl shadow-xl max-w-2xl w-full overflow-hidden border border-slate-200 z-10 transition-all transform scale-100 p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h3 class="text-sm font-extrabold text-bpkh-navy uppercase tracking-wider">Detail Mitra Institusi Haji (B2B)</h3>
                    <button onclick="closeInstitutionDetailModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Grid Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs">
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nama Institusi</span>
                            <span class="text-xs font-bold text-slate-800" id="inst-detail-name"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">No Izin Kemenag / Reg</span>
                            <span class="text-xs font-mono font-bold text-slate-800" id="inst-detail-reg"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">NPWP</span>
                            <span class="text-xs font-mono font-bold text-slate-800" id="inst-detail-npwp"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor Rekening Bank</span>
                            <span class="text-xs font-mono font-bold text-slate-800" id="inst-detail-bank"></span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Alamat Lengkap</span>
                            <span class="text-xs font-bold text-slate-800" id="inst-detail-address"></span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Koordinat Peta</span>
                            <span class="text-xs font-mono font-bold text-slate-800" id="inst-detail-coords"></span>
                        </div>
                    </div>

                    <!-- Dokumen Legalitas Section -->
                    <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl">
                        <span class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-3">Dokumen Legalitas</span>
                        
                        <div id="inst-legal-doc-container">
                            <a href="#" id="inst-detail-legal-doc" target="_blank" class="flex items-center gap-3 p-3 bg-white border border-slate-150 hover:bg-slate-50 rounded-xl transition-all group">
                                <div class="w-10 h-8 bg-red-50 text-red-700 flex items-center justify-center rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="overflow-hidden flex-1">
                                    <span class="block text-xs font-bold text-slate-700 truncate" id="inst-detail-legal-doc-name"></span>
                                    <span class="block text-[9px] text-red-650 font-bold uppercase tracking-wider mt-0.5">Unduh / Lihat Dokumen</span>
                                </div>
                            </a>
                            <div id="inst-detail-legal-doc-empty" class="text-xs font-bold text-slate-400 py-2 hidden">Belum Diunggah</div>
                        </div>
                    </div>

                    <!-- Daftar Sub-Agen Section -->
                    <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl">
                        <span class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-3">Daftar Sub-Agen / Anggota</span>
                        <div id="inst-detail-agents-list" class="space-y-2 max-h-[150px] overflow-y-auto">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 mt-6 flex justify-end">
                    <button type="button" onclick="closeInstitutionDetailModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs cursor-pointer transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Unggah Agen -->
    <div id="modal-import-agents" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeImportAgentsModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-hidden border border-slate-250 z-10 transition-all transform scale-100">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Unggah Agen Masal via CSV</h3>
                    <button onclick="closeImportAgentsModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Step 1: Upload File -->
                <div id="import-step-1" class="p-6 space-y-6">
                    <div class="p-8 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center text-center bg-slate-50/50 hover:bg-slate-50 transition-all cursor-pointer relative" onclick="document.getElementById('csv_file_input').click()">
                        <input type="file" id="csv_file_input" accept=".csv,.txt" class="hidden" onchange="handleCsvFileSelected(this)">
                        <div class="p-4 bg-bpkh-navy/5 text-bpkh-navy rounded-full mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <span id="csv_file_label" class="text-xs font-bold text-slate-600 block">Tarik & Lepaskan File CSV atau Klik di Sini</span>
                        <span class="text-[10px] text-slate-400 block mt-1">Hanya mendukung format file .csv atau .txt (Maksimal 4 MB)</span>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="closeImportAgentsModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs cursor-pointer transition-all">Batal</button>
                        <button type="button" id="btn-validate-upload" disabled onclick="validateCsvFile()" class="px-4 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all flex items-center gap-1.5">
                            <span>Validasi & Pratinjau</span>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Pratinjau & Validasi -->
                <div id="import-step-2" class="p-6 space-y-6 hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-bold">Hasil Validasi Dokumen:</span>
                        <div class="flex gap-2">
                            <span id="badge-valid-count" class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-extrabold uppercase rounded-full">0 Valid</span>
                            <span id="badge-invalid-count" class="px-2.5 py-1 bg-red-50 border border-red-200 text-red-700 text-[10px] font-extrabold uppercase rounded-full">0 Invalid</span>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden max-h-[300px] overflow-y-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase font-bold tracking-wider text-slate-500 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">NIK</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Error</th>
                                </tr>
                            </thead>
                            <tbody id="preview-tbody" class="divide-y divide-slate-100 font-medium">
                                <!-- Dynamic rows via JS -->
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                        <button type="button" onclick="setImportStep(1)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs cursor-pointer transition-all">Kembali</button>
                        <button type="button" id="btn-process-import" onclick="processValidatedAgents()" class="px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all">Proses Impor</button>
                    </div>
                </div>

                <!-- Step 3: Sukses -->
                <div id="import-step-3" class="p-6 space-y-6 hidden flex flex-col items-center justify-center text-center py-12">
                    <div class="p-4 bg-emerald-50 text-emerald-600 rounded-full mb-4 border border-emerald-100">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-base font-extrabold text-slate-800 uppercase tracking-wide">Impor Agen Berhasil!</h4>
                    <p id="success-import-message" class="text-xs text-slate-500 max-w-sm mt-2">Daftar agen baru telah berhasil dimasukkan ke dalam database dengan status Pending.</p>
                    
                    <div class="pt-6 border-t border-slate-100 w-full flex justify-center">
                        <button type="button" onclick="window.location.reload()" class="px-5 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all">Tutup & Reload</button>
                    </div>
                </div>
            </div>
    <!-- MODAL REJECT AGENT -->
    <div id="rejectModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 hidden transition-opacity">
        <div class="bg-white border border-slate-200 p-6 rounded-3xl max-w-md w-full mx-4 shadow-xl">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-base font-extrabold text-slate-800">Tolak Berkas Pendaftaran</h3>
            <p class="text-slate-500 text-xs font-medium mt-1">
                Anda akan menolak pendaftaran berkas untuk agen <span id="reject-agent-name" class="font-bold text-slate-700"></span>.
                Harap masukkan alasan penolakan agar agen dapat memperbaikinya.
            </p>
            
            <form id="rejectForm" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Alasan Penolakan*</label>
                    <textarea name="rejection_reason" required placeholder="Contoh: Foto NPWP buram, silakan upload ulang." rows="3" class="w-full bg-white border border-slate-200 focus:border-red-500 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all"></textarea>
                </div>
                
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 py-2 text-xs font-bold rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2 text-xs font-bold rounded-xl text-white bg-red-650 hover:bg-red-750 cursor-pointer animate-pulse-once">
                        Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DASHBOARD RESUME KINERJA INSTITUSI B2B -->
    <div id="modal-institution-dashboard" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 hidden transition-opacity">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-4xl w-full mx-4 shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-bpkh-navy uppercase tracking-wider">Dashboard Kinerja Mitra B2B</h3>
                    <p class="text-[10px] text-slate-500 font-bold mt-0.5" id="inst-dash-title-name"></p>
                </div>
                <button onclick="closeInstitutionDashboardModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Content Area (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 bg-slate-50/30">
                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <!-- Total Sub-Agen -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Sub-Agen</span>
                            <span class="text-base font-black text-slate-800" id="inst-dash-total-agents">0</span>
                        </div>
                    </div>

                    <!-- Total Jemaah Direkrut -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Jemaah Haji</span>
                            <span class="text-base font-black text-slate-800" id="inst-dash-total-jemaah">0</span>
                        </div>
                    </div>

                    <!-- Total Akumulasi Komisi -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Akumulasi Komisi</span>
                            <span class="text-base font-black text-slate-800" id="inst-dash-total-commission">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Agents Performance Table -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Detail Kinerja Per Agen</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600">
                            <thead class="text-[9px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                                <tr>
                                    <th class="px-5 py-3">Nama Agen</th>
                                    <th class="px-5 py-3">Level</th>
                                    <th class="px-5 py-3 text-center">Jemaah Direkrut</th>
                                    <th class="px-5 py-3 text-right">Total Komisi</th>
                                    <th class="px-5 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="inst-dash-agents-table-body" class="divide-y divide-slate-100">
                                <!-- Dynamic rows via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-150 bg-slate-50/50 flex justify-end">
                <button type="button" onclick="closeInstitutionDashboardModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs cursor-pointer transition-all">Tutup Dashboard</button>
            </div>
        </div>
    </div>

    <!-- Modal Tolak Pendaftaran Agen -->
    <div id="rejectModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeRejectModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden border border-slate-250 z-10 transition-all transform scale-100">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Tolak Berkas & Minta Perbaikan</h3>
                    <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-650 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="rejectForm" action="" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Agen</label>
                        <div id="reject-agent-name" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 text-xs font-bold"></div>
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
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                switchTab(tabParam);
            }

            // Click listener for B2B Dashboard names
            document.querySelectorAll('.btn-institution-dashboard').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const totalAgents = parseInt(this.getAttribute('data-agents-count') || 0);
                    const totalJemaah = parseInt(this.getAttribute('data-total-jemaah') || 0);
                    const totalCommission = parseFloat(this.getAttribute('data-total-commission') || 0);
                    let agents = [];
                    try {
                        agents = JSON.parse(this.getAttribute('data-agents') || '[]');
                    } catch(err) {
                        console.error('Failed to parse agents JSON:', err);
                    }
                    
                    openInstitutionDashboard(id, name, totalAgents, totalJemaah, totalCommission, agents);
                });
            });

            // Click listener for B2B Detail buttons
            document.querySelectorAll('.btn-institution-detail').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const reg = this.getAttribute('data-reg');
                    const address = this.getAttribute('data-address');
                    const npwp = this.getAttribute('data-npwp');
                    const bank = this.getAttribute('data-bank');
                    const lat = this.getAttribute('data-lat');
                    const lng = this.getAttribute('data-lng');
                    const legalDoc = this.getAttribute('data-legal-doc');
                    let agents = [];
                    try {
                        agents = JSON.parse(this.getAttribute('data-agents') || '[]');
                    } catch(err) {
                        console.error('Failed to parse agents JSON:', err);
                    }
                    
                    openInstitutionDetail(id, name, reg, address, npwp, bank, lat, lng, legalDoc, agents);
                });
            });
        });

        function switchTab(tabId) {
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            const activePanel = document.getElementById('tab-' + tabId);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            const buttons = ['agents', 'institutions', 'jemaahs', 'settings', 'backgrounds', 'audit', 'users', 'gamification', 'kinerja'];

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

        let validAgentsToImport = [];

        function openImportAgentsModal() {
            setImportStep(1);
            document.getElementById('csv_file_input').value = '';
            document.getElementById('csv_file_label').innerText = 'Tarik & Lepaskan File CSV atau Klik di Sini';
            document.getElementById('btn-validate-upload').disabled = true;
            document.getElementById('btn-validate-upload').className = "px-4 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all";
            document.getElementById('modal-import-agents').classList.remove('hidden');
        }

        function closeImportAgentsModal() {
            document.getElementById('modal-import-agents').classList.add('hidden');
        }

        function setImportStep(step) {
            document.getElementById('import-step-1').classList.add('hidden');
            document.getElementById('import-step-2').classList.add('hidden');
            document.getElementById('import-step-3').classList.add('hidden');
            document.getElementById('import-step-' + step).classList.remove('hidden');
        }

        function handleCsvFileSelected(input) {
            const label = document.getElementById('csv_file_label');
            const validateBtn = document.getElementById('btn-validate-upload');
            
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                label.innerText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                validateBtn.disabled = false;
                validateBtn.className = "px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm transition-all border border-bpkh-navy";
            } else {
                label.innerText = 'Tarik & Lepaskan File CSV atau Klik di Sini';
                validateBtn.disabled = true;
                validateBtn.className = "px-4 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all";
            }
        }

        function validateCsvFile() {
            const fileInput = document.getElementById('csv_file_input');
            if (!fileInput.files || fileInput.files.length === 0) return;

            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('file', file);

            const validateBtn = document.getElementById('btn-validate-upload');
            const originalText = validateBtn.innerHTML;
            validateBtn.disabled = true;
            validateBtn.innerText = "Memvalidasi...";

            fetch("{{ route('superadmin.agents.import-validate') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(res => {
                validateBtn.disabled = false;
                validateBtn.innerHTML = originalText;

                if (res.success) {
                    document.getElementById('badge-valid-count').innerText = res.valid_count + ' Valid';
                    document.getElementById('badge-invalid-count').innerText = res.invalid_count + ' Invalid';

                    const tbody = document.getElementById('preview-tbody');
                    tbody.innerHTML = '';

                    validAgentsToImport = [];

                    res.rows.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.className = row.is_valid ? 'bg-emerald-50/20' : 'bg-red-50/20';

                        // Status
                        const tdStatus = document.createElement('td');
                        tdStatus.className = 'px-4 py-2.5 text-center font-bold';
                        tdStatus.innerHTML = row.is_valid 
                            ? '<span class="text-emerald-600">✓</span>' 
                            : '<span class="text-red-600">✗</span>';
                        tr.appendChild(tdStatus);

                        // Name
                        const tdName = document.createElement('td');
                        tdName.className = 'px-4 py-2.5 font-bold text-slate-800';
                        tdName.innerText = row.data.name || '-';
                        tr.appendChild(tdName);

                        // Email
                        const tdEmail = document.createElement('td');
                        tdEmail.className = 'px-4 py-2.5';
                        tdEmail.innerText = row.data.email || '-';
                        tr.appendChild(tdEmail);

                        // NIK
                        const tdNik = document.createElement('td');
                        tdNik.className = 'px-4 py-2.5 font-mono';
                        tdNik.innerText = row.data.nik || '-';
                        tr.appendChild(tdNik);

                        // Type
                        const tdType = document.createElement('td');
                        tdType.className = 'px-4 py-2.5 font-bold';
                        tdType.innerText = row.data.type || '-';
                        tr.appendChild(tdType);

                        // Error message
                        const tdError = document.createElement('td');
                        tdError.className = 'px-4 py-2.5 text-red-650 font-bold';
                        tdError.innerText = row.errors.join(', ') || '-';
                        tr.appendChild(tdError);

                        tbody.appendChild(tr);

                        if (row.is_valid) {
                            validAgentsToImport.push(row.data);
                        }
                    });

                    // Disable process button if no valid rows
                    const processBtn = document.getElementById('btn-process-import');
                    if (validAgentsToImport.length === 0) {
                        processBtn.disabled = true;
                        processBtn.className = "px-4 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all";
                    } else {
                        processBtn.disabled = false;
                        processBtn.className = "px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all";
                    }

                    setImportStep(2);
                } else {
                    alert(res.message || 'Gagal memproses file.');
                }
            })
            .catch(err => {
                validateBtn.disabled = false;
                validateBtn.innerHTML = originalText;
                alert(err.message || 'Terjadi kesalahan sistem.');
            });
        }

        function processValidatedAgents() {
            if (validAgentsToImport.length === 0) return;

            const processBtn = document.getElementById('btn-process-import');
            const originalText = processBtn.innerHTML;
            processBtn.disabled = true;
            processBtn.innerText = "Memproses...";

            fetch("{{ route('superadmin.agents.import-process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ agents: validAgentsToImport })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(res => {
                processBtn.disabled = false;
                processBtn.innerHTML = originalText;

                if (res.success) {
                    document.getElementById('success-import-message').innerText = res.message;
                    setImportStep(3);
                } else {
                    alert(res.message || 'Gagal mengimpor data.');
                }
            })
            .catch(err => {
                processBtn.disabled = false;
                processBtn.innerHTML = originalText;
                alert(err.message || 'Terjadi kesalahan sistem.');
            });
        }
        function openInstitutionDetail(id, name, reg, address, npwp, bank, lat, lng, legalDocUrl, agents) {
            document.getElementById('inst-detail-name').innerText = name || '-';
            document.getElementById('inst-detail-reg').innerText = reg || '-';
            document.getElementById('inst-detail-npwp').innerText = npwp || '-';
            document.getElementById('inst-detail-bank').innerText = bank || '-';
            document.getElementById('inst-detail-address').innerText = address || '-';
            
            if (lat && lng) {
                document.getElementById('inst-detail-coords').innerText = lat + ', ' + lng;
            } else {
                document.getElementById('inst-detail-coords').innerText = '-';
            }

            if (legalDocUrl) {
                document.getElementById('inst-detail-legal-doc').href = legalDocUrl;
                document.getElementById('inst-detail-legal-doc-name').innerText = legalDocUrl.substring(legalDocUrl.lastIndexOf('/') + 1);
                document.getElementById('inst-detail-legal-doc').classList.remove('hidden');
                document.getElementById('inst-detail-legal-doc-empty').classList.add('hidden');
            } else {
                document.getElementById('inst-detail-legal-doc').classList.add('hidden');
                document.getElementById('inst-detail-legal-doc-empty').classList.remove('hidden');
            }

            // Render sub-agents list
            const agentsListContainer = document.getElementById('inst-detail-agents-list');
            agentsListContainer.innerHTML = '';
            if (agents && agents.length > 0) {
                agents.forEach(agent => {
                    const agentName = agent.user ? agent.user.name : 'Unknown';
                    const ref = agent.referral_code;
                    const statusText = agent.status === 'active' ? 'Aktif' : (agent.status === 'pending' ? 'Pending' : 'Ditangguhkan');
                    
                    const li = document.createElement('div');
                    li.className = 'flex items-center justify-between p-2 bg-white border border-slate-150 rounded-xl';
                    li.innerHTML = `
                        <a href="/agent/profile/${ref}" target="_blank" class="font-bold text-bpkh-navy hover:underline">${agentName}</a>
                        <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase ${agent.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 'bg-amber-50 text-amber-700 border border-amber-150'}">${statusText}</span>
                    `;
                    agentsListContainer.appendChild(li);
                });
            } else {
                agentsListContainer.innerHTML = '<div class="text-slate-400 font-bold py-1 text-center">Belum ada sub-agen terdaftar.</div>';
            }

            document.getElementById('modal-institution-detail').classList.remove('hidden');
        }

        function closeInstitutionDetailModal() {
            document.getElementById('modal-institution-detail').classList.add('hidden');
        }

        function openRejectModal(agentId, name) {
            document.getElementById('reject-agent-name').innerText = name;
            document.getElementById('rejectForm').action = '/superadmin/agents/' + agentId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function openInstitutionDashboard(id, name, totalAgents, totalJemaah, totalCommission, agents) {
            document.getElementById('inst-dash-title-name').innerText = name || '-';
            document.getElementById('inst-dash-total-agents').innerText = totalAgents + ' Agen';
            document.getElementById('inst-dash-total-jemaah').innerText = totalJemaah + ' Jemaah';
            document.getElementById('inst-dash-total-commission').innerText = 'Rp ' + totalCommission.toLocaleString('id-ID');

            const tbody = document.getElementById('inst-dash-agents-table-body');
            tbody.innerHTML = '';

            if (agents && agents.length > 0) {
                agents.forEach(agent => {
                    const agentName = agent.user ? agent.user.name : 'Unknown';
                    const ref = agent.referral_code;
                    const levelName = agent.level ? agent.level.name : '-';
                    const prospectsCount = agent.prospects_count || 0;
                    const totalComm = agent.total_commission || 0;
                    const statusText = agent.status === 'active' ? 'Aktif' : (agent.status === 'pending' ? 'Pending' : 'Ditangguhkan');
                    
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50/50 transition-colors';
                    tr.innerHTML = `
                        <td class="px-5 py-3 font-bold text-slate-800">
                            <a href="/agent/profile/${ref}" target="_blank" class="text-bpkh-navy hover:underline">${agentName}</a>
                        </td>
                        <td class="px-5 py-3 font-bold text-slate-700">${levelName}</td>
                        <td class="px-5 py-3 text-center font-bold text-slate-800">${prospectsCount}</td>
                        <td class="px-5 py-3 text-right font-mono font-bold text-bpkh-navy">Rp ${totalComm.toLocaleString('id-ID')}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px] ${agent.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 'bg-amber-50 text-amber-700 border border-amber-150'}">${statusText}</span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada sub-agen terdaftar di bawah institusi ini.</td>
                    </tr>
                `;
            }

            document.getElementById('modal-institution-dashboard').classList.remove('hidden');
        }

        function closeInstitutionDashboardModal() {
            document.getElementById('modal-institution-dashboard').classList.add('hidden');
        }
    </script>
@endsection
