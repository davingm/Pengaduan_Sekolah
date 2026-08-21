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

    public function test_demo_role_switcher(): void
    {
        $response = $this->get('/demo-switch/guru_piket');
        $response->assertRedirect('/dashboard');
        $this->assertEquals('guru_piket', auth()->user()->role);
    }
}
