<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agent;
use App\Models\AgentLevel;
use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AgentImportTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $level;
    protected $institution;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin Haji user
        $this->admin = User::create([
            'name' => 'Admin Haji BPKH',
            'email' => 'admin_haji@bpkh.go.id',
            'password' => bcrypt('password123'),
            'role' => 'admin_haji',
        ]);

        // Create Default Agent level
        $this->level = AgentLevel::create([
            'name' => 'Silver',
            'target_prospects' => 0,
            'commission_per_prospect' => 100000
        ]);

        // Create B2B Institution
        $this->institution = Institution::create([
            'name' => 'KBIHU Budi Luhur',
            'registration_number' => 'REG-BPKH-001',
            'address' => 'Jl. Kebon Jeruk No. 5',
            'status' => 'active'
        ]);
    }

    /**
     * Test download template CSV.
     */
    public function test_can_download_csv_template()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.agents.import-template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_impor_agen.csv"');
    }

    /**
     * Test validate import CSV with valid and invalid data.
     */
    public function test_validate_import_agents()
    {
        // CSV Content containing:
        // 1. A valid Freelance agent
        // 2. An invalid agent (missing name)
        // 3. A valid Institution agent linked to REG-BPKH-001
        // 4. An invalid Institution agent (institution registration number does not exist)
        $csvContent = "name;email;nik;whatsapp_number;type;institution_registration_number;is_institution_admin;nip\n" .
            "Ujang Freelance;ujang@gmail.com;3171012345678901;081234567891;freelance;;no;\n" .
            ";missname@gmail.com;3171012345678902;081234567892;freelance;;no;\n" .
            "Asep B2B;asep@gmail.com;3171012345678903;081234567893;institution;REG-BPKH-001;yes;NIP999\n" .
            "Dadang B2B;dadang@gmail.com;3171012345678904;081234567894;institution;REG-WRONG-999;no;";

        $file = UploadedFile::fake()->createWithContent('agents.csv', $csvContent);

        $response = $this->actingAs($this->admin)
            ->post(route('superadmin.agents.import-validate'), [
                'file' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'valid_count' => 2,
            'invalid_count' => 2
        ]);

        $rows = $response->json('rows');
        $this->assertTrue($rows[0]['is_valid']); // Ujang valid
        $this->assertFalse($rows[1]['is_valid']); // Missing name invalid
        $this->assertTrue($rows[2]['is_valid']); // Asep valid
        $this->assertFalse($rows[3]['is_valid']); // Wrong institution invalid
    }

    /**
     * Test process and store valid imported agents.
     */
    public function test_process_import_agents()
    {
        $payload = [
            'agents' => [
                [
                    'name' => 'Ujang Freelance',
                    'email' => 'ujang@gmail.com',
                    'nik' => '3171012345678901',
                    'whatsapp_number' => '081234567891',
                    'type' => 'freelance',
                    'institution_registration_number' => '',
                    'is_institution_admin' => 'no',
                    'nip' => ''
                ],
                [
                    'name' => 'Asep B2B',
                    'email' => 'asep@gmail.com',
                    'nik' => '3171012345678903',
                    'whatsapp_number' => '081234567893',
                    'type' => 'institution',
                    'institution_registration_number' => 'REG-BPKH-001',
                    'is_institution_admin' => 'yes',
                    'nip' => 'NIP999'
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('superadmin.agents.import-process'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 2
        ]);

        // Assert Users and Agents are created in Database
        $this->assertDatabaseHas('users', [
            'email' => 'ujang@gmail.com',
            'role' => 'agent'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'asep@gmail.com',
            'role' => 'agent'
        ]);

        // Assert agents are created as unverified and pending (user persona requirement)
        $ujangAgent = Agent::where('nik', '3171012345678901')->first();
        $this->assertNotNull($ujangAgent);
        $this->assertEquals('pending', $ujangAgent->status);
        $this->assertFalse((bool)$ujangAgent->is_email_verified);
        $this->assertFalse((bool)$ujangAgent->is_whatsapp_verified);
        $this->assertNull($ujangAgent->institution_id);

        $asepAgent = Agent::where('nik', '3171012345678903')->first();
        $this->assertNotNull($asepAgent);
        $this->assertEquals('pending', $asepAgent->status);
        $this->assertFalse((bool)$asepAgent->is_email_verified);
        $this->assertFalse((bool)$asepAgent->is_whatsapp_verified);
        $this->assertEquals($this->institution->id, $asepAgent->institution_id);
        $this->assertTrue((bool)$asepAgent->is_institution_admin);
        $this->assertEquals('NIP999', $asepAgent->nip);
    }
}
