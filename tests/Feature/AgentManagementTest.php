<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agent;
use App\Models\AgentLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminHaji;
    protected $agentUser;
    protected $agentProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create silver level for agent
        AgentLevel::create([
            'name' => 'Silver',
            'target_prospects' => 0,
            'commission_per_prospect' => 100000
        ]);

        // Create Admin Haji
        $this->adminHaji = User::create([
            'name' => 'Admin Haji BPKH',
            'email' => 'admin.haji@bpkh.go.id',
            'password' => bcrypt('password123'),
            'role' => 'admin_haji',
        ]);

        // Create an Agent User
        $this->agentUser = User::create([
            'name' => 'Agent Ahmad',
            'email' => 'ahmad@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent',
        ]);

        // Create their agent profile
        $this->agentProfile = Agent::create([
            'user_id' => $this->agentUser->id,
            'agent_level_id' => 1,
            'referral_code' => 'AHMAD-REF',
            'nik' => '1234567890123456',
            'whatsapp_number' => '6281234567890',
            'type' => 'freelance',
            'status' => 'active',
            'is_email_verified' => false,
            'is_whatsapp_verified' => false,
            'is_ktp_verified' => false
        ]);
    }

    /**
     * Test admin can toggle verify agent credentials.
     */
    public function test_admin_haji_can_verify_agent_credentials()
    {
        // 1. Verify Email
        $response = $this->actingAs($this->adminHaji)
            ->post("/superadmin/agents/{$this->agentProfile->id}/verify-credentials", [
                'field' => 'email',
                'status' => 1
            ]);

        $response->assertRedirect('/superadmin');
        $this->agentProfile->refresh();
        $this->assertTrue((bool)$this->agentProfile->is_email_verified);

        // 2. Unverify Email
        $response = $this->actingAs($this->adminHaji)
            ->post("/superadmin/agents/{$this->agentProfile->id}/verify-credentials", [
                'field' => 'email',
                'status' => 0
            ]);
        $this->agentProfile->refresh();
        $this->assertFalse((bool)$this->agentProfile->is_email_verified);

        // 3. Verify WhatsApp
        $response = $this->actingAs($this->adminHaji)
            ->post("/superadmin/agents/{$this->agentProfile->id}/verify-credentials", [
                'field' => 'whatsapp',
                'status' => 1
            ]);
        $this->agentProfile->refresh();
        $this->assertTrue((bool)$this->agentProfile->is_whatsapp_verified);

        // 4. Verify KTP
        $response = $this->actingAs($this->adminHaji)
            ->post("/superadmin/agents/{$this->agentProfile->id}/verify-credentials", [
                'field' => 'ktp',
                'status' => 1
            ]);
        $this->agentProfile->refresh();
        $this->assertTrue((bool)$this->agentProfile->is_ktp_verified);
    }

    /**
     * Test admin can delete agent profile.
     */
    public function test_admin_haji_can_delete_agent()
    {
        $response = $this->actingAs($this->adminHaji)
            ->delete("/superadmin/agents/{$this->agentProfile->id}");

        $response->assertRedirect('/superadmin');

        // Verify records are deleted
        $this->assertDatabaseMissing('agents', ['id' => $this->agentProfile->id]);
        $this->assertDatabaseMissing('users', ['id' => $this->agentUser->id]);
    }

    /**
     * Test non-admin cannot delete or verify agent.
     */
    public function test_non_admin_blocked_from_agent_actions()
    {
        $hacker = User::create([
            'name' => 'Agent Hacker',
            'email' => 'hacker@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'agent'
        ]);

        // Try verify
        $responseVerify = $this->actingAs($hacker)
            ->post("/superadmin/agents/{$this->agentProfile->id}/verify-credentials", [
                'field' => 'email',
                'status' => 1
            ]);
        $responseVerify->assertStatus(404);

        // Try delete
        $responseDelete = $this->actingAs($hacker)
            ->delete("/superadmin/agents/{$this->agentProfile->id}");
        $responseDelete->assertStatus(404);
    }
}
