<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\AuditLog;
use App\Models\CommissionLedger;
use App\Models\ProspectJemaah;
use App\Models\User;
use App\Services\AgentLevelingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AgentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $levelingService;

    protected function setUp(): void
    {
        parent::setUp();

        // Run seeders to populate initial agent levels, test users, and settings
        $this->seed();

        $this->levelingService = app(AgentLevelingService::class);
    }

    /**
     * Test Role-Based Access Control (RBAC) and auditing of unauthorized access.
     */
    public function test_role_hierarchy_middleware_blocks_and_audits(): void
    {
        // 1. Retrieve the seeded agent user
        $agentUser = User::where('role', 'agent')->first();

        // Define a temporary test route to verify role restriction
        $this->app['router']->get('/test-superadmin-route', function () {
            return response()->json(['status' => 'success']);
        })->middleware(['web', 'role:superadmin']);

        // 2. Access as agent (should be blocked with 403)
        $response = $this->actingAs($agentUser)->getJson('/test-superadmin-route');
        $response->assertStatus(403);

        // 3. Verify that an unauthorized access attempt was logged in audit_logs
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $agentUser->id,
            'action' => 'unauthorized_access_attempt'
        ]);

        // 4. Access as superadmin (should succeed with 200)
        $superadminUser = User::where('role', 'superadmin')->first();
        $response = $this->actingAs($superadminUser)->getJson('/test-superadmin-route');
        $response->assertStatus(200);
    }

    /**
     * Test Multi-Tenant isolation scopes.
     */
    public function test_multi_tenant_isolation_scopes(): void
    {
        // Clear prospects to ensure clean counts in tests
        ProspectJemaah::query()->delete();

        // 1. Retrieve the seeded agents
        $agent1 = Agent::where('referral_code', 'ALRM2002')->first();
        $agent2 = Agent::where('referral_code', 'BPKH-KBIU001')->first();

        // 2. Create prospect pilgrims for Agent 1
        $prospect1 = ProspectJemaah::create([
            'agent_id' => $agent1->id,
            'name' => 'Jemaah Agent 1',
            'nik' => '1111222233334444',
            'phone_number' => '081200000001',
            'status_pendaftaran' => 'Draft'
        ]);

        // 3. Create prospect pilgrims for Agent 2
        $prospect2 = ProspectJemaah::create([
            'agent_id' => $agent2->id,
            'name' => 'Jemaah Agent 2',
            'nik' => '5555666677778888',
            'phone_number' => '081200000002',
            'status_pendaftaran' => 'Draft'
        ]);

        // 4. Authenticate as Agent 1's user
        Auth::login($agent1->user);

        // Retrieve prospects; TenantScope should automatically restrict to Agent 1's prospects
        $myProspects = ProspectJemaah::all();
        $this->assertCount(1, $myProspects);
        $this->assertEquals($prospect1->id, $myProspects->first()->id);

        // 5. Authenticate as Agent 2's user
        Auth::login($agent2->user);

        $myProspects = ProspectJemaah::all();
        $this->assertCount(1, $myProspects);
        $this->assertEquals($prospect2->id, $myProspects->first()->id);

        // 6. Authenticate as Superadmin
        $superadmin = User::where('role', 'superadmin')->first();
        Auth::login($superadmin);

        // Superadmin should bypass TenantScope and see all prospects nationwide
        $allProspects = ProspectJemaah::all();
        $this->assertCount(2, $allProspects);

        // 7. Authenticate as Admin Haji
        $adminHaji = User::where('role', 'admin_haji')->first();
        Auth::login($adminHaji);

        // Admin Haji should also bypass TenantScope and see all prospects nationwide
        $allProspectsAdmin = ProspectJemaah::all();
        $this->assertCount(2, $allProspectsAdmin);
    }

    /**
     * Test dynamic leveling promotion and commission credits.
     */
    public function test_agent_promotion_and_commission_ledger(): void
    {
        $agent = Agent::where('referral_code', 'ALRM2002')->first();
        $silverLevel = AgentLevel::where('name', 'Silver')->first();
        $goldLevel = AgentLevel::where('name', 'Gold')->first();

        // Set agent to Silver level to test promotion
        $agent->agent_level_id = $silverLevel->id;
        $agent->save();

        // Clear prospects and commission ledgers to ensure clean counts in tests
        ProspectJemaah::query()->delete();
        CommissionLedger::query()->delete();

        // 1. Initial State: Agent is at Silver level
        $this->assertEquals($silverLevel->id, $agent->agent_level_id);

        // 2. Create and verify 20 prospects for the agent to meet Silver level
        // (already at Silver, so no change)
        for ($i = 0; $i < 20; $i++) {
            ProspectJemaah::create([
                'agent_id' => $agent->id,
                'name' => 'Jemaah ' . $i,
                'nik' => '10000000000000' . sprintf('%02d', $i),
                'phone_number' => '0812000000' . sprintf('%02d', $i),
                'status_pendaftaran' => 'Verified',
                'verified_at' => now()
            ]);
        }

        $res = $this->levelingService->evaluateAgentLevel($agent);
        $this->assertEquals('Silver', $res['new_level']);
        $this->assertFalse($res['level_changed']);

        // 3. Create 6 more verified prospects (Total 26) to reach Gold target
        for ($i = 20; $i < 26; $i++) {
            $prospect = ProspectJemaah::create([
                'agent_id' => $agent->id,
                'name' => 'Jemaah ' . $i,
                'nik' => '10000000000000' . sprintf('%02d', $i),
                'phone_number' => '0812000000' . sprintf('%02d', $i),
                'status_pendaftaran' => 'Verified',
                'verified_at' => now()
            ]);

            // Credit commission for the verified prospect
            $this->levelingService->creditCommissionForProspect($prospect);
        }

        // Evaluate levels (simulating the monthly scheduler)
        $res = $this->levelingService->evaluateAgentLevel($agent);
        
        // Reload agent profile
        $agent->refresh();

        // 4. Verify promotion to Gold
        $this->assertEquals('Gold', $res['new_level']);
        $this->assertTrue($res['level_changed']);
        $this->assertEquals($goldLevel->id, $agent->agent_level_id);

        // 5. Verify that audit log was generated for the promotion
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'agent_level_promotion',
            'model_id' => $agent->id
        ]);

        // 6. Verify ledger entries: commission was credited at Silver rate (Rp50,000) for the 6 new prospects
        $creditedAmount = CommissionLedger::where('agent_id', $agent->id)
            ->where('type', 'credit')
            ->value('amount');
        
        $this->assertEquals(50000.00, $creditedAmount); // Verified under Silver level, so Rp50k rate applies
    }

    /**
     * Test agent can view their own profile, but not other agents' profiles.
     */
    public function test_agent_profile_access_control(): void
    {
        $agent1 = Agent::where('referral_code', 'ALRM2002')->first();
        $agent2 = Agent::where('referral_code', 'BPKH-KBIU001')->first();
        $superadmin = User::where('role', 'superadmin')->first();

        // 1. Agent 1 can view own profile
        $response = $this->actingAs($agent1->user)->get("/agent/profile/{$agent1->referral_code}");
        $response->assertStatus(200);
        $response->assertSee($agent1->full_name);
        $response->assertSee((string) $agent1->latitude_tinggal);
        $response->assertSee((string) $agent1->longitude_tinggal);

        // 2. Agent 1 cannot view Agent 2's profile
        $response = $this->actingAs($agent1->user)->get("/agent/profile/{$agent2->referral_code}");
        $response->assertStatus(403);

        // 3. Superadmin can view Agent 1's profile
        $response = $this->actingAs($superadmin)->get("/agent/profile/{$agent1->referral_code}");
        $response->assertStatus(200);
        $response->assertSee($agent1->full_name);

        // 4. B2B Institution Agent can view their sub-agent's profile
        // Create a sub-agent under agent2's institution
        $subUser = User::factory()->create(['role' => 'agent']);
        $subAgent = Agent::create([
            'user_id' => $subUser->id,
            'institution_id' => $agent2->institution_id,
            'agent_level_id' => $agent2->agent_level_id,
            'referral_code' => 'BPKH-SUB001',
            'nik' => '3171012345679999',
            'whatsapp_number' => '6289876543999',
            'type' => 'freelance',
            'status' => 'active',
            'full_name' => 'Sub Agent Test',
            'latitude_tinggal' => -6.500000,
            'longitude_tinggal' => 106.900000
        ]);

        // Agent 2 (B2B Institution) should be able to view their sub-agent
        $response = $this->actingAs($agent2->user)->get("/agent/profile/{$subAgent->referral_code}");
        $response->assertStatus(200);
        $response->assertSee('Sub Agent Test');
    }

    /**
     * Test the self-registration, wizard interception, and superadmin approval flow.
     */
    public function test_self_registration_wizard_and_approval_flow(): void
    {
        // 1. Register as a new agent
        $response = $this->post('/register', [
            'name' => 'Calon Agen Baru',
            'email' => 'calon.agen@bpkh.go.id',
            'whatsapp_number' => '6287777777777',
            'nik' => '3201011234560001',
            'type' => 'freelance',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        // Retrieve the created user and agent
        $user = User::where('email', 'calon.agen@bpkh.go.id')->firstOrFail();
        $agent = Agent::withoutGlobalScope(\App\Scopes\TenantScope::class)->where('user_id', $user->id)->firstOrFail();

        $this->assertEquals('pending', $agent->status);
        $this->assertFalse((bool) $agent->is_ktp_verified);

        // 2. Try accessing the dashboard (should be redirected to the wizard)
        $response = $this->actingAs($user)->get('/agent/freelance');
        $response->assertRedirect(route('agent.verification.wizard'));

        // 3. Fill the wizard
        $response = $this->actingAs($user)->post('/agent/verification-wizard', [
            'nama_lengkap' => 'Calon Agen Baru Lengkap',
            'jenis_kelamin' => 'Pria',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'alamat_ktp' => 'Jl. KTP No. 1',
            'provinsi_ktp' => 'DKI Jakarta',
            'kota_ktp' => 'Jakarta Selatan',
            'kecamatan_ktp' => 'Kebayoran Baru',
            'kelurahan_ktp' => 'Senayan',
            'alamat_tinggal' => 'Jl. Domisili No. 2',
            'provinsi_tinggal' => 'DKI Jakarta',
            'kota_tinggal' => 'Jakarta Selatan',
            'kecamatan_tinggal' => 'Kebayoran Baru',
            'kelurahan_tinggal' => 'Senayan',
            'nama_bank' => 'Bank Syariah Indonesia',
            'cabang_bank' => 'KCP Fatmawati',
            'nomor_rekening' => '1234567890',
            'nomor_npwp' => '123456789012345',
        ]);

        $response->assertRedirect(route('agent.verification.wizard'));
        $agent->refresh();
        $this->assertTrue((bool) $agent->is_submitted);

        // 4. Act as Admin Haji and approve the agent
        $adminHaji = User::where('role', 'admin_haji')->first();
        $response = $this->actingAs($adminHaji)->post("/superadmin/agents/{$agent->id}/status", [
            'status' => 'active'
        ]);

        $response->assertRedirect(route('superadmin.index'));
        $agent->refresh();

        $this->assertEquals('active', $agent->status);
        $this->assertTrue((bool) $agent->is_ktp_verified);
        $this->assertTrue((bool) $agent->is_email_verified);
        $this->assertTrue((bool) $agent->is_whatsapp_verified);

        // 5. Try accessing the dashboard again (should load successfully with 200, no redirect)
        $response = $this->actingAs($user)->get('/agent/freelance');
        $response->assertStatus(200);
        $response->assertSee('Calon Agen Baru');
    }

    /**
     * Test User Management CRUD for Superadmin.
     */
    public function test_superadmin_user_crud(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();

        // 1. Store User
        $response = $this->actingAs($superadmin)->post('/superadmin/users', [
            'name' => 'New Test Admin',
            'email' => 'new.test.admin@bpkh.go.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin_haji'
        ]);

        $response->assertRedirect(route('superadmin.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Test Admin',
            'email' => 'new.test.admin@bpkh.go.id',
            'role' => 'admin_haji'
        ]);

        $newUser = User::where('email', 'new.test.admin@bpkh.go.id')->first();

        // 2. Update User
        $response = $this->actingAs($superadmin)->post("/superadmin/users/{$newUser->id}/update", [
            'name' => 'Updated Test Admin',
            'email' => 'new.test.admin@bpkh.go.id',
            'role' => 'superadmin'
        ]);

        $response->assertRedirect(route('superadmin.index'));
        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'name' => 'Updated Test Admin',
            'role' => 'superadmin'
        ]);

        // 3. Prevent Self-Deletion
        $response = $this->actingAs($superadmin)->post("/superadmin/users/{$superadmin->id}/delete");
        $response->assertRedirect(route('superadmin.index'));
        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);

        // 4. Delete User
        $response = $this->actingAs($superadmin)->post("/superadmin/users/{$newUser->id}/delete");
        $response->assertRedirect(route('superadmin.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }
}
