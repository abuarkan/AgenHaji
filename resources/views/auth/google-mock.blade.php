<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in with Google - Sandbox Developer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f0f4f9;
        }
        .google-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e3e3;
        }
        .avatar {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
        }
        .account-item {
            transition: background-color 0.15s ease-in-out;
        }
        .account-item:hover {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 antialiased font-sans">
    <div class="w-full max-w-[450px]">
        <!-- Main Google Consent Box -->
        <div class="google-card p-10 flex flex-col items-center">
            
            <!-- Google Logo SVG -->
            <svg class="h-8 mb-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>

            <h1 class="text-xl font-medium text-slate-800 tracking-tight text-center">Pilih akun</h1>
            <p class="text-slate-500 text-xs mt-1.5 mb-6 text-center">untuk melanjutkan ke <span class="font-semibold text-bpkh-navy">BPKH Hajj Agent</span></p>

            <div class="w-full flex flex-col border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100 mb-6">
                <!-- Demo Account 1 -->
                <button type="button" onclick="selectAccount('ahmad@gmail.com', 'Ahmad Agent')"
                    class="account-item w-full flex items-center gap-3.5 p-3.5 text-left cursor-pointer border-none bg-transparent outline-none">
                    <div class="avatar bg-emerald-600">AH</div>
                    <div>
                        <span class="block text-xs font-bold text-slate-700 leading-none">Ahmad Agent</span>
                        <span class="block text-[10px] text-slate-400 mt-1 font-medium font-mono">ahmad@gmail.com</span>
                    </div>
                </button>

                <!-- Demo Account 2 -->
                <button type="button" onclick="selectAccount('kbiu@travel.com', 'KBIU Travel')"
                    class="account-item w-full flex items-center gap-3.5 p-3.5 text-left cursor-pointer border-none bg-transparent outline-none">
                    <div class="avatar bg-sky-600">KB</div>
                    <div>
                        <span class="block text-xs font-bold text-slate-700 leading-none">KBIU Travel</span>
                        <span class="block text-[10px] text-slate-400 mt-1 font-medium font-mono">kbiu@travel.com</span>
                    </div>
                </button>

                <!-- Demo Account 3 -->
                <button type="button" onclick="selectAccount('superadmin@bpkh.go.id', 'Superadmin BPKH')"
                    class="account-item w-full flex items-center gap-3.5 p-3.5 text-left cursor-pointer border-none bg-transparent outline-none">
                    <div class="avatar bg-amber-600">SA</div>
                    <div>
                        <span class="block text-xs font-bold text-slate-700 leading-none">Superadmin BPKH</span>
                        <span class="block text-[10px] text-slate-400 mt-1 font-medium font-mono">superadmin@bpkh.go.id</span>
                    </div>
                </button>
            </div>

            <!-- Custom Account Form -->
            <form action="{{ route('auth.google.mock.submit') }}" method="POST" class="w-full flex flex-col gap-4 border-t border-slate-100 pt-5">
                @csrf
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Atau Masuk dengan Akun Baru:</div>
                
                <div>
                    <label for="name" class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required placeholder="Google User"
                        class="w-full bg-white border border-slate-200 focus:border-blue-500 rounded-lg px-3 py-2 text-slate-800 placeholder-slate-400 text-xs outline-none transition-all">
                </div>

                <div>
                    <label for="email" class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Email Google</label>
                    <input type="email" name="email" id="email" required placeholder="nama@gmail.com"
                        class="w-full bg-white border border-slate-200 focus:border-blue-500 rounded-lg px-3 py-2 text-slate-800 placeholder-slate-400 text-xs outline-none transition-all">
                </div>

                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all cursor-pointer text-xs uppercase">
                    Masuk Sandbox
                </button>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 transition-all hover:underline">
                Batal dan Kembali ke Login
            </a>
        </div>
    </div>

    <!-- Hidden form to process click-account submission -->
    <form id="directSelectForm" action="{{ route('auth.google.mock.submit') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="email" id="directEmail">
        <input type="hidden" name="name" id="directName">
    </form>

    <script>
        function selectAccount(email, name) {
            document.getElementById('directEmail').value = email;
            document.getElementById('directName').value = name;
            document.getElementById('directSelectForm').submit();
        }
    </script>
</body>
</html>
