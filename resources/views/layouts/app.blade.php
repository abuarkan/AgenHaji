<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BPKH Hajj Agent') - Sistem Manajemen Agen Haji</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bpkh-bg text-slate-800 min-h-screen flex flex-col font-sans antialiased overflow-x-hidden">

    <!-- Top Navigation Header -->
    <header class="h-16 bg-bpkh-navy flex items-center justify-between px-6 z-30 shadow-md">
        <!-- Logo & Title -->
        <div class="flex items-center gap-3">
            <div class="py-1 px-3 rounded-lg bg-white/5 border border-white/10">
                <img src="{{ asset('uploads/logos/bpkh_logo.png') }}" class="h-8 object-contain" alt="BPKH Logo">
            </div>
            <div class="hidden xs:block">
                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-widest leading-none">Portal Aplikasi</span>
                <span class="block text-[10px] text-slate-200 font-extrabold uppercase tracking-widest mt-0.5">Manajemen Agen Haji</span>
            </div>
        </div>

        <!-- Right Side: User Profile & Logout -->
        <div class="flex items-center gap-4">
            <span class="text-xs text-slate-300 hidden sm:inline-block font-medium">Hari ini: {{ now()->translatedFormat('d F Y') }}</span>
            <div class="flex items-center gap-3 pl-4 border-l border-white/15">
                <div class="w-8 h-8 rounded-full bg-bpkh-gold text-slate-950 flex items-center justify-center font-bold text-xs uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="hidden md:block">
                    @if(Auth::user()->role !== 'superadmin' && Auth::user()->role !== 'admin_haji' && Auth::user()->agent)
                        <a href="{{ route('agent.profile', Auth::user()->agent->referral_code) }}" class="hover:underline">
                            <span class="block text-xs font-semibold text-white">{{ Auth::user()->name }}</span>
                        </a>
                    @else
                        <span class="block text-xs font-semibold text-white">{{ Auth::user()->name }}</span>
                    @endif
                    <span class="block text-[9px] text-slate-400 font-medium uppercase tracking-wider">
                        {{ Auth::user()->role === 'superadmin' ? 'Superadmin BPKH' : (Auth::user()->role === 'admin_haji' ? 'Administrator Agen Haji' : (Auth::user()->agent->type === 'institution' ? 'Agen Institusi' : 'Agen Freelance')) }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-400 rounded-lg hover:bg-white/5 transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar Bar (Horizontal Sub-navigation like PDF Page 1) -->
    <nav class="bg-white border-b border-slate-200/80 px-6 py-2 flex items-center gap-1.5 z-20 shadow-sm">
        @yield('sidebar-nav')
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow p-6 max-w-7xl w-full mx-auto">
        <!-- Toast Notifications (Styled in Light Theme) -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm animate-fade-in">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="block font-bold text-sm">Berhasil</span>
                    <span class="block text-xs mt-0.5 text-emerald-700/90">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-start gap-3 shadow-sm animate-fade-in">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="block font-bold text-sm">Kesalahan</span>
                    <span class="block text-xs mt-0.5 text-red-700/90">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
