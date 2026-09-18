<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Verifikasi Akun - Sistem Agen Haji BPKH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .custom-otp-input {
            letter-spacing: 0.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            padding-left: 0.75rem; /* center the letters */
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col justify-between items-center relative overflow-hidden font-sans antialiased p-6">
    <!-- Full-screen Background with Dark Overlay -->
    <div id="background-slideshow" class="absolute inset-0 z-0">
        <div class="absolute inset-0" style="background-image: url('{{ asset('uploads/backgrounds/bg_default.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
        <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-md"></div>
    </div>

    <div></div>

    <div class="w-full max-w-lg z-10 my-8">
        <!-- BPKH Official Logo -->
        <div class="w-full bg-bpkh-navy rounded-xl py-3.5 px-6 flex justify-center mb-6 shadow-md border border-slate-700">
            <img src="{{ asset('uploads/logos/bpkh_logo.png') }}" class="h-10 object-contain" alt="BPKH Logo">
        </div>

        <div class="bg-white/95 rounded-2xl p-8 shadow-2xl border border-white/20 backdrop-blur-sm flex flex-col gap-6">
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 text-center">Pusat Verifikasi Kredensial</h1>
                <p class="text-slate-500 text-xs mt-1 text-center font-medium">Selesaikan langkah verifikasi di bawah ini untuk mengaktifkan akun agen Anda.</p>
            </div>

            <!-- Error and Success Messages -->
            @if ($errors->any())
                <div class="w-full p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-[11px] leading-relaxed">
                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="w-full p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-medium text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="w-full p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-medium text-center">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Step 1: Email Verification Card -->
            <div class="p-5 rounded-xl border {{ $agent->is_email_verified ? 'bg-emerald-50/30 border-emerald-200' : 'bg-slate-50/50 border-slate-200' }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-bold {{ $agent->is_email_verified ? 'text-emerald-800' : 'text-slate-850' }}">1. Verifikasi Alamat Email</h2>
                        <p class="text-slate-500 text-[11px] mt-0.5">Kode OTP dikirimkan ke: <span class="font-semibold text-slate-700">{{ $agent->user->email }}</span></p>
                    </div>
                    @if ($agent->is_email_verified)
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full border border-amber-200 animate-pulse">
                            Menunggu
                        </span>
                    @endif
                </div>

                @if (!$agent->is_email_verified)
                    <form action="{{ route('verify-credentials.email') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="code" maxlength="6" required autocomplete="off" placeholder="------" class="custom-otp-input bg-white text-slate-800 rounded-lg border-2 border-slate-200 focus:border-sky-700 outline-none w-full py-2 max-w-[160px] font-mono">
                            <button type="submit" class="flex-1 bg-sky-700 hover:bg-sky-800 text-white font-bold text-xs py-2 rounded-lg cursor-pointer transition-all">Verifikasi</button>
                        </div>
                    </form>

                    <div class="mt-3 flex items-center justify-between gap-4">
                        <form action="{{ route('verify-credentials.resend') }}" method="POST" id="resendEmailForm" class="inline">
                            @csrf
                            <input type="hidden" name="type" value="email">
                            <button type="submit" id="resendEmailBtn" class="text-sky-700 hover:underline font-bold text-[11px] cursor-pointer bg-transparent border-none p-0 outline-none">
                                Kirim Ulang Kode
                            </button>
                            <span id="emailCountdown" class="text-slate-400 text-[11px] font-medium hidden"></span>
                        </form>
                    </div>


                @endif
            </div>

            <!-- Step 2: WhatsApp Verification Card -->
            <div class="p-5 rounded-xl border {{ $agent->is_whatsapp_verified ? 'bg-emerald-50/30 border-emerald-200' : 'bg-slate-50/50 border-slate-200' }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-bold {{ $agent->is_whatsapp_verified ? 'text-emerald-800' : 'text-slate-850' }}">2. Verifikasi Nomor WhatsApp</h2>
                        <p class="text-slate-500 text-[11px] mt-0.5">Kode OTP dikirimkan ke: <span class="font-semibold text-slate-700">{{ $agent->whatsapp_number }}</span></p>
                    </div>
                    @if ($agent->is_whatsapp_verified)
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full border border-amber-200 animate-pulse">
                            Menunggu
                        </span>
                    @endif
                </div>

                @if (!$agent->is_whatsapp_verified)
                    <form action="{{ route('verify-credentials.whatsapp') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="code" maxlength="6" required autocomplete="off" placeholder="------" class="custom-otp-input bg-white text-slate-800 rounded-lg border-2 border-slate-200 focus:border-sky-700 outline-none w-full py-2 max-w-[160px] font-mono">
                            <button type="submit" class="flex-1 bg-sky-700 hover:bg-sky-800 text-white font-bold text-xs py-2 rounded-lg cursor-pointer transition-all">Verifikasi</button>
                        </div>
                    </form>

                    <div class="mt-3 flex items-center justify-between gap-4">
                        <form action="{{ route('verify-credentials.resend') }}" method="POST" id="resendWhatsappForm" class="inline">
                            @csrf
                            <input type="hidden" name="type" value="whatsapp">
                            <button type="submit" id="resendWhatsappBtn" class="text-sky-700 hover:underline font-bold text-[11px] cursor-pointer bg-transparent border-none p-0 outline-none">
                                Kirim Ulang Kode
                            </button>
                            <span id="whatsappCountdown" class="text-slate-400 text-[11px] font-medium hidden"></span>
                        </form>
                    </div>


                @endif
            </div>

            <!-- Logout Link -->
            <div class="w-full text-center mt-2 border-t border-slate-100 pt-4">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-slate-700 text-xs font-bold transition-all bg-transparent border-none cursor-pointer p-0 outline-none">
                        Keluar dari Akun (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Empty bottom spacer to push card to center -->
    <div class="text-[10px] text-slate-400 font-sans z-10">&copy; 2026 Badan Pengelola Keuangan Haji</div>

    <!-- Countdown Javascript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupCountdown('resendEmailBtn', 'emailCountdown', 'email_cooldown');
            setupCountdown('resendWhatsappBtn', 'whatsappCountdown', 'whatsapp_cooldown');

            function setupCountdown(btnId, spanId, storageKey) {
                const btn = document.getElementById(btnId);
                const span = document.getElementById(spanId);
                if (!btn) return;

                let cooldownTime = localStorage.getItem(storageKey);
                let now = Math.floor(Date.now() / 1000);

                if (cooldownTime && cooldownTime > now) {
                    startTimer(btn, span, cooldownTime - now, storageKey);
                }

                btn.addEventListener('click', function() {
                    let nowTime = Math.floor(Date.now() / 1000);
                    localStorage.setItem(storageKey, nowTime + 60);
                    setTimeout(function() {
                        startTimer(btn, span, 60, storageKey);
                    }, 100);
                });
            }

            function startTimer(btn, span, duration, storageKey) {
                btn.style.display = 'none';
                span.style.display = 'inline';
                
                let remaining = duration;
                span.innerText = `Kirim ulang dalam ${remaining}s`;

                let interval = setInterval(function() {
                    remaining--;
                    if (remaining <= 0) {
                        clearInterval(interval);
                        btn.style.display = 'inline';
                        span.style.display = 'none';
                        localStorage.removeItem(storageKey);
                    } else {
                        span.innerText = `Kirim ulang dalam ${remaining}s`;
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>
