<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Agen Haji BPKH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="text-slate-800 min-h-screen flex flex-col justify-between items-center relative overflow-hidden font-sans antialiased p-6">

    <!-- Full-screen Background Slideshow -->
    <div id="background-slideshow" class="absolute inset-0 z-0">
        @forelse($backgrounds as $index => $bg)
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                style="background-image: url('{{ asset($bg->image_path) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
                data-index="{{ $index }}"></div>
        @empty
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100"
                style="background-image: url('{{ asset('uploads/backgrounds/bg_default.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            </div>
        @endforelse
        <!-- Dark Overlay for better contrast -->
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
    </div>

    <!-- Empty top spacer to push card to center -->
    <div></div>

    <!-- Centered Login Box -->
    <div class="w-full max-w-md z-10 my-8">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl p-8 shadow-2xl border border-slate-100 flex flex-col items-center">

            <!-- BPKH Official Logo wrapped in navy blue container to make white text clearly visible -->
            <div
                class="w-full bg-bpkh-navy rounded-xl py-3.5 px-6 flex justify-center mb-6 shadow-md border border-slate-700">
                <img src="{{ asset('uploads/logos/bpkh_logo.png') }}" class="h-10 object-contain"
                    alt="BPKH Logo Official">
            </div>

            <!-- Title & Subtitle from Mockup -->
            <h1 class="text-base font-extrabold text-sky-700 uppercase tracking-wide text-center">Aplikasi Manajemen
                Agen Haji</h1>
            <p class="text-slate-400 text-xs mt-1 text-center font-medium">Sign in to start your session</p>
            @if ($errors->any())
                <div
                    class="w-full mt-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-[11px] leading-relaxed">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm" class="w-full mt-6">
                @csrf
                <div class="mb-4">
                    <label for="email"
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-lg px-4 py-2.5 text-slate-800 placeholder-slate-400 text-xs outline-none transition-all shadow-sm"
                        placeholder="Enter username">
                </div>

                <div class="mb-6">
                    <label for="password"
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-lg pl-4 pr-10 py-2.5 text-slate-800 placeholder-slate-400 text-xs outline-none transition-all shadow-sm"
                            placeholder="Enter password">
                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eye-icon-open" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg id="eye-icon-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-extrabold rounded-lg shadow-md hover:shadow-lg transition-all cursor-pointer text-xs uppercase tracking-wider">
                    Log In
                </button>
            </form>

            <div class="text-center mt-5 w-full border-t border-slate-100 pt-4">
                <a href="{{ route('register') }}"
                    class="text-[11px] font-bold text-slate-400 hover:text-bpkh-navy transition-colors">
                    Belum memiliki akun? Daftar Mandiri di Sini
                </a>
            </div>
        </div>

        <!-- Quick Login Helper Card -->
        <div class="mt-4 bg-white border border-slate-200 rounded-xl p-4 shadow-lg text-xs text-slate-650">
            <span class="block font-bold text-slate-400 mb-2.5 text-[9px] uppercase tracking-wider">Pilih Akun Demo
                (Klik untuk Isi):</span>
            <div class="grid grid-cols-4 gap-2">
                <button type="button" onclick="quickFill('superadmin@bpkh.go.id', 'password123')"
                    class="flex flex-col items-center p-2 rounded-lg bg-slate-50 hover:bg-bpkh-navy/5 border border-slate-200 hover:border-bpkh-navy/20 text-center transition-all cursor-pointer group">
                    <span
                        class="block font-extrabold text-[9px] text-slate-700 group-hover:text-bpkh-navy">Superadmin</span>
                    <span class="text-slate-400 text-[8px] mt-0.5">BPKH Admin</span>
                </button>

                <button type="button" onclick="quickFill('admin.haji@bpkh.go.id', 'password123')"
                    class="flex flex-col items-center p-2 rounded-lg bg-slate-50 hover:bg-bpkh-navy/5 border border-slate-200 hover:border-bpkh-navy/20 text-center transition-all cursor-pointer group">
                    <span
                        class="block font-extrabold text-[9px] text-slate-700 group-hover:text-bpkh-navy text-center leading-none">Admin
                        Haji</span>
                    <span class="text-slate-400 text-[8px] mt-0.5">Ops Admin</span>
                </button>

                <button type="button" onclick="quickFill('kbiu@travel.com', 'password123')"
                    class="flex flex-col items-center p-2 rounded-lg bg-slate-50 hover:bg-bpkh-navy/5 border border-slate-200 hover:border-bpkh-navy/20 text-center transition-all cursor-pointer group">
                    <span
                        class="block font-extrabold text-[9px] text-slate-700 group-hover:text-bpkh-navy">Institusi</span>
                    <span class="text-slate-400 text-[8px] mt-0.5">KBIU Travel</span>
                </button>

                <button type="button" onclick="quickFill('ahmad@gmail.com', 'password123')"
                    class="flex flex-col items-center p-2 rounded-lg bg-slate-50 hover:bg-bpkh-navy/5 border border-slate-200 hover:border-bpkh-navy/20 text-center transition-all cursor-pointer group">
                    <span
                        class="block font-extrabold text-[9px] text-slate-700 group-hover:text-bpkh-navy">Freelance</span>
                    <span class="text-slate-400 text-[8px] mt-0.5">Ahmad Agent</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Copyright Footer from Mockup -->
    <div class="z-10 text-center">
        <p class="text-[10px] text-slate-350 font-medium tracking-wide">© 2026 Badan Pengelola Keuangan Haji</p>
    </div>

    <script>
        function quickFill(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            const form = document.getElementById('loginForm');
            form.classList.add('scale-[1.01]');
            setTimeout(() => form.classList.remove('scale-[1.01]'), 150);
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-icon-open');
            const eyeClosed = document.getElementById('eye-icon-closed');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('#background-slideshow > div[data-index]');
            if (slides.length > 1) {
                let current = 0;
                setInterval(() => {
                    slides[current].classList.replace('opacity-100', 'opacity-0');
                    current = (current + 1) % slides.length;
                    slides[current].classList.replace('opacity-0', 'opacity-100');
                }, 5000);
            }
        });
    </script>
</body>

</html>