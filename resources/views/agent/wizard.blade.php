@extends('layouts.app')

@section('title', 'Pendaftaran Verifikasi')

@section('sidebar-nav')
    <a href="#" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg text-bpkh-navy bg-bpkh-navy/5 border border-bpkh-navy/15 cursor-default">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Pendaftaran & Verifikasi Agen</span>
    </a>
@endsection

@section('content')
<div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm mb-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-extrabold text-slate-800">{{ $agent->full_name ?? Auth::user()->name }}</h1>
                <span class="px-2 py-0.5 text-[10px] font-black rounded bg-slate-100 text-slate-500 border border-slate-200">Belum Terverifikasi</span>
            </div>
            <div class="flex items-center gap-3 mt-1 text-[11px] font-bold text-slate-400">
                <span>Role: Agen</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <span>Tingkat: {{ $agent->level->name ?? 'Silver' }}</span>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Email</span>
                <span class="text-xs font-bold text-slate-700">{{ Auth::user()->email }}</span>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nomor Handphone</span>
                <span class="text-xs font-bold text-slate-700">{{ $agent->whatsapp_number }}</span>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[120px]">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">NIK</span>
                <span class="text-xs font-bold text-slate-700">{{ $agent->nik }}</span>
            </div>
        </div>
    </div>

    <!-- Alert / Status -->
    <div class="mt-5">
        <div class="flex items-start gap-3 p-4 bg-slate-100 border border-slate-200 text-slate-600 rounded-2xl shadow-sm text-xs font-semibold mb-6">
            <svg class="w-5 h-5 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                @if($agent->is_submitted)
                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                        <span>Pendaftaran Anda telah dikirim dan sedang menunggu verifikasi oleh administrator Institusi Anda. Silakan periksa kembali pratinjau dokumen Anda di bawah ini.</span>
                    @else
                        <span>Pendaftaran Anda telah dikirim dan sedang menunggu verifikasi oleh administrator BPKH. Silakan periksa kembali pratinjau dokumen Anda di bawah ini.</span>
                    @endif
                @else
                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                        <span>Anda belum terverifikasi sebagai Agen Haji. Lakukan verifikasi dengan mengunggah dokumen-dokumen yang diperlukan. Lengkapi form pada halaman ini agar dapat diproses oleh admin Institusi Anda.</span>
                    @else
                        <span>Anda belum terverifikasi sebagai Agen Haji. Lakukan verifikasi dengan mengunggah dokumen-dokumen yang diperlukan. Lengkapi form pada halaman ini agar dapat diproses oleh admin BPKH.</span>
                    @endif
                @endif
            </div>
        </div>

        <!-- Steps Wizard Navigator Bar -->
        <div class="flex items-center justify-center max-w-3xl mx-auto mb-8 relative">
            <div class="absolute left-0 right-0 top-1/2 h-[2px] bg-slate-200 -translate-y-1/2 z-0"></div>
            
            <div class="z-10 bg-white px-3 flex flex-col items-center gap-1">
                <div id="step-circle-1" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all bg-bpkh-navy text-white shadow">1</div>
                <span id="step-label-1" class="text-[10px] font-bold text-slate-700 uppercase tracking-wider mt-1">Profil & Alamat</span>
            </div>
            
            <div class="flex-grow"></div>
            
            <div class="z-10 bg-white px-3 flex flex-col items-center gap-1">
                <div id="step-circle-2" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all bg-slate-200 text-slate-500">2</div>
                <span id="step-label-2" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Dokumen Wajib</span>
            </div>
            
            <div class="flex-grow"></div>
            
            <div class="z-10 bg-white px-3 flex flex-col items-center gap-1">
                <div id="step-circle-3" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all bg-slate-200 text-slate-500">3</div>
                <span id="step-label-3" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Pratinjau</span>
            </div>
        </div>

        <!-- Wizard Form -->
        <form id="wizardForm" action="{{ route('agent.verification.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude_tinggal" id="latitude_tinggal" value="-6.200000">
            <input type="hidden" name="longitude_tinggal" id="longitude_tinggal" value="106.816666">

            <!-- STEP 1 PANEL -->
            <div id="panel-step-1" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Column 1 -->
                <div class="space-y-4">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">KTP & Info Diri</span>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto KTP*</label>
                        <input type="file" name="foto_ktp" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png maksimal 3MB)</span>
                    </div>

                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nama Institusi</label>
                            <input type="text" readonly value="{{ $agent->institution->name ?? '-' }}" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-500 text-xs outline-none font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nomor Induk Pegawai (NIP)*</label>
                            <input type="text" name="nip" required value="{{ old('nip', $agent->nip) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-mono font-bold" placeholder="Masukkan NIP Anda">
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nama Lengkap*</label>
                        <input type="text" name="nama_lengkap" required value="{{ old('nama_lengkap', $agent->full_name ?? Auth::user()->name) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Nama lengkap sesuai KTP">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Jenis Kelamin*</label>
                            <select name="jenis_kelamin" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all font-bold">
                                <option value="Pria" {{ old('jenis_kelamin', $agent->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $agent->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Tempat Lahir*</label>
                            <input type="text" name="tempat_lahir" required value="{{ old('tempat_lahir', $agent->tempat_lahir) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Tempat lahir sesuai KTP">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Tanggal Lahir*</label>
                        <input type="date" name="tanggal_lahir" required value="{{ old('tanggal_lahir', $agent->tanggal_lahir ? \Carbon\Carbon::parse($agent->tanggal_lahir)->format('Y-m-d') : '') }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Alamat KTP*</label>
                        <input type="text" name="alamat_ktp" required value="{{ old('alamat_ktp', $agent->alamat_ktp) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Jalan, RT/RW sesuai KTP">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Provinsi*</label>
                            <select name="provinsi_ktp" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="DKI Jakarta" {{ old('provinsi_ktp', $agent->provinsi_ktp) === 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                                <option value="Jawa Barat" {{ old('provinsi_ktp', $agent->provinsi_ktp) === 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                                <option value="Jawa Tengah" {{ old('provinsi_ktp', $agent->provinsi_ktp) === 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
                                <option value="Jawa Timur" {{ old('provinsi_ktp', $agent->provinsi_ktp) === 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Kota/Kabupaten*</label>
                            <select name="kota_ktp" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="Jakarta Selatan" {{ old('kota_ktp', $agent->kota_ktp) === 'Jakarta Selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
                                <option value="Bandung" {{ old('kota_ktp', $agent->kota_ktp) === 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                <option value="Semarang" {{ old('kota_ktp', $agent->kota_ktp) === 'Semarang' ? 'selected' : '' }}>Semarang</option>
                                <option value="Surabaya" {{ old('kota_ktp', $agent->kota_ktp) === 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Kecamatan*</label>
                            <select name="kecamatan_ktp" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="Kebayoran Baru" {{ old('kecamatan_ktp', $agent->kecamatan_ktp) === 'Kebayoran Baru' ? 'selected' : '' }}>Kebayoran Baru</option>
                                <option value="Coblong" {{ old('kecamatan_ktp', $agent->kecamatan_ktp) === 'Coblong' ? 'selected' : '' }}>Coblong</option>
                                <option value="Tembalang" {{ old('kecamatan_ktp', $agent->kecamatan_ktp) === 'Tembalang' ? 'selected' : '' }}>Tembalang</option>
                                <option value="Tegalsari" {{ old('kecamatan_ktp', $agent->kecamatan_ktp) === 'Tegalsari' ? 'selected' : '' }}>Tegalsari</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Desa/Kelurahan*</label>
                            <select name="kelurahan_ktp" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="Senayan" {{ old('kelurahan_ktp', $agent->kelurahan_ktp) === 'Senayan' ? 'selected' : '' }}>Senayan</option>
                                <option value="Dago" {{ old('kelurahan_ktp', $agent->kelurahan_ktp) === 'Dago' ? 'selected' : '' }}>Dago</option>
                                <option value="Bulusan" {{ old('kelurahan_ktp', $agent->kelurahan_ktp) === 'Bulusan' ? 'selected' : '' }}>Bulusan</option>
                                <option value="Kedungdoro" {{ old('kelurahan_ktp', $agent->kelurahan_ktp) === 'Kedungdoro' ? 'selected' : '' }}>Kedungdoro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="space-y-4 border-l border-slate-100 pl-0 lg:pl-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="block text-xs font-black text-slate-800 uppercase tracking-wider">Alamat Tinggal</span>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" id="same_address_check" class="rounded text-bpkh-navy border-slate-300 focus:ring-bpkh-navy/20">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Alamat domisili sama dengan KTP</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Alamat Domisili*</label>
                        <input type="text" name="alamat_tinggal" id="alamat_tinggal" required value="{{ old('alamat_tinggal', $agent->alamat_tinggal) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Alamat lengkap tinggal saat ini">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Provinsi*</label>
                            <select name="provinsi_tinggal" id="provinsi_tinggal" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="DKI Jakarta" {{ old('provinsi_tinggal', $agent->provinsi_tinggal) === 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                                <option value="Jawa Barat" {{ old('provinsi_tinggal', $agent->provinsi_tinggal) === 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                                <option value="Jawa Tengah" {{ old('provinsi_tinggal', $agent->provinsi_tinggal) === 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
                                <option value="Jawa Timur" {{ old('provinsi_tinggal', $agent->provinsi_tinggal) === 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Kota/Kabupaten*</label>
                            <select name="kota_tinggal" id="kota_tinggal" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="Jakarta Selatan" {{ old('kota_tinggal', $agent->kota_tinggal) === 'Jakarta Selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
                                <option value="Bandung" {{ old('kota_tinggal', $agent->kota_tinggal) === 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                <option value="Semarang" {{ old('kota_tinggal', $agent->kota_tinggal) === 'Semarang' ? 'selected' : '' }}>Semarang</option>
                                <option value="Surabaya" {{ old('kota_tinggal', $agent->kota_tinggal) === 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Kecamatan*</label>
                            <select name="kecamatan_tinggal" id="kecamatan_tinggal" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="Kebayoran Baru" {{ old('kecamatan_tinggal', $agent->kecamatan_tinggal) === 'Kebayoran Baru' ? 'selected' : '' }}>Kebayoran Baru</option>
                                <option value="Coblong" {{ old('kecamatan_tinggal', $agent->kecamatan_tinggal) === 'Coblong' ? 'selected' : '' }}>Coblong</option>
                                <option value="Tembalang" {{ old('kecamatan_tinggal', $agent->kecamatan_tinggal) === 'Tembalang' ? 'selected' : '' }}>Tembalang</option>
                                <option value="Tegalsari" {{ old('kecamatan_tinggal', $agent->kecamatan_tinggal) === 'Tegalsari' ? 'selected' : '' }}>Tegalsari</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Desa/Kelurahan*</label>
                            <select name="kelurahan_tinggal" id="kelurahan_tinggal" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all">
                                <option value="Senayan" {{ old('kelurahan_tinggal', $agent->kelurahan_tinggal) === 'Senayan' ? 'selected' : '' }}>Senayan</option>
                                <option value="Dago" {{ old('kelurahan_tinggal', $agent->kelurahan_tinggal) === 'Dago' ? 'selected' : '' }}>Dago</option>
                                <option value="Bulusan" {{ old('kelurahan_tinggal', $agent->kelurahan_tinggal) === 'Bulusan' ? 'selected' : '' }}>Bulusan</option>
                                <option value="Kedungdoro" {{ old('kelurahan_tinggal', $agent->kelurahan_tinggal) === 'Kedungdoro' ? 'selected' : '' }}>Kedungdoro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Column 3 -->
                <div class="space-y-4 border-l border-slate-100 pl-0 lg:pl-6">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Dokumen Unggahan</span>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto Bangunan (Kantor/Lokasi)*</label>
                        <input type="file" name="foto_bangunan" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto Diri*</label>
                        <input type="file" name="foto_diri" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Pakta Integritas*</label>
                        <input type="file" name="foto_pakta_integritas" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png maksimal 3MB)</span>
                        <a href="#" class="text-[10px] font-bold text-bpkh-navy hover:underline block mt-1.5">Download Template Pakta Integritas</a>
                    </div>

                    @if($agent->type === 'institution' && !$agent->is_institution_admin)
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto Bukti Pekerja*</label>
                        <input type="file" name="bukti_pekerja" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">ID Card / SK Pengangkatan*</label>
                        <input type="file" name="sk_pengangkatan" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(.pdf/.jpeg/.jpg/.png maksimal 3MB)</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- STEP 2 PANEL -->
            <div id="panel-step-2" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bank Info -->
                <div class="space-y-4">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Buku Tabungan</span>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Buku Tabungan (Belakang Sampul)*</label>
                        <input type="file" name="foto_buku_tabungan" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(jpeg/jpg/png maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nama Bank Syariah*</label>
                        <select name="nama_bank" required class="block w-full px-3 py-2 border border-slate-200 bg-white text-slate-800 text-xs rounded-xl focus:border-bpkh-navy/50 outline-none transition-all font-semibold">
                            <option value="Bank Syariah Indonesia" {{ old('nama_bank', $agent->nama_bank) === 'Bank Syariah Indonesia' ? 'selected' : '' }}>Bank Syariah Indonesia</option>
                            <option value="Bank Muamalat" {{ old('nama_bank', $agent->nama_bank) === 'Bank Muamalat' ? 'selected' : '' }}>Bank Muamalat</option>
                            <option value="Bank Mega Syariah" {{ old('nama_bank', $agent->nama_bank) === 'Bank Mega Syariah' ? 'selected' : '' }}>Bank Mega Syariah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Cabang Bank*</label>
                        <input type="text" name="cabang_bank" required value="{{ old('cabang_bank', $agent->cabang_bank) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all" placeholder="Masukkan Cabang Bank">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nomor Rekening*</label>
                        <input type="text" name="nomor_rekening" required value="{{ old('nomor_rekening', $agent->nomor_rekening) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-mono" placeholder="Masukkan nomor rekening">
                    </div>
                </div>

                <!-- NPWP Info -->
                <div class="space-y-4 border-l border-slate-100 pl-0 md:pl-6">
                    <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Nomor NPWP</span>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Foto NPWP*</label>
                        <input type="file" name="foto_npwp" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">(jpeg/jpg/png maksimal 3MB)</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Nomor NPWP*</label>
                        <input type="text" name="nomor_npwp" required value="{{ old('nomor_npwp', $agent->nomor_npwp) }}" class="w-full bg-white border border-slate-200 focus:border-bpkh-navy/50 rounded-xl px-3 py-2 text-slate-800 text-xs outline-none transition-all font-mono" placeholder="Masukkan nomor NPWP">
                    </div>
                </div>
            </div>

            <!-- STEP 3 PANEL (PREVIEW) -->
            <div id="panel-step-3" class="hidden space-y-6">
                <!-- Preview details grid -->
                <div class="bg-white border border-slate-200 p-6 rounded-2xl">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Step 1 Details -->
                        <div class="space-y-4">
                            <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-1.5">Informasi Profile dan Alamat Lokasi</span>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">KTP File</span>
                                    <span class="font-bold text-bpkh-navy flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span id="preview-ktp-filename">ktp_ahmad.pdf</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Lengkap</span>
                                    <span id="preview-nama" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tanggal Lahir</span>
                                    <span id="preview-lahir" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Alamat Domisili</span>
                                    <span id="preview-alamat-domisili" class="font-bold text-slate-700">-</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Alamat Sesuai KTP</span>
                                    <span id="preview-alamat-ktp" class="font-semibold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Foto Bangunan (Kantor/Lokasi)</span>
                                    <span class="font-bold text-bpkh-navy flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span>Ruko_Cempaka_Mas.jpeg</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Foto Diri</span>
                                    <span class="font-bold text-bpkh-navy flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Ahmed_selfie.jpeg</span>
                                    </span>
                                </div>
                                @if($agent->type === 'institution' && !$agent->is_institution_admin)
                                <div class="col-span-2 mt-3 pt-3 border-t border-slate-150 grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Institusi</span>
                                        <span class="font-bold text-slate-700">{{ $agent->institution->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">NIP</span>
                                        <span id="preview-nip" class="font-bold text-slate-700 font-mono">{{ $agent->nip ?? '-' }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Step 2 Details -->
                        <div class="space-y-4 border-l border-slate-100 pl-0 lg:pl-6">
                            <span class="block text-xs font-black text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-1.5">Dokumen Wajib</span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Buku Tabungan</span>
                                    <span class="font-bold text-bpkh-navy flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>bank_ikhlas_ahmad.pdf</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Bank Syariah</span>
                                    <span id="preview-bank" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Cabang Bank</span>
                                    <span id="preview-cabang" class="font-bold text-slate-700">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor Rekening</span>
                                    <span id="preview-rekening" class="font-bold text-slate-700 font-mono">-</span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Foto NPWP</span>
                                    <span class="font-bold text-bpkh-navy flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>npwp_ahmad.jpeg</span>
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor NPWP</span>
                                    <span id="preview-npwp" class="font-bold text-slate-700 font-mono">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert -->
                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs font-semibold">
                    <div class="flex items-center gap-2">
                        <span class="text-amber-600">⚠</span>
                        <span>Mohon pastikan dokumen Anda sudah lengkap dan benar.</span>
                    </div>
                </div>
            </div>

            <!-- BUTTON CONTROLS -->
            <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-5">
                <button type="button" id="prevBtn" onclick="nextPrev(-1)" class="px-5 py-2 text-xs font-bold rounded-xl text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all cursor-pointer hidden">
                    Kembali
                </button>
                <div class="flex-grow"></div>
                
                @if(!$agent->is_submitted)
                    <button type="button" id="nextBtn" onclick="nextPrev(1)" class="px-6 py-2.5 text-xs font-bold rounded-xl text-white bg-bpkh-navy hover:bg-bpkh-navy/95 transition-all shadow cursor-pointer">
                        Lanjutkan
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- MODAL CONFIRMATION -->
<div id="confirmModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 hidden transition-opacity">
    <div class="bg-white border border-slate-200 p-6 rounded-3xl max-w-sm w-full mx-4 shadow-xl">
        <div class="w-12 h-12 rounded-full bg-bpkh-navy/10 flex items-center justify-center text-bpkh-navy mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-base font-extrabold text-slate-800">Verifikasi Agen Haji</h3>
        <p class="text-slate-500 text-xs font-medium mt-1.5">Apakah Anda yakin informasi dan dokumen yang Anda masukkan sudah benar?</p>
        <div class="flex items-center gap-3 mt-6">
            <button type="button" onclick="closeConfirmModal()" class="flex-1 py-2 text-xs font-bold rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="submitForm()" class="flex-1 py-2 text-xs font-bold rounded-xl text-white bg-bpkh-navy hover:bg-bpkh-navy/95 cursor-pointer">
                Ya, Kirim
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentTab = 0;
    const isSubmitted = {{ $agent->is_submitted ? 'true' : 'false' }};

    if (isSubmitted) {
        currentTab = 2;
    }

    showTab(currentTab);

    function showTab(n) {
        const tabs = ["panel-step-1", "panel-step-2", "panel-step-3"];
        document.getElementById(tabs[0]).classList.add("hidden");
        document.getElementById(tabs[1]).classList.add("hidden");
        document.getElementById(tabs[2]).classList.add("hidden");
        document.getElementById(tabs[n]).classList.remove("hidden");

        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        if (n === 0) {
            prevBtn.classList.add("hidden");
        } else {
            prevBtn.classList.remove("hidden");
        }

        if (nextBtn) {
            if (n === (tabs.length - 1)) {
                nextBtn.innerHTML = "Kirim Verifikasi";
                nextBtn.classList.remove("bg-bpkh-navy");
                nextBtn.classList.add("bg-bpkh-gold");
            } else {
                nextBtn.innerHTML = "Lanjutkan";
                nextBtn.classList.add("bg-bpkh-navy");
                nextBtn.classList.remove("bg-bpkh-gold");
            }
        }

        updateStepIndicators(n);

        if (n === 2) {
            generatePreview();
        }
    }

    function nextPrev(n) {
        const tabs = ["panel-step-1", "panel-step-2", "panel-step-3"];
        if (n === 1 && !validateForm()) return false;

        currentTab = currentTab + n;

        if (currentTab >= tabs.length) {
            openConfirmModal();
            currentTab = tabs.length - 1;
            return false;
        }

        showTab(currentTab);
    }

    function validateForm() {
        if (isSubmitted) return true;
        const tabPanels = ["panel-step-1", "panel-step-2", "panel-step-3"];
        const currentPanel = document.getElementById(tabPanels[currentTab]);
        const inputs = currentPanel.querySelectorAll("input[required], select[required]");
        let valid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add("border-red-450");
                valid = false;
            } else {
                input.classList.remove("border-red-455");
            }
        });

        if (!valid) {
            alert("Harap lengkapi semua kolom bertanda bintang (*).");
        }
        return valid;
    }

    function updateStepIndicators(n) {
        for (let i = 1; i <= 3; i++) {
            const circle = document.getElementById("step-circle-" + i);
            const label = document.getElementById("step-label-" + i);

            if (i <= n + 1) {
                circle.classList.add("bg-bpkh-navy", "text-white", "shadow");
                circle.classList.remove("bg-slate-200", "text-slate-500");
                label.classList.add("text-slate-700");
                label.classList.remove("text-slate-400");
            } else {
                circle.classList.remove("bg-bpkh-navy", "text-white", "shadow");
                circle.classList.add("bg-slate-200", "text-slate-500");
                label.classList.remove("text-slate-700");
                label.classList.add("text-slate-400");
            }
        }
    }

    function generatePreview() {
        const form = document.getElementById("wizardForm");
        
        document.getElementById("preview-nama").innerText = form.elements["nama_lengkap"].value;
        document.getElementById("preview-lahir").innerText = form.elements["tempat_lahir"].value + ", " + form.elements["tanggal_lahir"].value;
        
        const jKtp = [
            form.elements["alamat_ktp"].value,
            form.elements["kelurahan_ktp"].value,
            form.elements["kecamatan_ktp"].value,
            form.elements["kota_ktp"].value,
            form.elements["provinsi_ktp"].value
        ].filter(Boolean).join(", ");
        document.getElementById("preview-alamat-ktp").innerText = jKtp;

        const jDom = [
            form.elements["alamat_tinggal"].value,
            form.elements["kelurahan_tinggal"].value,
            form.elements["kecamatan_tinggal"].value,
            form.elements["kota_tinggal"].value,
            form.elements["provinsi_tinggal"].value
        ].filter(Boolean).join(", ");
        document.getElementById("preview-alamat-domisili").innerText = jDom;

        document.getElementById("preview-bank").innerText = form.elements["nama_bank"].value;
        document.getElementById("preview-cabang").innerText = form.elements["cabang_bank"].value;
        document.getElementById("preview-rekening").innerText = form.elements["nomor_rekening"].value;
        document.getElementById("preview-npwp").innerText = form.elements["nomor_npwp"].value;

        if (form.elements["nip"]) {
            document.getElementById("preview-nip").innerText = form.elements["nip"].value;
        }

        // Try to show filenames
        const ktpInput = form.elements["foto_ktp"];
        if (ktpInput && ktpInput.files && ktpInput.files[0]) {
            document.getElementById("preview-ktp-filename").innerText = ktpInput.files[0].name;
        } else {
            document.getElementById("preview-ktp-filename").innerText = "{{ basename($agent->foto_ktp) ?: 'ktp_ahmad.pdf' }}";
        }
    }

    document.getElementById("same_address_check").addEventListener("change", function(e) {
        if (e.target.checked) {
            const form = document.getElementById("wizardForm");
            form.elements["alamat_tinggal"].value = form.elements["alamat_ktp"].value;
            form.elements["provinsi_tinggal"].value = form.elements["provinsi_ktp"].value;
            form.elements["kota_tinggal"].value = form.elements["kota_ktp"].value;
            form.elements["kecamatan_tinggal"].value = form.elements["kecamatan_ktp"].value;
            form.elements["kelurahan_tinggal"].value = form.elements["kelurahan_ktp"].value;
        }
    });

    function openConfirmModal() {
        document.getElementById("confirmModal").classList.remove("hidden");
    }

    function closeConfirmModal() {
        document.getElementById("confirmModal").classList.add("hidden");
    }

    function submitForm() {
        closeConfirmModal();
        document.getElementById("wizardForm").submit();
    }
</script>
@endsection
