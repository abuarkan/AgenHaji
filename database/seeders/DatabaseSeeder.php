<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Superadmin and Test Agent Users
        $superadminUser = User::create([
            'name' => 'Superadmin BPKH',
            'email' => 'superadmin@bpkh.go.id',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        $adminHajiUser = User::create([
            'name' => 'Admin Agen Haji BPKH',
            'email' => 'admin.haji@bpkh.go.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_haji',
        ]);

        $agentUser1 = User::create([
            'name' => 'Muhammad Aidul R.',
            'email' => 'ahmad@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'agent',
        ]);

        $agentUser2 = User::create([
            'name' => 'KBIU Hajj Travel',
            'email' => 'kbiu@travel.com',
            'password' => Hash::make('password123'),
            'role' => 'agent',
        ]);

        // 2. Seed Agent Levels (Silver, Gold, Platinum, Diamond)
        $silver = DB::table('agent_levels')->insertGetId([
            'name' => 'Silver',
            'target_prospects' => 20,
            'commission_per_prospect' => 50000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gold = DB::table('agent_levels')->insertGetId([
            'name' => 'Gold',
            'target_prospects' => 26,
            'commission_per_prospect' => 75000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $platinum = DB::table('agent_levels')->insertGetId([
            'name' => 'Platinum',
            'target_prospects' => 31,
            'commission_per_prospect' => 100000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $diamond = DB::table('agent_levels')->insertGetId([
            'name' => 'Diamond',
            'target_prospects' => 51,
            'commission_per_prospect' => 125000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Institutions (B2B Partner)
        $instId = DB::table('institutions')->insertGetId([
            'name' => 'PT Maju Jaya Haji',
            'registration_number' => '12.34.56.789',
            'address' => 'Jl. Sudirman No. 45, Jakarta',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed Agents
        // Freelance Agent
        DB::table('agents')->insert([
            'user_id' => $agentUser1->id,
            'institution_id' => null,
            'agent_level_id' => $gold, // Start at Gold
            'referral_code' => 'ALRM2002',
            'nik' => '3171012345670001',
            'whatsapp_number' => '6281234567890',
            'type' => 'freelance',
            'status' => 'active',
            'is_email_verified' => true,
            'is_whatsapp_verified' => true,
            'is_ktp_verified' => true,
            'full_name' => 'Muhammad Aidul R.',
            'birth_date' => '1995-05-12',
            'alamat_ktp' => 'Jalan Kenangan Blok Z RT/RW 01/01',
            'provinsi_ktp' => 'Kalimantan Utara',
            'kota_ktp' => 'Tarakan',
            'kecamatan_ktp' => 'Bunyu',
            'kelurahan_ktp' => 'Nama Desa',
            'alamat_tinggal' => 'Jalan Kenangan Blok Z RT/RW 01/01',
            'provinsi_tinggal' => 'Kalimantan Utara',
            'kota_tinggal' => 'Tarakan',
            'kecamatan_tinggal' => 'Bunyu',
            'kelurahan_tinggal' => 'Nama Desa',
            'foto_ktp' => 'ktp_ahmad.pdf',
            'foto_diri' => 'Aidul_selfie.jpeg',
            'foto_bangunan' => 'Ruko_Cempaka_Mas.jpeg',
            'foto_buku_tabungan' => 'bank_ikhlas_Aidul.pdf',
            'nama_bank' => 'Bank Syariah Indonesia',
            'nomor_rekening' => '987654321000',
            'cabang_bank' => 'Bunyu',
            'nomor_npwp' => '17500012345678',
            'foto_npwp' => 'npwp_aidul.jpeg',
            'latitude_tinggal' => -6.200000,
            'longitude_tinggal' => 106.816666,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Institutional Agent
        DB::table('agents')->insert([
            'user_id' => $agentUser2->id,
            'institution_id' => $instId,
            'agent_level_id' => $silver, // Start at Silver
            'referral_code' => 'BPKH-KBIU001',
            'nik' => '3171012345670002',
            'whatsapp_number' => '6289876543210',
            'type' => 'institution',
            'status' => 'active',
            'is_email_verified' => true,
            'is_whatsapp_verified' => true,
            'is_ktp_verified' => true,
            'full_name' => 'KBIU Hajj Travel Utama',
            'birth_date' => '1988-10-20',
            'alamat_ktp' => 'Jl. Sudirman No. 45',
            'provinsi_ktp' => 'DKI Jakarta',
            'kota_ktp' => 'Jakarta Selatan',
            'kecamatan_ktp' => 'Kebayoran Baru',
            'kelurahan_ktp' => 'Senayan',
            'alamat_tinggal' => 'Jl. Sudirman No. 45',
            'provinsi_tinggal' => 'DKI Jakarta',
            'kota_tinggal' => 'Jakarta Selatan',
            'kecamatan_tinggal' => 'Kebayoran Baru',
            'kelurahan_tinggal' => 'Senayan',
            'foto_ktp' => 'ktp_kbiu.pdf',
            'foto_diri' => 'kbiu_selfie.jpeg',
            'foto_bangunan' => 'Ruko_Cempaka_Mas.jpeg',
            'foto_buku_tabungan' => 'bank_syariah_kbiu.pdf',
            'nama_bank' => 'Bank Syariah Indonesia',
            'nomor_rekening' => '987654321111',
            'cabang_bank' => 'Sudirman Jakarta',
            'nomor_npwp' => '17500012345999',
            'foto_npwp' => 'npwp_kbiu.jpeg',
            'latitude_tinggal' => -6.175110,
            'longitude_tinggal' => 106.827170,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Seed System Settings (Dynamic Settings & API Credentials)
        DB::table('system_settings')->insert([
            [
                'key' => 'dukcapil_api_url',
                'value' => 'https://api.dukcapil.go.id/v1/verify',
                'is_encrypted' => false,
                'description' => 'URL API verifikasi KTP/NIK Dukcapil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'dukcapil_api_key',
                'value' => 'secret_dukcapil_key_bpkh',
                'is_encrypted' => true,
                'description' => 'API Key untuk autentikasi Dukcapil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_gateway_server_key',
                'value' => 'SB-Mid-server-BPKH2025Secret',
                'is_encrypted' => true,
                'description' => 'Midtrans/Xendit Server Key untuk pencairan dana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_api_token',
                'value' => 'EAAG3yZC3ZB2wYBA...',
                'is_encrypted' => true,
                'description' => 'WhatsApp Cloud API token untuk OTP & notifikasi',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 6. Seed prospects (Jemaah) and commissions for Agent Muhammad Aidul R.
        $agentId = DB::table('agents')->where('referral_code', 'ALRM2002')->value('id');

        $prospects = [
            [
                'agent_id' => $agentId,
                'name' => 'Ahmad Kenter',
                'nik' => '3171012345670010',
                'address' => 'Jl. Kebon Sirih No. 12, Jakarta Pusat',
                'email' => 'onguyen@gmail.com',
                'phone_number' => '081234567890',
                'registration_type' => '-',
                'bps_bpih' => '-',
                'status_pendaftaran' => 'Tertarik Daftar Haji',
                'claim_status' => '-',
                'porsi_number' => '-',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Savannah Nguyen',
                'nik' => '3171012345670011',
                'address' => 'Jl. Thamrin No. 89, Jakarta Pusat',
                'email' => 'SavannaN@gmail.com',
                'phone_number' => '081234567890',
                'registration_type' => '-',
                'bps_bpih' => '-',
                'status_pendaftaran' => '-',
                'claim_status' => '-',
                'porsi_number' => '-',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Henry Adams',
                'nik' => '3171012345670012',
                'address' => 'Jl. Sudirman No. 5, Jakarta Selatan',
                'email' => 'cbrown@yahoo.com',
                'phone_number' => '081234567890',
                'registration_type' => 'Reguler',
                'bps_bpih' => 'CIMB Niaga Syariah',
                'status_pendaftaran' => 'Pendaftar Haji',
                'claim_status' => 'Disetujui',
                'porsi_number' => '12341234123412',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'David Brewster',
                'nik' => '3171012345670013',
                'address' => 'Jl. Gatot Subroto No. 24, Jakarta Selatan',
                'email' => 'Yantok123@gmail.com',
                'phone_number' => '', // Empty for badge
                'registration_type' => 'Khusus',
                'bps_bpih' => 'Bank MEGA Syariah',
                'status_pendaftaran' => 'Pendaftar Haji',
                'claim_status' => 'Ditolak',
                'porsi_number' => '12341234123412',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Donnie Yen',
                'nik' => '3171012345670014',
                'address' => 'Jl. Gajah Mada No. 100, Jakarta Barat',
                'email' => 'agarcia@aol.com',
                'phone_number' => '081234567890',
                'registration_type' => '-',
                'bps_bpih' => '-',
                'status_pendaftaran' => '-',
                'claim_status' => '-',
                'porsi_number' => '-',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Hayden McGregor',
                'nik' => '3171012345670015',
                'address' => 'Jl. Hayam Wuruk No. 50, Jakarta Barat',
                'email' => 'slopez@yahoo.com',
                'phone_number' => '081234567890',
                'registration_type' => '-',
                'bps_bpih' => '-',
                'status_pendaftaran' => 'Tertarik Daftar Haji',
                'claim_status' => '-',
                'porsi_number' => '-',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Ann Rosser',
                'nik' => '3171012345670016',
                'address' => 'Jl. Rasuna Said No. 15, Jakarta Selatan',
                'email' => 'agarcia@aol.com',
                'phone_number' => '', // Empty for badge
                'registration_type' => '-',
                'bps_bpih' => '-',
                'status_pendaftaran' => 'Tertarik Daftar Haji',
                'claim_status' => 'Disetujui',
                'porsi_number' => '-',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Jaydon Ekstrom Bothman',
                'nik' => '3171012345670017',
                'address' => 'Jl. Palmerah No. 44, Jakarta Barat',
                'email' => 'pgarcia@gmail.com',
                'phone_number' => '081234567890',
                'registration_type' => 'Reguler',
                'bps_bpih' => 'Bank Syariah Indonesia',
                'status_pendaftaran' => 'Pendaftar Haji',
                'claim_status' => 'Disetujui',
                'porsi_number' => '12341234123412',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Livia Donin',
                'nik' => '3171012345670018',
                'address' => 'Jl. Kemang Raya No. 10, Jakarta Selatan',
                'email' => 'xmiller@yahoo.com',
                'phone_number' => '081234567890',
                'registration_type' => 'Khusus',
                'bps_bpih' => 'CIMB Niaga Syariah',
                'status_pendaftaran' => 'Pendaftar Haji',
                'claim_status' => 'Disetujui',
                'porsi_number' => '12341234123412',
            ],
            [
                'agent_id' => $agentId,
                'name' => 'Miracle Franci',
                'nik' => '3171012345670019',
                'address' => 'Jl. Dago No. 120, Bandung',
                'email' => 'jgreen@icloud.com',
                'phone_number' => '081234567890',
                'registration_type' => '-',
                'bps_bpih' => '-',
                'status_pendaftaran' => '-',
                'claim_status' => '-',
                'porsi_number' => '-',
            ],
        ];

        $genericRegulerCount = 18;
        $genericKhususCount = 5;

        for ($i = 1; $i <= $genericRegulerCount; $i++) {
            $prospects[] = [
                'agent_id' => $agentId,
                'name' => 'Jemaah Reguler ' . $i,
                'nik' => '317102000000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => 'Jl. Reguler Raya No. ' . $i . ', Jakarta',
                'email' => 'reguler' . $i . '@example.com',
                'phone_number' => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'registration_type' => 'Reguler',
                'bps_bpih' => 'Bank Syariah Indonesia',
                'status_pendaftaran' => 'Pendaftar Haji',
                'claim_status' => 'Disetujui',
                'porsi_number' => '1234' . str_pad($i, 10, '0', STR_PAD_LEFT),
            ];
        }

        for ($i = 1; $i <= $genericKhususCount; $i++) {
            $prospects[] = [
                'agent_id' => $agentId,
                'name' => 'Jemaah Khusus ' . $i,
                'nik' => '317103000000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => 'Jl. Khusus Raya No. ' . $i . ', Jakarta',
                'email' => 'khusus' . $i . '@example.com',
                'phone_number' => '0812' . str_pad($i, 8, '1', STR_PAD_LEFT),
                'registration_type' => 'Khusus',
                'bps_bpih' => 'Bank Muamalat Indonesia',
                'status_pendaftaran' => 'Pendaftar Haji',
                'claim_status' => 'Disetujui',
                'porsi_number' => '5678' . str_pad($i, 10, '0', STR_PAD_LEFT),
            ];
        }

        $cumulativeBalance = 0;
        foreach ($prospects as $pData) {
            $pId = DB::table('prospect_jemaahs')->insertGetId(array_merge($pData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            if ($pData['status_pendaftaran'] === 'Pendaftar Haji') {
                $cumulativeBalance += 75000.00;
                DB::table('commission_ledgers')->insert([
                    'agent_id' => $agentId,
                    'prospect_jemaah_id' => $pId,
                    'type' => 'credit',
                    'amount' => 75000.00,
                    'balance_after' => $cumulativeBalance,
                    'status' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 5. Seed default login background
        DB::table('login_backgrounds')->insert([
            'image_path' => 'uploads/backgrounds/bg_default.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
