<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\ProspectJemaah;
use App\Models\RacingProgram;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RacingContestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        AgentLevel::create([
            'name' => 'Gold',
            'target_prospects' => 0,
            'commission_per_prospect' => 1000000,
        ]);
    }

    public function test_racing_contest_threshold_and_umrah_winner_selection()
    {
        $level = AgentLevel::first();

        // Agent 1: 120 Porsi (Qualified, Winner #1)
        $u1 = User::create(['name' => 'Agen A (Top 1)', 'email' => 'a@example.com', 'password' => bcrypt('password'), 'role' => 'agent']);
        $ag1 = Agent::create(['user_id' => $u1->id, 'nik' => '3171010101011111', 'whatsapp_number' => '081211110000', 'referral_code' => 'RACEA', 'agent_type' => 'freelance', 'status' => 'active', 'agent_level_id' => $level->id]);

        // Agent 2: 105 Porsi (Qualified, Winner #2)
        $u2 = User::create(['name' => 'Agen B (Top 2)', 'email' => 'b@example.com', 'password' => bcrypt('password'), 'role' => 'agent']);
        $ag2 = Agent::create(['user_id' => $u2->id, 'nik' => '3171010101012222', 'whatsapp_number' => '081222220000', 'referral_code' => 'RACEB', 'agent_type' => 'freelance', 'status' => 'active', 'agent_level_id' => $level->id]);

        // Agent 3: 90 Porsi (NOT Qualified, NOT Winner)
        $u3 = User::create(['name' => 'Agen C (Unqualified)', 'email' => 'c@example.com', 'password' => bcrypt('password'), 'role' => 'agent']);
        $ag3 = Agent::create(['user_id' => $u3->id, 'nik' => '3171010101013333', 'whatsapp_number' => '081233330000', 'referral_code' => 'RACEC', 'agent_type' => 'freelance', 'status' => 'active', 'agent_level_id' => $level->id]);

        // Create Active Racing Contest Program
        $program = RacingProgram::create([
            'title' => 'Racing Contest Tenaga Pemasaran 2026',
            'start_date' => '2026-07-01',
            'end_date' => '2026-10-31',
            'announcement_date' => '2026-11-30',
            'min_portion_target' => 100,
            'reward_type' => 'Paket Umrah Gratis',
            'winner_quota' => 2, // Set winner quota to 2 for test
            'target_bps_bpih' => 'Bank Muamalat Indonesia',
            'is_active' => true,
        ]);

        // Create 120 prospects for Agent 1
        for ($i = 1; $i <= 120; $i++) {
            ProspectJemaah::create([
                'agent_id' => $ag1->id,
                'name' => "Jemaah A $i",
                'nik' => "3171010101011" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'phone_number' => "081210000" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => 'Jakarta',
                'status_pendaftaran' => 'Pendaftar Haji',
                'porsi_number' => "PORSI1-$i",
                'registration_channel' => 'bpkh_apps',
                'is_porsi_bound' => true,
                'created_at' => '2026-08-15 10:00:00',
            ]);
        }

        // Create 105 prospects for Agent 2
        for ($i = 1; $i <= 105; $i++) {
            ProspectJemaah::create([
                'agent_id' => $ag2->id,
                'name' => "Jemaah B $i",
                'nik' => "3171010101012" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'phone_number' => "081220000" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => 'Jakarta',
                'status_pendaftaran' => 'Pendaftar Haji',
                'porsi_number' => "PORSI2-$i",
                'registration_channel' => 'bpkh_apps',
                'is_porsi_bound' => true,
                'created_at' => '2026-08-16 10:00:00',
            ]);
        }

        // Create 90 prospects for Agent 3
        for ($i = 1; $i <= 90; $i++) {
            ProspectJemaah::create([
                'agent_id' => $ag3->id,
                'name' => "Jemaah C $i",
                'nik' => "3171010101013" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'phone_number' => "081230000" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => 'Jakarta',
                'status_pendaftaran' => 'Pendaftar Haji',
                'porsi_number' => "PORSI3-$i",
                'registration_channel' => 'bpkh_apps',
                'is_porsi_bound' => true,
                'created_at' => '2026-08-17 10:00:00',
            ]);
        }

        $service = app(GamificationService::class);
        $result = $service->getRacingLeaderboard($program);

        $this->assertTrue($result['has_active_program']);
        $this->assertEquals(2, $result['winner_count']);

        $standings = $result['all_standings'];

        // Agent 1 should be Rank 1, qualified and Umrah winner
        $this->assertEquals($ag1->id, $standings[0]['agent_id']);
        $this->assertTrue($standings[0]['is_qualified']);
        $this->assertTrue($standings[0]['is_umrah_winner']);
        $this->assertEquals(120, $standings[0]['portion_count']);

        // Agent 2 should be Rank 2, qualified and Umrah winner
        $this->assertEquals($ag2->id, $standings[1]['agent_id']);
        $this->assertTrue($standings[1]['is_qualified']);
        $this->assertTrue($standings[1]['is_umrah_winner']);
        $this->assertEquals(105, $standings[1]['portion_count']);

        // Agent 3 should be Rank 3, NOT qualified and NOT Umrah winner (needs 10 more porsi)
        $this->assertEquals($ag3->id, $standings[2]['agent_id']);
        $this->assertFalse($standings[2]['is_qualified']);
        $this->assertFalse($standings[2]['is_umrah_winner']);
        $this->assertEquals(10, $standings[2]['needed_to_threshold']);
    }
}
