<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA - Sistem Agen Haji BPKH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .otp-input {
            width: 3.5rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            background-color: #ffffff;
            color: #093566;
            outline: none;
            transition: all 0.2s ease-in-out;
        }

        .otp-input:focus {
            border-color: #093566;
            box-shadow: 0 0 0 3px rgba(9, 53, 102, 0.15);
            transform: scale(1.05);
        }

        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body
    class="text-slate-800 min-h-screen flex flex-col justify-between items-center relative overflow-hidden font-sans antialiased p-6">

    <!-- Full-screen Background with Dark Overlay -->
    <div id="background-slideshow" class="absolute inset-0 z-0">
        <div class="absolute inset-0"
            style="background-image: url('{{ asset('uploads/backgrounds/bg_default.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        </div>
        <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-md"></div>
    </div>

    <!-- Empty top spacer to push card to center -->
    <div></div>

    <!-- Centered Card Box -->
    <div class="w-full max-w-md z-10 my-8">
        <div class="bg-white/95 rounded-2xl p-8 shadow-2xl border border-white/20 backdrop-blur-sm flex flex-col items-center">

            <!-- BPKH Official Logo -->
            <div class="w-full bg-bpkh-navy rounded-xl py-3.5 px-6 flex justify-center mb-6 shadow-md border border-slate-700">
                <img src="{{ asset('uploads/logos/bpkh_logo.png') }}" class="h-10 object-contain" alt="BPKH Logo">
            </div>

            <!-- Title & Subtitle -->
            <h1 class="text-base font-extrabold text-sky-700 uppercase tracking-wide text-center">Verifikasi Keamanan (2FA)</h1>
            <p class="text-slate-500 text-xs mt-1 text-center font-medium font-sans">Kami telah mengirimkan 6-digit kode OTP ke email Anda:</p>
            <span class="text-bpkh-navy font-bold text-sm mt-1 select-all font-mono">{{ Auth::user()->email }}</span>

            <!-- Error and Success Messages -->
            @if ($errors->any())
                <div class="w-full mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-[11px] leading-relaxed">
                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="w-full mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-medium text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- OTP Input Form -->
            <form action="{{ route('two-factor.verify') }}" method="POST" id="otpForm" class="w-full mt-6">
                @csrf
                
                <!-- Hidden single input to submit the concatenated code -->
                <input type="hidden" name="code" id="verificationCode">

                <!-- 6-digit styled OTP grid -->
                <div class="flex justify-between gap-2 mb-6">
                    <input type="text" maxlength="1" class="otp-input" data-index="1" autofocus autocomplete="off">
                    <input type="text" maxlength="1" class="otp-input" data-index="2" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-input" data-index="3" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-input" data-index="4" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-input" data-index="5" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-input" data-index="6" autocomplete="off">
                </div>

                <button type="submit" id="submitBtn" disabled
                    class="w-full py-2.5 px-4 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-extrabold rounded-lg shadow-md hover:shadow-lg transition-all cursor-not-allowed opacity-50 text-xs uppercase tracking-wider">
                    Verifikasi Kode
                </button>
            </form>

            <div class="w-full flex flex-col items-center gap-3 mt-6 border-t border-slate-100 pt-5">
                <!-- Countdown & Resend Button -->
                <div class="text-center text-xs text-slate-400">
                    <span id="timerText">Kirim ulang kode dalam <strong id="countdown" class="text-slate-600 font-bold">120</strong> detik</span>
                    <form action="{{ route('two-factor.resend') }}" method="POST" id="resendForm" class="hidden inline">
                        @csrf
                        <button type="submit" class="text-sky-700 hover:text-sky-850 font-bold hover:underline transition-colors cursor-pointer bg-transparent border-none p-0 outline-none">
                            Kirim Ulang Kode OTP
                        </button>
                    </form>
                </div>

                <!-- Back to Login / Logout -->
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-center text-[11px] font-bold text-red-500 hover:text-red-750 transition-colors cursor-pointer hover:underline bg-transparent border-none p-0 outline-none">
                        Kembali ke Halaman Login (Keluar)
                    </button>
                </form>
            </div>
        </div>

        <!-- Debug Helper: Only visible in local debug environments -->
        @if(config('app.debug'))
            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4 shadow-lg text-xs text-amber-900">
                <div class="flex items-center gap-2 mb-1.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-amber-800 uppercase tracking-wider text-[9px]">LOKAL DEBUG HELPER:</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Karena Anda berada di mode pengembangan lokal, Anda dapat menggunakan kode OTP debug berikut tanpa membuka file log:
                    <strong class="block text-sm text-bpkh-navy mt-1 font-extrabold tracking-widest bg-white py-1 px-3 rounded-lg border border-amber-250 w-max select-all">
                        {{ Auth::user()->two_factor_code ?? 'TIDAK_DITEMUKAN' }}
                    </strong>
                </p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="z-10 text-center">
        <p class="text-[10px] text-slate-400 font-medium tracking-wide">© 2026 Badan Pengelola Keuangan Haji</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.otp-input');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('otpForm');
            const hiddenInput = document.getElementById('verificationCode');

            // Handle OTP input focus and composition
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const val = e.target.value;
                    // Allow only digits
                    if (!/^\d*$/.test(val)) {
                        input.value = '';
                        return;
                    }

                    if (val.length > 0) {
                        // Move to next input if exists
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                    updateSubmitState();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace') {
                        if (input.value.length === 0) {
                            // Backspace on empty input goes back
                            if (index > 0) {
                                inputs[index - 1].value = '';
                                inputs[index - 1].focus();
                            }
                        } else {
                            input.value = '';
                        }
                        updateSubmitState();
                    }
                });

                // Paste support
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').trim();
                    if (/^\d{6}$/.test(pastedData)) {
                        inputs.forEach((inp, idx) => {
                            inp.value = pastedData[idx];
                        });
                        inputs[5].focus();
                        updateSubmitState();
                    }
                });
            });

            function updateSubmitState() {
                let code = '';
                inputs.forEach(inp => code += inp.value);
                hiddenInput.value = code;

                if (code.length === 6) {
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('cursor-not-allowed', 'opacity-50');
                    submitBtn.classList.add('cursor-pointer');
                } else {
                    submitBtn.setAttribute('disabled', 'true');
                    submitBtn.classList.add('cursor-not-allowed', 'opacity-50');
                    submitBtn.classList.remove('cursor-pointer');
                }
            }

            // Form Submit Listener
            form.addEventListener('submit', (e) => {
                let code = '';
                inputs.forEach(inp => code += inp.value);
                hiddenInput.value = code;
                if (code.length !== 6) {
                    e.preventDefault();
                }
            });

            // Countdown Timer Logic
            let seconds = 120;
            const countdownEl = document.getElementById('countdown');
            const timerTextEl = document.getElementById('timerText');
            const resendFormEl = document.getElementById('resendForm');

            const timer = setInterval(() => {
                seconds--;
                if (countdownEl) {
                    countdownEl.textContent = seconds;
                }

                if (seconds <= 0) {
                    clearInterval(timer);
                    if (timerTextEl && resendFormEl) {
                        timerTextEl.classList.add('hidden');
                        resendFormEl.classList.remove('hidden');
                    }
                }
            }, 1000);
        });
    </script>
</body>

</html>
