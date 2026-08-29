<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SiPengaduan');
    }

    public function test_student_can_submit_complaint_and_get_ticket(): void
    {
        $siswa = User::where('role', 'siswa')->first();
        $category = Category::first();

        $response = $this->actingAs($siswa)->post('/pengaduan', [
            'category_id' => $category->id,
            'title' => 'Meja Rusak di Kelas XII',
            'description' => 'Kaki meja baris depan patah dan tidak stabil.',
            'location' => 'Kelas XII MIPA 1',
            'priority' => 'sedang',
            'is_anonymous' => 0,
        ]);

        $complaint = Complaint::where('title', 'Meja Rusak di Kelas XII')->first();
        $this->assertNotNull($complaint);
        $this->assertStringStartsWith('PGD-', $complaint->ticket_code);
        $this->assertEquals('menunggu_verifikasi', $complaint->status);

        $response->assertRedirect('/pengaduan/' . $complaint->ticket_code);
    }

    public function test_guru_piket_can_verify_and_disposition_ticket(): void
    {
        $guruPiket = User::where('role', 'guru_piket')->first();
        $petugas = User::where('role', 'petugas')->first();
        $complaint = Complaint::where('status', 'menunggu_verifikasi')->first();

        $response = $this->actingAs($guruPiket)->post('/dashboard/pengaduan/' . $complaint->ticket_code . '/verify', [
            'assigned_to' => $petugas->id,
            'category_id' => $complaint->category_id,
            'priority' => 'tinggi',
            'notes' => 'Segera periksa sore ini.',
        ]);

        $complaint->refresh();
        $this->assertEquals('didisposisikan', $complaint->status);
        $this->assertEquals($petugas->id, $complaint->assigned_to);
        $this->assertEquals($guruPiket->id, $complaint->assigned_by);
    }

    public function test_petugas_can_process_and_resolve_ticket(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        $complaint = Complaint::where('status', 'didisposisikan')->first();

        // 1. Mulai proses
        $this->actingAs($petugas)->post('/dashboard/pengaduan/' . $complaint->ticket_code . '/process');
        $complaint->refresh();
        $this->assertEquals('diproses', $complaint->status);

        // 2. Laporkan selesai (menunggu persetujuan kepsek)
        $this->actingAs($petugas)->post('/dashboard/pengaduan/' . $complaint->ticket_code . '/resolve', [
            'resolution_notes' => 'Pipa AC telah dibersihkan dan unit normal kembali.',
        ]);
        $complaint->refresh();
        $this->assertEquals('menunggu_persetujuan', $complaint->status);
    }

    public function test_kepala_sekolah_can_approve_and_close_ticket(): void
    {
        $kepsek = User::where('role', 'kepala_sekolah')->first();
        $complaint = Complaint::where('status', 'menunggu_persetujuan')->first();

        $this->actingAs($kepsek)->post('/dashboard/pengaduan/' . $complaint->ticket_code . '/approve', [
            'approval_notes' => 'Laporan perbaikan disetujui.',
        ]);

        $complaint->refresh();
        $this->assertEquals('selesai', $complaint->status);
        $this->assertNotNull($complaint->resolved_at);
    }

    public function test_standard_login_with_credentials(): void
    {
        $user = User::where('role', 'guru_piket')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_demo_switcher_route_is_removed(): void
    {
        $response = $this->get('/demo-switch/guru_piket');
        $response->assertStatus(404);
    }

    public function test_siswa_cannot_access_category_or_reports(): void
    {
        $siswa = User::where('role', 'siswa')->first();

        // Should return 403 Forbidden
        $responseCat = $this->actingAs($siswa)->get('/dashboard/kategori');
        $responseCat->assertStatus(403);

        $responseLap = $this->actingAs($siswa)->get('/dashboard/laporan');
        $responseLap->assertStatus(403);
    }

    public function test_dashboard_index_accessible_by_all_roles(): void
    {
        $roles = ['siswa', 'guru_piket', 'petugas', 'kepala_sekolah', 'admin'];

        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
            $response->assertSee('Ringkasan');
        }
    }

    public function test_dashboard_pengaduan_index_and_filters(): void
    {
        $guruPiket = User::where('role', 'guru_piket')->first();

        // 1. Default all
        $response = $this->actingAs($guruPiket)->get('/dashboard/pengaduan');
        $response->assertStatus(200);
        $response->assertSee('Daftar Pengaduan');

        // 2. Filter status
        $responseStatus = $this->actingAs($guruPiket)->get('/dashboard/pengaduan?status=menunggu_verifikasi');
        $responseStatus->assertStatus(200);

        // 3. Search query
        $responseSearch = $this->actingAs($guruPiket)->get('/dashboard/pengaduan?q=PGD');
        $responseSearch->assertStatus(200);
    }

    public function test_dashboard_pengaduan_show_accessible(): void
    {
        $guruPiket = User::where('role', 'guru_piket')->first();
        $complaint = Complaint::first();

        $response = $this->actingAs($guruPiket)->get('/dashboard/pengaduan/' . $complaint->ticket_code);
        $response->assertStatus(200);
        $response->assertSee($complaint->ticket_code);
    }

    public function test_dashboard_kategori_and_laporan_accessible_by_admin_and_kepsek(): void
    {
        $admin = User::where('role', 'admin')->first();
        $kepsek = User::where('role', 'kepala_sekolah')->first();

        // Admin categories
        $responseCat = $this->actingAs($admin)->get('/dashboard/kategori');
        $responseCat->assertStatus(200);

        // Kepsek laporan
        $responseLap = $this->actingAs($kepsek)->get('/dashboard/laporan');
        $responseLap->assertStatus(200);

        // Kepsek print
        $responsePrint = $this->actingAs($kepsek)->get('/dashboard/laporan/print?start_date=2026-01-01&end_date=2026-12-31&status=all');
        $responsePrint->assertStatus(200);
    }
}
