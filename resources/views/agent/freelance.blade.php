@extends('layouts.app')

@section('title', 'Dashboard Agen Freelance')

@section('sidebar-nav')
    <button onclick="switchTab('dashboard')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-white bg-bpkh-navy border border-bpkh-navy shadow-sm" id="tab-btn-dashboard">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span>Beranda</span>
    </button>

    <button onclick="switchTab('jemaah')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-jemaah">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <span>Jemaah</span>
    </button>

    <button onclick="switchTab('ledger')" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200" id="tab-btn-ledger">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Mutasi Komisi Saya</span>
    </button>

    <a href="{{ route('agent.profile', $agent->referral_code) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span>Profil Saya</span>
    </a>
@endsection

@section('content')
    <!-- Tab 1: Beranda / Dashboard Stats & Charts -->
    <div id="tab-dashboard" class="tab-panel">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Levels and Summary -->
            <div class="space-y-6">
                <!-- Pilgrim Count Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jemaah Terdaftar</span>
                        <span class="block text-4xl font-extrabold text-slate-800 mt-1">{{ $stats['total_prospects'] }}</span>
                        <span class="block text-[10px] text-slate-400 font-semibold mt-1 uppercase tracking-wide">Calon Jemaah Haji</span>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-1 rounded bg-bpkh-navy/5 text-bpkh-navy font-bold text-[10px] border border-bpkh-navy/10 uppercase mb-1">
                            Reguler: {{ \App\Models\ProspectJemaah::where('registration_type', 'Reguler')->count() }}
                        </span>
                        <br>
                        <span class="inline-block px-2.5 py-1 rounded bg-bpkh-gold/10 text-bpkh-gold font-bold text-[10px] border border-bpkh-gold/20 uppercase">
                            Khusus: {{ \App\Models\ProspectJemaah::where('registration_type', 'Khusus')->count() }}
                        </span>
                    </div>
                </div>

                <!-- Level Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Level Keagenan</span>
                            <span class="block text-2xl font-extrabold text-slate-800 mt-0.5">{{ $agent->level->name }}</span>
                        </div>
                        <span class="px-3 py-1 rounded-xl bg-bpkh-gold text-slate-950 font-black text-xs uppercase shadow-sm">
                            {{ $agent->level->name }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-xs font-semibold text-slate-600 border-t border-slate-100 pt-4">
                        <div class="flex justify-between">
                            <span>Jemaah Terverifikasi:</span>
                            <strong class="text-slate-800">{{ $stats['verified_prospects'] }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Insentif per Jemaah:</span>
                            <strong class="text-emerald-700">@Rp {{ number_format($agent->level->commission_per_prospect, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Perolehan Insentif:</span>
                            <strong class="text-bpkh-navy">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Level Progress Line -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-5">Target Pencapaian Level</h4>
                    
                    <!-- Progress Timeline dots -->
                    <div class="relative pl-6 space-y-6 border-l border-slate-200">
                        @php
                            $levels = \App\Models\AgentLevel::orderBy('target_prospects', 'asc')->get();
                            $nextLvl = \App\Models\AgentLevel::where('target_prospects', '>', $agent->level->target_prospects)->orderBy('target_prospects', 'asc')->first();
                        @endphp
                        
                        @foreach($levels as $lvl)
                            @php
                                $isActive = $agent->agent_level_id === $lvl->id;
                                $isCompleted = $stats['verified_prospects'] >= $lvl->target_prospects;
                            @endphp
                            <div class="relative">
                                <!-- Dot indicator -->
                                <span class="absolute -left-[31px] top-1 w-4.5 h-4.5 rounded-full flex items-center justify-center border-2 transition-all 
                                    {{ $isActive ? 'bg-bpkh-gold border-bpkh-navy text-slate-950 ring-4 ring-bpkh-gold/20' : ($isCompleted ? 'bg-bpkh-navy border-bpkh-navy text-white' : 'bg-white border-slate-300 text-slate-400') }}">
                                    @if($isCompleted)
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold {{ $isActive ? 'text-slate-800' : 'text-slate-500' }}">{{ $lvl->name }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">({{ $lvl->target_prospects }} Jemaah)</span>
                                    </div>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">Komisi: Rp {{ number_format($lvl->commission_per_prospect, 0, ',', '.') }} / Jemaah</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($nextLvl)
                        @php
                            $remaining = $nextLvl->target_prospects - $stats['verified_prospects'];
                        @endphp
                        <div class="mt-6 pt-4 border-t border-slate-100 bg-amber-50/50 border border-amber-100 rounded-lg p-3">
                            <span class="block text-[10px] text-amber-800 font-bold tracking-wide">
                                🔔 {{ $remaining }} jemaah lagi untuk naik ke level {{ $nextLvl->name }}!
                            </span>
                            <span class="block text-[9px] text-amber-600 font-medium mt-0.5">Ayo terus promosikan BPKH untuk memaksimalkan insentif!</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Center Column: BPS BPIH Leaderboard & Charts -->
            <div class="lg:col-span-2 space-y-6">
                <!-- BPS BPIH Podiums and Leaderboard -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
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

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kinerja Agen per BPS-BPIH -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-6">Kinerja BPS BPIH Saya</h4>
                        
                        <!-- Custom CSS Bar Chart -->
                        <div class="flex items-end justify-between h-48 pt-6 border-b border-slate-200 font-bold">
                            @php
                                $chartBanks = [
                                    'BSI' => $stats['bank_stats']['Bank Syariah Indonesia'] ?? 0,
                                    'Muamalat' => $stats['bank_stats']['Bank Muamalat Indonesia'] ?? ($stats['bank_stats']['Bank Muamalat'] ?? 0),
                                    'CIMB' => $stats['bank_stats']['CIMB Niaga Syariah'] ?? 0,
                                    'Mega' => $stats['bank_stats']['Bank MEGA Syariah'] ?? 0,
                                ];
                                $maxCount = max(1, max(array_values($chartBanks)));
                            @endphp
                            
                            @foreach($chartBanks as $lbl => $cnt)
                                @php $pct = round(($cnt / $maxCount) * 80); @endphp
                                <div class="flex flex-col items-center flex-1 gap-2">
                                    <div class="text-[9px] font-bold text-slate-500">{{ $cnt }}</div>
                                    <div class="w-8 bg-bpkh-navy rounded-t transition-all hover:bg-bpkh-navy-light" style="height: {{ max(5, $pct) }}%;"></div>
                                    <div class="text-[10px] font-bold text-slate-700 truncate w-12 text-center" title="{{ $lbl }}">{{ $lbl }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kinerja Agen per Provinsi -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-6">Sebaran Jemaah per Provinsi</h4>
                        
                        <!-- Custom CSS Horizontal Bar Chart -->
                        <div class="space-y-4 pt-2">
                            @php
                                $provMax = max(1, max(array_values($stats['province_stats'])));
                            @endphp
                            @foreach($stats['province_stats'] as $prov => $cnt)
                                @php $pct = round(($cnt / $provMax) * 100); @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-20 text-[10px] font-bold text-slate-500 truncate text-right">{{ $prov }}</div>
                                    <div class="flex-grow h-2.5 bg-slate-100 rounded overflow-hidden">
                                        <div class="h-full bg-bpkh-gold rounded" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <div class="w-6 text-[10px] font-mono font-bold text-slate-800 text-right">{{ $cnt }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Kelola Jemaah -->
    <div id="tab-jemaah" class="tab-panel hidden">
        <!-- Control Header & Filters -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 border-b border-slate-100 pb-5 mb-5">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Kelola Jemaah Haji</h3>
                <div class="flex gap-2 w-full md:w-auto">
                    <button onclick="openImportModal()" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-lg text-xs cursor-pointer shadow-sm transition-all flex items-center gap-1.5 border border-slate-200">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Import Excel (CSV)</span>
                    </button>
                    <button onclick="toggleModal(true)" class="px-4 py-2.5 bg-bpkh-gold hover:bg-bpkh-gold-hover text-slate-950 font-bold rounded-lg text-xs cursor-pointer shadow-sm transition-all flex items-center gap-1.5 border border-bpkh-gold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Daftarkan Jemaah Baru</span>
                    </button>
                </div>
            </div>

            <!-- Filters Form -->
            <form action="{{ route('agent.freelance') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="filter_status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status Pendaftaran</label>
                        <select name="status" id="filter_status" class="w-full bg-white border border-slate-200 rounded-lg px-4 py-2 text-slate-700 text-xs outline-none focus:border-bpkh-navy/40">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status Pendaftar</option>
                            <option value="Pendaftar Haji" {{ request('status') == 'Pendaftar Haji' ? 'selected' : '' }}>Pendaftar Haji</option>
                            <option value="Tertarik Daftar Haji" {{ request('status') == 'Tertarik Daftar Haji' ? 'selected' : '' }}>Tertarik Daftar Haji</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter_search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama atau Email</label>
                        <input type="text" name="search_name" id="filter_search" placeholder="Cari nama / email..." value="{{ request('search_name') }}"
                            class="w-full bg-white border border-slate-200 rounded-lg px-4 py-2 text-slate-700 text-xs outline-none focus:border-bpkh-navy/40">
                    </div>
                    <div>
                        <label for="filter_bank" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">BPS-BPIH Bank</label>
                        <input type="text" name="search_bank" id="filter_bank" placeholder="Cari bank..." value="{{ request('search_bank') }}"
                            class="w-full bg-white border border-slate-200 rounded-lg px-4 py-2 text-slate-700 text-xs outline-none focus:border-bpkh-navy/40">
                    </div>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:flex-initial px-6 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm transition-all">
                        Cari
                    </button>
                    @if(request()->anyFilled(['status', 'search_name', 'search_bank']))
                        <a href="{{ route('agent.freelance') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-lg text-xs cursor-pointer transition-all border border-slate-200 text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600 min-w-[1000px] table-fixed">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-48 sticky left-0 bg-slate-50 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Nama Pengguna</th>
                            <th class="px-6 py-4 w-28">Tipe Haji</th>
                            <th class="px-6 py-4 w-44">Email</th>
                            <th class="px-6 py-4 w-48">Nomor Handphone</th>
                            <th class="px-6 py-4 w-36">Status</th>
                            <th class="px-6 py-4 w-44">BPS-BPIH</th>
                            <th class="px-6 py-4 w-32">Klaim Porsi</th>
                            <th class="px-6 py-4 w-36">Nomor Porsi</th>
                            <th class="px-6 py-4 w-32 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($prospects as $prospect)
                            <tr class="group hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800 break-words sticky left-0 bg-white group-hover:bg-slate-50 transition-colors shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    {{ $prospect->name }}
                                    <span class="block text-slate-400 text-[10px] font-normal mt-0.5 break-words">{{ $prospect->address }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($prospect->registration_type !== '-')
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px] uppercase border
                                            {{ $prospect->registration_type === 'Reguler' ? 'bg-teal-50 text-teal-700 border-teal-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                                            {{ $prospect->registration_type }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-500 break-all">{{ $prospect->email ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if(!empty($prospect->phone_number))
                                        <span class="font-mono text-slate-600">{{ $prospect->phone_number }}</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[9px] font-bold uppercase tracking-wider">
                                            Nomor HP Belum Tersimpan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($prospect->status_pendaftaran === 'Pendaftar Haji')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-250 text-[10px] font-bold uppercase">
                                            Pendaftar Haji
                                        </span>
                                    @elseif($prospect->status_pendaftaran === 'Tertarik Daftar Haji')
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-250 text-[10px] font-bold uppercase">
                                            Tertarik Daftar Haji
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-bold uppercase">
                                            -
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700 break-words">{{ $prospect->bps_bpih ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($prospect->claim_status === 'Disetujui')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold uppercase">
                                            Disetujui
                                        </span>
                                    @elseif($prospect->claim_status === 'Ditolak')
                                        <span class="px-2 py-0.5 rounded bg-red-50 border border-red-200 text-red-700 text-[10px] font-bold uppercase">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-bpkh-navy break-all">{{ $prospect->porsi_number ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Kelola / Edit -->
                                        <button onclick="openEditModal({{ json_encode($prospect) }})"
                                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded text-[10px] cursor-pointer transition-all border border-slate-200">
                                            Kelola
                                        </button>
                                        <!-- Hapus -->
                                        <form action="{{ route('agent.prospects.delete', $prospect->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Jemaah ini?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded text-[10px] cursor-pointer transition-all border border-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-slate-400">Jemaah tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($prospects->hasPages())
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                    {{ $prospects->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Tab 3: Buku Besar Komisi -->
    <div id="tab-ledger" class="tab-panel hidden">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide">Buku Besar & Riwayat Mutasi Komisi Anda</h3>
                <p class="text-slate-500 text-xs mt-1">Daftar lengkap perolehan komisi dan riwayat pencairan saldo.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="text-[10px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Tanggal Transaksi</th>
                            <th class="px-6 py-4">Keterangan / Jemaah</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Jumlah</th>
                            <th class="px-6 py-4">Saldo Kumulatif</th>
                            <th class="px-6 py-4">Status Payout</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ledgers as $ledger)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono text-slate-400">{{ $ledger->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-6 py-4">
                                    @if($ledger->prospect_jemaah_id)
                                        <span class="block font-bold text-slate-800">Komisi Jemaah: {{ $ledger->prospectJemaah->name }}</span>
                                        <span class="block text-slate-400 text-[10px]">NIK: {{ $ledger->prospectJemaah->nik }}</span>
                                    @else
                                        <span class="block font-bold text-slate-800">Pencairan Dana (Disbursement)</span>
                                        <span class="block text-slate-400 text-[10px]">Ref: {{ $ledger->disbursement_reference ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($ledger->type === 'credit')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold uppercase">Credit</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold uppercase">Debit</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold {{ $ledger->type === 'credit' ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $ledger->type === 'credit' ? '+' : '-' }} Rp {{ number_format($ledger->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700">Rp {{ number_format($ledger->balance_after, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if($ledger->status === 'approved' || $ledger->status === 'disbursed')
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-100 text-emerald-700 text-[10px] font-bold uppercase">Selesai</span>
                                    @elseif($ledger->status === 'pending')
                                        <span class="px-2 py-0.5 rounded bg-amber-50 border border-amber-100 text-amber-700 text-[10px] font-bold uppercase">Pending</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-red-50 border border-red-100 text-red-700 text-[10px] font-bold uppercase">Batal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada transaksi terekam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Registration & Edit Modal -->
    <div id="registerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto py-10">
        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg p-6 shadow-xl relative mx-4 my-auto">
            <button onclick="toggleModal(false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <h3 id="modalTitle" class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-6">Pendaftaran Calon Jemaah Haji Baru</h3>

            <div class="mb-5" id="nikVerificationBlock">
                <label for="modal_nik" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">NIK Calon Jemaah (KTP)</label>
                <div class="flex gap-2 font-mono">
                    <input type="text" id="modal_nik" maxlength="16" placeholder="Masukkan 16 digit NIK"
                        class="flex-1 bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                    <button type="button" onclick="verifyNik()" id="verifyBtn"
                        class="px-4 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer transition-all shadow-sm">
                        Verifikasi NIK
                    </button>
                </div>
                <div id="nik_feedback" class="mt-2 text-[10px] font-bold hidden"></div>
            </div>

            <form action="{{ route('agent.prospects.store') }}" method="POST" id="submitForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="nik" id="form_nik">

                <div class="mb-4">
                    <label for="form_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap Jemaah</label>
                    <input type="text" name="name" id="form_name" required readonly
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-400 text-xs outline-none cursor-not-allowed">
                </div>

                <div class="mb-4">
                    <label for="form_address" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Lengkap (Sesuai KTP)</label>
                    <textarea name="address" id="form_address" required readonly rows="2"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-400 text-xs outline-none cursor-not-allowed"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih Koordinat Tinggal (Peta Klik / Geser)</label>
                    <div id="map" class="h-36 w-full rounded-lg border border-slate-200 mb-2 relative z-0"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="text" name="location_lat" id="form_lat" readonly placeholder="Latitude"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-slate-500 font-mono text-[10px] outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <input type="text" name="location_lng" id="form_lng" readonly placeholder="Longitude"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-slate-500 font-mono text-[10px] outline-none cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="form_phone" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone_number" id="form_phone" placeholder="Contoh: 0812XXXXXXXX"
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label for="form_email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                        <input type="email" name="email" id="form_email" placeholder="contoh@domain.com"
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="registration_type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tipe Pendaftaran Haji</label>
                        <select name="registration_type" id="registration_type" required
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                            <option value="-">-</option>
                            <option value="Reguler">Haji Reguler</option>
                            <option value="Khusus">Haji Khusus</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_pendaftaran" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status Pendaftaran</label>
                        <select name="status_pendaftaran" id="status_pendaftaran" required
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                            <option value="-">-</option>
                            <option value="Tertarik Daftar Haji">Tertarik Daftar Haji</option>
                            <option value="Pendaftar Haji">Pendaftar Haji</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="bps_bpih" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Bank Penyalur BPS-BPIH</label>
                        <select name="bps_bpih" id="bps_bpih"
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                            <option value="-">-</option>
                            <option value="Bank Syariah Indonesia">Bank Syariah Indonesia (BSI)</option>
                            <option value="Bank Muamalat Indonesia">Bank Muamalat</option>
                            <option value="Bank BCA Syariah">BCA Syariah</option>
                            <option value="Bank Maybank Syariah">Maybank Syariah</option>
                            <option value="Bank MEGA Syariah">Bank Mega Syariah</option>
                            <option value="CIMB Niaga Syariah">CIMB Niaga Syariah</option>
                        </select>
                    </div>
                    <div>
                        <label for="claim_status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Klaim Porsi</label>
                        <select name="claim_status" id="claim_status"
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                            <option value="-">-</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="porsi_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nomor Porsi</label>
                    <input type="text" name="porsi_number" id="porsi_number" placeholder="-"
                        class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none">
                </div>

                <div class="border-t border-slate-100 pt-4 mb-5">
                    <h4 class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-3">Dokumen Wajib Calon Jemaah</h4>
                    <div class="space-y-3.5">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto KTP Jemaah (Maks 2MB)</label>
                            <input type="file" name="ktp_photo" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            <div id="preview_ktp_container" class="mt-1.5 hidden">
                                <a id="preview_ktp_link" href="#" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-bpkh-navy hover:underline font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Lihat Foto KTP Terunggah
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto Buku Tabungan BPS-BPIH (Maks 2MB)</label>
                            <input type="file" name="saving_book_photo" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            <div id="preview_saving_book_container" class="mt-1.5 hidden">
                                <a id="preview_saving_book_link" href="#" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-bpkh-navy hover:underline font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Lihat Foto Buku Tabungan Terunggah
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Foto Kartu NPWP Jemaah (Maks 2MB, Opsional)</label>
                            <input type="file" name="npwp_photo" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                            <div id="preview_npwp_container" class="mt-1.5 hidden">
                                <a id="preview_npwp_link" href="#" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-bpkh-navy hover:underline font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Lihat Foto NPWP Terunggah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn" disabled
                    class="w-full py-2.5 px-4 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs transition-all cursor-not-allowed border border-slate-200">
                    Selesaikan Pendaftaran (Verifikasi NIK Dahulu)
                </button>
            </form>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div id="importModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto py-10">
        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-2xl p-6 shadow-xl relative mx-4 my-auto">
            <button onclick="closeImportModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-6">Bulk Import Jemaah (Excel CSV)</h3>

            <!-- Step 1: Upload File -->
            <div id="import-step-1" class="space-y-5">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-4">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700">Unduh Template Import Excel (CSV)</h4>
                        <p class="text-[10px] text-slate-500 mt-1">Gunakan template resmi untuk menghindari kegagalan pemetaan data.</p>
                    </div>
                    <a href="{{ route('agent.prospects.import-template') }}" class="px-3.5 py-1.5 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-[10px] shadow-sm transition-all flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Template
                    </a>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pilih File CSV Hasil Ekspor Excel</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-bpkh-navy/55 rounded-xl p-8 text-center relative transition-colors cursor-pointer group">
                        <input type="file" id="csv_file_input" accept=".csv,.txt" onchange="handleCsvFileSelected(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg class="w-10 h-10 text-slate-400 group-hover:text-bpkh-navy mx-auto mb-2.5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span id="csv_file_label" class="block text-xs font-bold text-slate-600 transition-colors">Tarik & Lepaskan File atau Klik di Sini</span>
                        <span class="block text-[9px] text-slate-400 mt-1">Maksimal ukuran file: 4MB (Format .csv atau .txt dengan pembatas koma / semicolon)</span>
                    </div>
                    <div class="mt-2 text-right">
                        <button type="button" id="btn-mock-upload" onclick="injectMockCsvFile()" class="text-[10px] text-bpkh-navy hover:underline font-bold">
                            [Gunakan File Uji Otomatis]
                        </button>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button id="btn-validate-upload" disabled onclick="validateCsvFile()" class="px-5 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all flex items-center gap-1.5">
                        Unggah & Validasi
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Step 2: Validate & Preview -->
            <div id="import-step-2" class="space-y-4 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Pratinjau Hasil Validasi Data Jemaah</h4>
                        <p class="text-[10px] text-slate-500 mt-0.5">Hanya baris dengan status valid (✓) yang akan dimasukkan ke sistem.</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-150 text-[10px] font-bold" id="badge-valid-count">0 Valid</span>
                        <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-150 text-[10px] font-bold" id="badge-invalid-count">0 Invalid</span>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden max-h-60 overflow-y-auto">
                    <table class="w-full text-left text-[11px] text-slate-600">
                        <thead class="bg-slate-50 text-[9px] uppercase font-bold text-slate-500 border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 w-10 text-center">Status</th>
                                <th class="px-4 py-2.5 w-36">NIK</th>
                                <th class="px-4 py-2.5 w-32">Nama</th>
                                <th class="px-4 py-2.5 w-44">Alamat</th>
                                <th class="px-4 py-2.5">Catatan / Error</th>
                            </tr>
                        </thead>
                        <tbody id="preview-tbody" class="divide-y divide-slate-100">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                    <button onclick="goToImportStep(1)" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 font-bold rounded-lg text-xs border border-slate-200 transition-all flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </button>
                    <button id="btn-process-import" onclick="processImportedProspects()" class="px-5 py-2 bg-bpkh-gold hover:bg-bpkh-gold-hover text-slate-950 font-bold rounded-lg text-xs shadow-sm transition-all border border-bpkh-gold flex items-center gap-1">
                        Mulai Proses Impor
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Step 3: Progress & Summary -->
            <div id="import-step-3" class="space-y-6 text-center py-6 hidden">
                <!-- Spinner/Progress State -->
                <div id="import-progress-state" class="space-y-4">
                    <div class="relative w-16 h-16 mx-auto">
                        <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-bpkh-navy border-t-transparent animate-spin"></div>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Sedang memproses impor data...</h4>
                        <p class="text-[10px] text-slate-500 mt-1">Harap tidak menutup jendela modal ini.</p>
                    </div>
                </div>

                <!-- Finished/Success State -->
                <div id="import-success-state" class="space-y-4 hidden">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto border border-emerald-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Proses Impor Selesai!</h4>
                        <p class="text-[10px] text-slate-500 mt-1" id="import-success-message">Berhasil mengimpor 0 data jemaah baru.</p>
                    </div>
                    <div class="pt-2">
                        <button onclick="reloadPageWithTab()" class="px-5 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                            Selesai & Muat Ulang Halaman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');

            const buttons = {
                'dashboard': 'tab-btn-dashboard',
                'jemaah': 'tab-btn-jemaah',
                'ledger': 'tab-btn-ledger'
            };

            Object.keys(buttons).forEach(btn => {
                const el = document.getElementById(buttons[btn]);
                if (btn === tabId) {
                    el.className = "flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-white bg-bpkh-navy border border-bpkh-navy shadow-sm";
                } else {
                    el.className = "flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg transition-all cursor-pointer text-slate-600 hover:bg-slate-100/80 bg-white border border-slate-200";
                }
            });
        }

        // Auto switch tab if search query params are present
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('status') || urlParams.has('search_name') || urlParams.has('search_bank') || urlParams.has('page')) {
                switchTab('jemaah');
            } else {
                switchTab('dashboard');
            }
        });

        let validProspectsToImport = [];

        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            goToImportStep(1);
            
            // Reset input
            const fileInput = document.getElementById('csv_file_input');
            fileInput.value = '';
            document.getElementById('csv_file_label').innerText = 'Tarik & Lepaskan File atau Klik di Sini';
            document.getElementById('btn-validate-upload').disabled = true;
            document.getElementById('btn-validate-upload').className = "px-5 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all flex items-center gap-1.5";
            validProspectsToImport = [];
        }

        function injectMockCsvFile() {
            const csvContent = `nik;name;address;phone_number;email;registration_type;bps_bpih;claim_status;porsi_number;location_lat;location_lng\n3171012345670999;Ahmad Valid;Jl. Kebon Jeruk No. 12;08123456789;ahmad_valid@example.com;Reguler;Bank Syariah Indonesia;Disetujui;1234567890;-6.20880000;106.84560000\n123;Jemaah Invalid;Jl. Mawar No. 45;08139876543;siti_invalid@example.com;Khusus;Bank Muamalat Indonesia;Disetujui;1234567891;-6.91750000;107.61910000`;

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const file = new File([blob], 'test_bulk_jemaah.csv', { type: 'text/csv' });
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            
            const fileInput = document.getElementById('csv_file_input');
            fileInput.files = dataTransfer.files;
            
            handleCsvFileSelected(fileInput);
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function goToImportStep(step) {
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
                validateBtn.className = "px-5 py-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm transition-all border border-bpkh-navy flex items-center gap-1.5";
            } else {
                label.innerText = 'Tarik & Lepaskan File atau Klik di Sini';
                validateBtn.disabled = true;
                validateBtn.className = "px-5 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all flex items-center gap-1.5";
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

            fetch("{{ route('agent.prospects.import-validate') }}", {
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
                    // Populate step 2
                    document.getElementById('badge-valid-count').innerText = res.valid_count + ' Valid';
                    document.getElementById('badge-invalid-count').innerText = res.invalid_count + ' Invalid';

                    const tbody = document.getElementById('preview-tbody');
                    tbody.innerHTML = '';

                    validProspectsToImport = [];

                    res.rows.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.className = row.is_valid ? 'bg-emerald-50/20' : 'bg-red-50/20';

                        // Status column
                        const tdStatus = document.createElement('td');
                        tdStatus.className = 'px-4 py-2.5 text-center font-bold';
                        tdStatus.innerHTML = row.is_valid 
                            ? '<span class="text-emerald-600">✓</span>' 
                            : '<span class="text-red-600">✗</span>';
                        tr.appendChild(tdStatus);

                        // NIK column
                        const tdNik = document.createElement('td');
                        tdNik.className = 'px-4 py-2.5 font-mono';
                        tdNik.innerText = row.data.nik || '-';
                        tr.appendChild(tdNik);

                        // Name column
                        const tdName = document.createElement('td');
                        tdName.className = 'px-4 py-2.5 font-bold text-slate-800';
                        tdName.innerText = row.data.name || '-';
                        tr.appendChild(tdName);

                        // Address column
                        const tdAddress = document.createElement('td');
                        tdAddress.className = 'px-4 py-2.5 max-w-xs truncate';
                        tdAddress.innerText = row.data.address || '-';
                        tr.appendChild(tdAddress);

                        // Errors column
                        const tdErrors = document.createElement('td');
                        tdErrors.className = 'px-4 py-2.5 text-red-650 font-medium text-[10px]';
                        tdErrors.innerText = row.is_valid ? 'Siap diimpor' : row.errors.join(', ');
                        tr.appendChild(tdErrors);

                        tbody.appendChild(tr);

                        if (row.is_valid) {
                            validProspectsToImport.push(row.data);
                        }
                    });

                    // Disable or enable import execution button
                    const processBtn = document.getElementById('btn-process-import');
                    if (validProspectsToImport.length > 0) {
                        processBtn.disabled = false;
                        processBtn.className = "px-5 py-2 bg-bpkh-gold hover:bg-bpkh-gold-hover text-slate-950 font-bold rounded-lg text-xs shadow-sm transition-all border border-bpkh-gold cursor-pointer";
                    } else {
                        processBtn.disabled = true;
                        processBtn.className = "px-5 py-2 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs cursor-not-allowed border border-slate-200 transition-all";
                    }

                    goToImportStep(2);
                } else {
                    alert(res.message || 'Gagal memvalidasi file.');
                }
            })
            .catch(err => {
                validateBtn.disabled = false;
                validateBtn.innerHTML = originalText;
                alert(err.message || 'Gagal memvalidasi file. Harap periksa format file.');
            });
        }

        function processImportedProspects() {
            if (validProspectsToImport.length === 0) return;

            goToImportStep(3);
            
            // Show progress state
            document.getElementById('import-progress-state').classList.remove('hidden');
            document.getElementById('import-success-state').classList.add('hidden');

            fetch("{{ route('agent.prospects.import-process') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ prospects: validProspectsToImport })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('import-progress-state').classList.add('hidden');
                    document.getElementById('import-success-state').classList.remove('hidden');
                    document.getElementById('import-success-message').innerText = `Berhasil mengimpor ${res.count} data jemaah baru ke sistem.`;
                } else {
                    alert(res.message || 'Gagal memproses import.');
                    goToImportStep(2);
                }
            })
            .catch(err => {
                alert(err.message || 'Terjadi kesalahan sistem saat memproses import.');
                goToImportStep(2);
            });
        }

        function reloadPageWithTab() {
            const url = new URL(window.location.href);
            url.searchParams.set('status', 'all'); // forces jemaah tab trigger
            window.location.href = url.toString();
        }

        let map;
        let marker;

        function initLeafletMap(lat, lng) {
            if (!map) {
                map = L.map('map').setView([lat || -6.2088, lng || 106.8456], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                map.on('click', function(e) {
                    setMapCoordinates(e.latlng.lat, e.latlng.lng);
                });
            } else {
                map.setView([lat || -6.2088, lng || 106.8456], 13);
            }

            setMapCoordinates(lat, lng, false);
            
            setTimeout(() => {
                map.invalidateSize();
            }, 250);
        }

        function setMapCoordinates(lat, lng) {
            if (!lat || !lng) {
                document.getElementById('form_lat').value = '';
                document.getElementById('form_lng').value = '';
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }
                return;
            }

            document.getElementById('form_lat').value = parseFloat(lat).toFixed(8);
            document.getElementById('form_lng').value = parseFloat(lng).toFixed(8);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    setMapCoordinates(pos.lat, pos.lng);
                });
            }
        }

        function toggleModal(show) {
            const modal = document.getElementById('registerModal');
            if (show) {
                // Set default Create mode
                document.getElementById('modalTitle').innerText = "Pendaftaran Calon Jemaah Haji Baru";
                document.getElementById('submitBtn').innerText = "Daftarkan Jemaah Baru";
                
                const form = document.getElementById('submitForm');
                form.action = "{{ route('agent.prospects.store') }}";
                
                document.getElementById('nikVerificationBlock').classList.remove('hidden');
                document.getElementById('modal_nik').disabled = false;
                document.getElementById('verifyBtn').classList.remove('hidden');
                
                document.getElementById('modal_nik').value = '';
                document.getElementById('form_nik').value = '';
                document.getElementById('form_name').value = '';
                document.getElementById('form_address').value = '';
                document.getElementById('form_phone').value = '';
                document.getElementById('form_email').value = '';
                document.getElementById('registration_type').value = '-';
                document.getElementById('status_pendaftaran').value = '-';
                document.getElementById('bps_bpih').value = '-';
                document.getElementById('claim_status').value = '-';
                document.getElementById('porsi_number').value = '';
                
                // Hide file previews
                document.getElementById('preview_ktp_container').classList.add('hidden');
                document.getElementById('preview_saving_book_container').classList.add('hidden');
                document.getElementById('preview_npwp_container').classList.add('hidden');

                document.getElementById('nik_feedback').classList.add('hidden');
                lockForm();
                modal.classList.remove('hidden');

                initLeafletMap(-6.2088, 106.8456);
            } else {
                modal.classList.add('hidden');
            }
        }

        function openEditModal(prospect) {
            // Change title
            document.getElementById('modalTitle').innerText = "Kelola / Perbarui Data Jemaah";
            document.getElementById('submitBtn').innerText = "Simpan Perubahan";
            
            // Set form action url
            const form = document.getElementById('submitForm');
            form.action = `/agent/prospects/${prospect.id}/update`;
            
            // Hide NIK verification block
            document.getElementById('nikVerificationBlock').classList.add('hidden');
            document.getElementById('form_nik').value = prospect.nik;
            
            const nameInput = document.getElementById('form_name');
            nameInput.value = prospect.name;
            nameInput.className = "w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none";
            nameInput.readOnly = false;
            
            const addressInput = document.getElementById('form_address');
            addressInput.value = prospect.address;
            addressInput.className = "w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-lg px-4 py-2 text-slate-800 text-xs outline-none";
            addressInput.readOnly = false;
            
            document.getElementById('form_phone').value = prospect.phone_number;
            document.getElementById('form_email').value = prospect.email || '';
            document.getElementById('registration_type').value = prospect.registration_type || '-';
            document.getElementById('status_pendaftaran').value = prospect.status_pendaftaran || '-';
            document.getElementById('bps_bpih').value = prospect.bps_bpih || '-';
            document.getElementById('claim_status').value = prospect.claim_status || '-';
            document.getElementById('porsi_number').value = prospect.porsi_number || '';
            
            // Previews
            if (prospect.ktp_photo_path) {
                document.getElementById('preview_ktp_link').href = '/storage/' + prospect.ktp_photo_path;
                document.getElementById('preview_ktp_container').classList.remove('hidden');
            } else {
                document.getElementById('preview_ktp_container').classList.add('hidden');
            }
            if (prospect.saving_book_photo_path) {
                document.getElementById('preview_saving_book_link').href = '/storage/' + prospect.saving_book_photo_path;
                document.getElementById('preview_saving_book_container').classList.remove('hidden');
            } else {
                document.getElementById('preview_saving_book_container').classList.add('hidden');
            }
            if (prospect.npwp_photo_path) {
                document.getElementById('preview_npwp_link').href = '/storage/' + prospect.npwp_photo_path;
                document.getElementById('preview_npwp_container').classList.remove('hidden');
            } else {
                document.getElementById('preview_npwp_container').classList.add('hidden');
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = false;
            submitBtn.className = "w-full py-2.5 px-4 bg-bpkh-gold hover:bg-bpkh-gold-hover text-slate-950 font-bold rounded-lg text-xs cursor-pointer border border-bpkh-gold";
            
            document.getElementById('registerModal').classList.remove('hidden');

            const lat = prospect.location_lat ? parseFloat(prospect.location_lat) : -6.2088;
            const lng = prospect.location_lng ? parseFloat(prospect.location_lng) : 106.8456;
            initLeafletMap(lat, lng);
        }

        function verifyNik() {
            const nik = document.getElementById('modal_nik').value;
            const feedback = document.getElementById('nik_feedback');
            const verifyBtn = document.getElementById('verifyBtn');

            if (nik.length !== 16 || isNaN(nik)) {
                feedback.className = "mt-2 text-[10px] text-red-650 font-bold";
                feedback.innerText = "Masukkan 16 digit angka NIK yang valid.";
                feedback.classList.remove('hidden');
                return;
            }

            verifyBtn.disabled = true;
            verifyBtn.innerText = "Checking...";

            fetch("{{ route('agent.verify-nik') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ nik: nik })
            })
            .then(res => res.json())
            .then(res => {
                verifyBtn.disabled = false;
                verifyBtn.innerText = "Verifikasi NIK";

                if (res.success) {
                    feedback.className = "mt-2 text-[10px] text-emerald-600 font-bold";
                    feedback.innerText = "✓ NIK Terverifikasi dengan Dukcapil.";
                    feedback.classList.remove('hidden');

                    document.getElementById('form_nik').value = nik;
                    
                    const nameInput = document.getElementById('form_name');
                    nameInput.value = res.data.name;
                    nameInput.classList.remove('bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
                    nameInput.classList.add('bg-white', 'text-slate-800');
                    nameInput.readOnly = false;

                    const addressInput = document.getElementById('form_address');
                    addressInput.value = res.data.address;
                    addressInput.classList.remove('bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
                    addressInput.classList.add('bg-white', 'text-slate-800');
                    addressInput.readOnly = false;

                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'border-slate-200');
                    submitBtn.classList.add('bg-bpkh-gold', 'hover:bg-bpkh-gold-hover', 'text-slate-950', 'cursor-pointer');
                    submitBtn.innerText = "Daftarkan Jemaah Baru";
                } else {
                    feedback.className = "mt-2 text-[10px] text-red-650 font-bold";
                    feedback.innerText = res.message || "Gagal memverifikasi NIK.";
                    feedback.classList.remove('hidden');
                    lockForm();
                }
            })
            .catch(err => {
                verifyBtn.disabled = false;
                verifyBtn.innerText = "Verifikasi NIK";
                feedback.className = "mt-2 text-[10px] text-red-650 font-bold";
                feedback.innerText = "Gagal menghubungi Dukcapil Hub.";
                feedback.classList.remove('hidden');
                lockForm();
            });
        }

        function lockForm() {
            const nameInput = document.getElementById('form_name');
            nameInput.readOnly = true;
            nameInput.className = "w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-400 text-xs outline-none cursor-not-allowed";

            const addressInput = document.getElementById('form_address');
            addressInput.readOnly = true;
            addressInput.className = "w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-slate-400 text-xs outline-none cursor-not-allowed";

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.className = "w-full py-2.5 px-4 bg-slate-100 text-slate-400 font-bold rounded-lg text-xs transition-all cursor-not-allowed border border-slate-200";
            submitBtn.innerText = "Selesaikan Pendaftaran (Verifikasi NIK Dahulu)";
        }
    </script>
@endsection
