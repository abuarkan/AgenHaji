<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil Agen - Sistem Agen Haji BPKH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 min-h-screen flex flex-col justify-between items-center relative overflow-hidden font-sans antialiased p-6">
    
    <!-- Background slideshow with dark overlay -->
    <div id="background-slideshow" class="absolute inset-0 z-0">
        <div class="absolute inset-0"
            style="background-image: url('{{ asset('uploads/backgrounds/bg_default.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        </div>
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
    </div>

    <div></div>

    <!-- Main Complete Profile Box -->
    <div class="w-full max-w-lg z-10 my-8">
        <div class="bg-white rounded-2xl p-8 shadow-2xl border border-slate-100 flex flex-col items-center">
            
            <!-- BPKH Official Logo -->
            <div class="w-full bg-bpkh-navy rounded-xl py-3.5 px-6 flex justify-center mb-6 shadow-md border border-slate-700">
                <img src="{{ asset('uploads/logos/bpkh_logo.png') }}" class="h-10 object-contain" alt="BPKH Logo">
            </div>

            <!-- Title -->
            <h1 class="text-base font-extrabold text-sky-700 uppercase tracking-wide text-center">Lengkapi Data Pendaftaran</h1>
            <p class="text-slate-400 text-xs mt-1 text-center font-medium">Langkah terakhir untuk mengaktifkan akun Agen Haji BPKH Anda</p>

            @if ($errors->any())
                <div class="w-full mt-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-[11px] leading-relaxed">
                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.google.complete.submit') }}" method="POST" class="w-full mt-6 space-y-4">
                @csrf

                <!-- Google Account Info (Disabled display) -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama dari Google</label>
                        <input type="text" disabled value="{{ $googleUser['name'] }}"
                            class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-slate-500 text-xs outline-none cursor-not-allowed font-semibold">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Google</label>
                        <input type="text" disabled value="{{ $googleUser['email'] }}"
                            class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-slate-500 text-xs outline-none cursor-not-allowed font-mono">
                    </div>
                </div>

                <!-- WhatsApp & NIK -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="whatsapp_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. WhatsApp</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" required value="{{ old('whatsapp_number') }}"
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-lg px-4 py-2.5 text-slate-800 placeholder-slate-350 text-xs outline-none transition-all shadow-sm font-mono"
                            placeholder="Contoh: 628123456789">
                    </div>
                    <div>
                        <label for="nik" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">NIK (KTP)</label>
                        <input type="text" name="nik" id="nik" required maxlength="16" value="{{ old('nik') }}"
                            class="w-full bg-white border border-slate-200 focus:border-bpkh-navy rounded-lg px-4 py-2.5 text-slate-800 placeholder-slate-350 text-xs outline-none transition-all shadow-sm font-mono"
                            placeholder="16 Digit NIK KTP">
                    </div>
                </div>

                <!-- Agent Type Selection -->
                <div>
                    <label for="type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipe Kemitraan Agen</label>
                    <select id="type" name="type" required
                        class="block w-full px-4 py-2.5 border border-slate-200 bg-white text-slate-800 text-xs rounded-lg focus:outline-none focus:border-bpkh-navy outline-none font-semibold transition-all">
                        <option value="freelance" {{ old('type') === 'freelance' ? 'selected' : '' }}>Agen Perorangan (Freelance)</option>
                        <option value="institution_employee" {{ old('type') === 'institution_employee' ? 'selected' : '' }}>Petugas Kemitraan B2B (Lembaga/KBIU/Travel)</option>
                    </select>
                </div>

                <!-- Institution Afiliate (Conditional) -->
                <div id="section_institution_employee" class="hidden p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <label for="institution_id" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Institusi Afiliasi Anda</label>
                    <select id="institution_id" name="institution_id"
                        class="block w-full px-4 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-lg focus:outline-none focus:border-bpkh-navy outline-none font-semibold transition-all">
                        <option value="">-- Pilih Institusi Aktif --</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }} ({{ $inst->registration_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 mt-2 bg-bpkh-navy hover:bg-bpkh-navy-light text-white font-extrabold rounded-lg shadow-md hover:shadow-lg transition-all cursor-pointer text-xs uppercase tracking-wider">
                    Selesaikan Pendaftaran
                </button>
            </form>

            <div class="text-center mt-5 w-full border-t border-slate-100 pt-4">
                <a href="{{ route('login') }}" class="text-[11px] font-bold text-slate-400 hover:text-bpkh-navy transition-colors hover:underline">
                    Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="z-10 text-center">
        <p class="text-[10px] text-slate-350 font-medium tracking-wide">© 2026 Badan Pengelola Keuangan Haji</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('type');
            const secInstEmp = document.getElementById('section_institution_employee');
            const instSelect = document.getElementById('institution_id');

            function toggleInstitutionField() {
                if (typeSelect.value === 'institution_employee') {
                    secInstEmp.classList.remove('hidden');
                    instSelect.setAttribute('required', 'required');
                } else {
                    secInstEmp.classList.add('hidden');
                    instSelect.removeAttribute('required');
                }
            }

            typeSelect.addEventListener('change', toggleInstitutionField);
            toggleInstitutionField(); // initial run
        });
    </script>
</body>
</html>
