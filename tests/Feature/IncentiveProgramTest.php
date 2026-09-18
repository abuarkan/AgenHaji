<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\ProspectJemaah;
use App\Models\User;
use App\Services\IncentiveService;
use App\Services\SiskehatApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncentiveProgramTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        AgentLevel::create([
            'name' => 'Silver',
            'target_prospects' => 0,
            'commission_per_prospect' => 1000000,
        ]);
    }

    public function test_progressive_tier_incentive_calculation()
    {
        $user = User::create([
            'name' => 'Agent Incentive Test',
            'email' => 'agent.inc@example.com',
            'password' => bcrypt('password'),
            'role' => 'agent',
        ]);

        $level = AgentLevel::first();

        $agent = Agent::create([
            'user_id' => $user->id,
            'nik' => '3171010101019999',
            'whatsapp_number' => '081299990000',
            'referral_code' => 'INCAGENT1',
            'agent_type' => 'freelance',
            'status' => 'active',
            'agent_level_id' => $level->id,
        ]);

        $currentMonth = date('n');
        $currentYear = date('Y');

        // Create 35 prospects via BPKH Apps with portion number
        for ($i = 1; $i <= 35; $i++) {
            ProspectJemaah::create([
                'agent_id' => $agent->id,
                'name' => "Jemaah $i",
                'nik' => "31710101010100" . str_pad($i, 2, '0', STR_PAD_LEFT),
                'phone_number' => "0812000000" . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Jakarta',
                'status_pendaftaran' => 'Pendaftar Haji',
                'porsi_number' => "PORSI$i",
                'registration_channel' => 'bpkh_apps',
                'is_porsi_bound' => true,
            ]);
        }

        $service = app(IncentiveService::class);
        $result = $service->calculateMonthlyAgentIncentive($agent->id, $currentMonth, $currentYear);

        $this->assertEquals(35, $result['total_porsi']);
        $this->assertEquals(35, $result['bpkh_apps']['count']);
        
        // 20 x 125,000 (Silver) + 15 x 150,000 (Gold) = 2,500,000 + 2,250,000 = 4,750,000
        $this->assertEquals(4750000, $result['total_incentive_amount']);
    }

    public function test_siskehat_api_sync()
    {
        $user = User::create(['name' => 'Sync Test', 'email' => 'sync@example.com', 'password' => bcrypt('password'), 'role' => 'agent']);
        $level = AgentLevel::first();
        $agent = Agent::create([
            'user_id' => $user->id,
            'nik' => '3171010101018888',
            'whatsapp_number' => '081288880000',
            'referral_code' => 'SYNCAGENT',
            'agent_type' => 'freelance',
            'status' => 'active',
            'agent_level_id' => $level->id,
        ]);

        $prospect = ProspectJemaah::create([
            'agent_id' => $agent->id,
            'name' => 'Jemaah Sync',
            'nik' => '3171010101017777',
            'phone_number' => '081277770000',
            'address' => 'Jakarta',
            'status_pendaftaran' => 'Pendaftar Haji',
            'porsi_number' => 'PORSI888',
        ]);

        $service = app(SiskehatApiService::class);
        $response = $service->syncNominativePilgrimData($prospect->id);

        $this->assertTrue($response['success']);
        
        $prospect->refresh();
        $this->assertNotNull($prospect->siskehat_reference_id);
        $this->assertNotNull($prospect->siskehat_sync_at);
        $this->assertTrue($prospect->is_porsi_bound);
    }
}
