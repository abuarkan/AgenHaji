<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\AgentPointLedger;
use App\Models\ProspectJemaah;
use App\Models\RacingProgram;
use App\Models\ReferralProgram;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AgentLevelingService;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic AgentLevel
        AgentLevel::create([
            'name' => 'Level 1',
            'target_prospects' => 0,
            'commission_per_prospect' => 1000000,
        ]);
    }

    public function test_referral_program_periodization()
    {
        $service = app(GamificationService::class);

        // Initially no active program
        $this->assertFalse($service->isReferralMonetizable());

        // Create active program covering today
        ReferralProgram::create([
            'name' => 'Program Referral Q3 2026',
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(60)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $this->assertTrue($service->isReferralMonetizable());
    }

    public function test_points_awarded_on_verified_prospect_and_portion_number()
    {
        // Setup point settings
        SystemSetting::create(['key' => 'points_per_reguler', 'value' => '10', 'type' => 'number', 'group' => 'gamification']);
        SystemSetting::create(['key' => 'points_per_portion', 'value' => '5', 'type' => 'number', 'group' => 'gamification']);

        // Create referral program
        ReferralProgram::create([
            'name' => 'Active Program',
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(10)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Agen Tester',
            'email' => 'agent.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'agent',
        ]);

        $level = AgentLevel::first();

        $agent = Agent::create([
            'user_id' => $user->id,
            'nik' => '3171010101010099',
            'whatsapp_number' => '081234567891',
            'referral_code' => 'AGNTST1',
            'agent_type' => 'freelance',
            'status' => 'active',
            'agent_level_id' => $level->id,
            'is_ktp_verified' => true,
            'is_submitted' => true,
        ]);

        $prospect = ProspectJemaah::create([
            'agent_id' => $agent->id,
            'name' => 'Jemaah Test',
            'nik' => '3171010101010001',
            'phone_number' => '081299998888',
            'address' => 'Jakarta',
            'status_pendaftaran' => 'Verified',
            'registration_type' => 'Reguler',
            'porsi_number' => '1234567890',
        ]);

        $levelingService = app(AgentLevelingService::class);
        $levelingService->creditCommissionForProspect($prospect);

        $gamificationService = app(GamificationService::class);
        $totalPoints = $gamificationService->getAgentTotalPoints($agent->id);

        // 10 points for reguler + 5 points for portion = 15 total points
        $this->assertEquals(15, $totalPoints);
        $this->assertEquals(2, AgentPointLedger::where('agent_id', $agent->id)->count());
    }

    public function test_racing_program_leaderboard()
    {
        $racing = RacingProgram::create([
            'title' => 'Racing Porsi Hajj 2026',
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(25)->format('Y-m-d'),
            'min_portion_target' => 2,
            'prize_rank_1' => 'Paket Umrah',
            'is_active' => true,
        ]);

        $user1 = User::create(['name' => 'Agen Juara 1', 'email' => 'juara1@example.com', 'password' => bcrypt('password'), 'role' => 'agent']);
        $user2 = User::create(['name' => 'Agen Juara 2', 'email' => 'juara2@example.com', 'password' => bcrypt('password'), 'role' => 'agent']);

        $level = AgentLevel::first();

        $agent1 = Agent::create(['user_id' => $user1->id, 'nik' => '3171010101010011', 'whatsapp_number' => '081234567892', 'referral_code' => 'AGNT1', 'agent_type' => 'freelance', 'status' => 'active', 'agent_level_id' => $level->id]);
        $agent2 = Agent::create(['user_id' => $user2->id, 'nik' => '3171010101010022', 'whatsapp_number' => '081234567893', 'referral_code' => 'AGNT2', 'agent_type' => 'freelance', 'status' => 'active', 'agent_level_id' => $level->id]);

        // Agent 1 has 3 prospects with portion number
        for ($i = 1; $i <= 3; $i++) {
            ProspectJemaah::create([
                'agent_id' => $agent1->id,
                'name' => "Jemaah Agent1 $i",
                'nik' => "317101010101000$i",
                'phone_number' => "08129999880$i",
                'address' => 'Jakarta',
                'status_pendaftaran' => 'Pendaftar Haji',
                'porsi_number' => "PORSI10$i",
            ]);
        }

        // Agent 2 has 1 prospect with portion number
        ProspectJemaah::create([
            'agent_id' => $agent2->id,
            'name' => "Jemaah Agent2 1",
            'nik' => "3171010101010099",
            'phone_number' => "081299998899",
            'address' => 'Jakarta',
            'status_pendaftaran' => 'Pendaftar Haji',
            'porsi_number' => "PORSI201",
        ]);

        $service = app(GamificationService::class);
        $result = $service->getRacingLeaderboard();

        $this->assertTrue($result['has_active_program']);
        $this->assertCount(2, $result['leaderboard']);
        $this->assertEquals($agent1->id, $result['leaderboard'][0]['agent_id']);
        $this->assertTrue($result['leaderboard'][0]['is_qualified']);
        $this->assertFalse($result['leaderboard'][1]['is_qualified']);
    }
}
