<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Agen Baru - BPKH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .bg-bpkh-navy {
            background-color: #093566;
        }
        .text-bpkh-navy {
            color: #093566;
        }
        .border-bpkh-navy {
            border-color: #093566;
        }
        .focus-bpkh-navy:focus {
            border-color: #093566;
            ring-color: rgba(9, 53, 102, 0.2);
        }
        .bg-bpkh-gold {
            background-color: #e5b22c;
        }
        .hover-bpkh-gold:hover {
            background-color: #d1a120;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full space-y-8 bg-white border border-slate-200 p-8 rounded-3xl shadow-sm">
        <!-- Logo and Header -->
        <div class="text-center">
            <!-- BPKH Official Logo wrapped in navy blue container to make white text clearly visible -->
            <div class="w-full bg-bpkh-navy rounded-xl py-3.5 px-6 flex justify-center mb-6 shadow-md border border-slate-700">
                <img src="{{ asset('uploads/logos/bpkh_logo.png') }}" class="h-10 object-contain" alt="BPKH Logo Official">
            </div>
            <h2 class="text-xl font-extrabold text-slate-800">Daftar Akun Agen Haji</h2>
            <p class="mt-2 text-xs text-slate-400 font-semibold">Silakan lengkapi formulir pendaftaran mandiri di bawah ini.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="font-bold">⚠ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form class="mt-6 space-y-4" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                    class="appearance-none rounded-xl relative block w-full px-4 py-2.5 border border-slate-200 placeholder-slate-350 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none"
                    placeholder="Masukkan nama lengkap sesuai KTP">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                    class="appearance-none rounded-xl relative block w-full px-4 py-2.5 border border-slate-200 placeholder-slate-350 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none"
                    placeholder="nama@email.com atau gmail">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- WhatsApp Phone Number -->
                <div>
                    <label for="whatsapp_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nomor WhatsApp</label>
                    <input id="whatsapp_number" name="whatsapp_number" type="text" required value="{{ old('whatsapp_number') }}"
                        class="appearance-none rounded-xl relative block w-full px-4 py-2.5 border border-slate-200 placeholder-slate-350 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none font-mono"
                        placeholder="62812XXXXXXXX">
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">NIK (KTP)</label>
                    <input id="nik" name="nik" type="text" required maxlength="16" value="{{ old('nik') }}"
                        class="appearance-none rounded-xl relative block w-full px-4 py-2.5 border border-slate-200 placeholder-slate-350 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none font-mono"
                        placeholder="16 digit NIK KTP">
                </div>
            </div>

            <!-- Agent Type -->
            <div>
                <label for="type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jenis Keagenan</label>
                <select id="type" name="type" required
                    class="block w-full px-4 py-2.5 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none font-semibold">
                    <option value="freelance" {{ old('type') === 'freelance' ? 'selected' : '' }}>Agen Perorangan (Freelance)</option>
                    <option value="institution_new" {{ old('type') === 'institution_new' ? 'selected' : '' }}>Mitra Institusi Baru (B2B)</option>
                    <option value="institution_employee" {{ old('type') === 'institution_employee' ? 'selected' : '' }}>Agen Institusi / B2B</option>
                </select>
            </div>

            <!-- New Institution Fields -->
            <div id="section_institution_new" class="hidden space-y-3 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 text-bpkh-navy">Informasi Institusi Baru</h4>
                
                <div>
                    <label for="institution_name" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Institusi</label>
                    <input id="institution_name" name="institution_name" type="text"
                        class="appearance-none rounded-xl block w-full px-4 py-2 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 outline-none"
                        placeholder="Nama PT / KBIU / Lembaga">
                </div>

                <div>
                    <label for="institution_address" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Kantor</label>
                    <input id="institution_address" name="institution_address" type="text"
                        class="appearance-none rounded-xl block w-full px-4 py-2 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 outline-none"
                        placeholder="Alamat lengkap kantor pusat">
                </div>

                <div>
                    <label for="institution_legal_doc" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bukti Legalitas Kantor</label>
                    <input id="institution_legal_doc" name="institution_legal_doc" type="file" accept="image/*,application/pdf"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-bpkh-navy file:text-white hover:file:bg-bpkh-navy/90 cursor-pointer">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="institution_npwp" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">NPWP Institusi</label>
                        <input id="institution_npwp" name="institution_npwp" type="text"
                            class="appearance-none rounded-xl block w-full px-3 py-2 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 outline-none font-mono"
                            placeholder="Nomor NPWP">
                    </div>
                    <div>
                        <label for="institution_bank_account" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rekening Bank</label>
                        <input id="institution_bank_account" name="institution_bank_account" type="text"
                            class="appearance-none rounded-xl block w-full px-3 py-2 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 outline-none"
                            placeholder="Nama Bank & No Rekening">
                    </div>
                </div>

                <div>
                    <label for="institution_latitude" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kode Latitude Kantor</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input id="institution_latitude" name="institution_latitude" type="text" placeholder="-6.200000"
                            class="appearance-none rounded-xl block w-full px-3 py-2 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 outline-none font-mono">
                        <input id="institution_longitude" name="institution_longitude" type="text" placeholder="106.816666"
                            class="appearance-none rounded-xl block w-full px-3 py-2 border border-slate-200 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Existing Institution Selection -->
            <div id="section_institution_employee" class="hidden space-y-3 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 text-bpkh-navy">Afiliasi Institusi</h4>
                <div>
                    <label for="institution_id" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Institusi Induk</label>
                    <select id="institution_id" name="institution_id"
                        class="block w-full px-4 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:outline-none focus:border-bpkh-navy/50 outline-none font-semibold">
                        <option value="">-- Pilih Institusi Aktif --</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }} ({{ $inst->registration_number }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
                    <input id="password" name="password" type="password" required
                        class="appearance-none rounded-xl relative block w-full px-4 py-2.5 border border-slate-200 placeholder-slate-350 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none"
                        placeholder="Minimal 8 karakter">
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Ulangi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="appearance-none rounded-xl relative block w-full px-4 py-2.5 border border-slate-200 placeholder-slate-350 text-slate-800 text-xs focus:outline-none focus:border-bpkh-navy/50 focus:ring-2 focus:ring-bpkh-navy/15 transition-all outline-none"
                        placeholder="Konfirmasi password">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-xs font-bold rounded-xl text-white bg-bpkh-navy hover:bg-bpkh-navy/95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bpkh-navy shadow-sm transition-all cursor-pointer">
                    Daftar sebagai Agen Baru
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-[11px] font-bold text-slate-400 hover:text-bpkh-navy transition-colors">
                Sudah memiliki akun? Masuk di sini
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const secInstNew = document.getElementById('section_institution_new');
        const secInstEmp = document.getElementById('section_institution_employee');

        function toggleFields() {
            const val = typeSelect.value;
            if (val === 'institution_new') {
                secInstNew.classList.remove('hidden');
                secInstEmp.classList.add('hidden');
                setRequired(secInstNew, true);
                setRequired(secInstEmp, false);
            } else if (val === 'institution_employee') {
                secInstNew.classList.add('hidden');
                secInstEmp.classList.remove('hidden');
                setRequired(secInstNew, false);
                setRequired(secInstEmp, true);
            } else {
                secInstNew.classList.add('hidden');
                secInstEmp.classList.add('hidden');
                setRequired(secInstNew, false);
                setRequired(secInstEmp, false);
            }
        }

        function setRequired(container, required) {
            const inputs = container.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (required) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Run once on load
    });
    </script>
</body>
</html>
