<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintLog;
use App\Models\ComplaintResponse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ComplaintResponse::truncate();
        ComplaintLog::truncate();
        ComplaintAttachment::truncate();
        Complaint::truncate();
        Category::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create Users for all roles
        $admin = User::create([
            'name' => 'Administrator Sistem',
            'email' => 'admin@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nisn_nip' => '198501012010011001',
            'phone' => '081234567890',
            'department' => 'IT & Tata Usaha',
        ]);

        $guruPiket = User::create([
            'name' => 'Fikri Awaludin Rahmat (Guru Piket)',
            'email' => 'fikri@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'guru_piket',
            'nisn_nip' => '197605122005012003',
            'phone' => '081234567891',
            'department' => 'Tim Piket Harian & Kedisiplinan',
        ]);

        $guruBk = User::create([
            'name' => 'Dr. Dika Pratama (Guru BK)',
            'email' => 'dika@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'nisn_nip' => '198003152008011005',
            'phone' => '081234567892',
            'department' => 'Bimbingan Konseling (BK)',
        ]);

        $petugasSarpras = User::create([
            'name' => 'Faiz spd. (Petugas Sarpras)',
            'email' => 'faiz@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'nisn_nip' => '198811202015011002',
            'phone' => '081234567893',
            'department' => 'Sarana & Prasarana (Sarpras)',
        ]);

        $waliKelas = User::create([
            'name' => 'Nairha (Wali Kelas XI MIPA 2)',
            'email' => 'naiha@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'nisn_nip' => '198207102009012004',
            'phone' => '081234567894',
            'department' => 'Wali Kelas & Kesiswaan',
        ]);

        $kepsek = User::create([
            'name' => 'Dr, Ajrin.',
            'email' => 'ajrin@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
            'nisn_nip' => '196802141994031002',
            'phone' => '081234567895',
            'department' => 'Kepala Sekolah',
        ]);

        $siswa = User::create([
            'name' => 'Davin Gahisa Mustafid',
            'email' => 'a@davingm.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nisn_nip' => '0068945123',
            'phone' => '085712345678',
            'department' => 'Kelas XI MIPA 2',
        ]);

        $siswa2 = User::create([
            'name' => 'Kina Saqina',
            'email' => 'kina@mhs.id',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nisn_nip' => '0071234567',
            'phone' => '085798765432',
            'department' => 'Kelas X IPS 1',
        ]);

        // 2. Categories
        $catSarpras = Category::create([
            'name' => 'Sarana & Prasarana',
            'slug' => 'sarana-prasarana',
            'icon' => 'wrench',
            'color' => 'indigo',
            'description' => 'Fasilitas gedung, meja, kursi, proyektor, AC, toilet, dan sarana belajar',
            'default_role_target' => 'petugas',
            'is_active' => true,
        ]);

        $catBk = Category::create([
            'name' => 'Bimbingan Konseling & Perundungan',
            'slug' => 'bk-perundungan',
            'icon' => 'shield-alert',
            'color' => 'rose',
            'description' => 'Laporan bullying, mediasi konflik antarsiswa, dan pendampingan mental',
            'default_role_target' => 'petugas',
            'is_active' => true,
        ]);

        $catKebersihan = Category::create([
            'name' => 'Kebersihan & Lingkungan',
            'slug' => 'kebersihan-lingkungan',
            'icon' => 'sparkles',
            'color' => 'emerald',
            'description' => 'Kebersihan kelas, toilet, kantin, taman, dan tempat sampah',
            'default_role_target' => 'petugas',
            'is_active' => true,
        ]);

        $catAkademik = Category::create([
            'name' => 'Akademik & Pembelajaran',
            'slug' => 'akademik-pembelajaran',
            'icon' => 'book-open',
            'color' => 'blue',
            'description' => 'Jadwal pelajaran, buku paket perpustakaan, dan praktikum laboratorium',
            'default_role_target' => 'petugas',
            'is_active' => true,
        ]);

        $catKeamanan = Category::create([
            'name' => 'Keamanan & Ketertiban',
            'slug' => 'keamanan-ketertiban',
            'icon' => 'lock',
            'color' => 'amber',
            'description' => 'Parkiran sekolah, gerbang, kehilangan barang, dan ketertiban umum',
            'default_role_target' => 'petugas',
            'is_active' => true,
        ]);

        // 3. Sample Complaints with full workflow states

        // Ticket 1: Baru masuk (Menunggu Verifikasi Guru Piket)
        $c1 = Complaint::create([
            'ticket_code' => 'PGD-202608-0001',
            'user_id' => $siswa->id,
            'reporter_name' => $siswa->name,
            'reporter_nisn' => $siswa->nisn_nip,
            'reporter_phone' => $siswa->phone,
            'reporter_email' => $siswa->email,
            'reporter_class' => 'XI MIPA 2',
            'is_anonymous' => false,
            'category_id' => $catSarpras->id,
            'title' => 'Proyektor Ruang Lab Komputer 2 Mati Total & Kabel Power Putus',
            'description' => 'Saat jam praktikum Informatika, proyektor LCD di langit-langit Lab Komputer 2 tiba-tiba mati mendadak dan ada bau sangit. Kami cek stop kontak kabelnya kendur dan lampu indikator tidak mau menyala.',
            'location' => 'Lab Komputer 2 Gedung Timur Lantai 2',
            'priority' => 'tinggi',
            'status' => 'menunggu_verifikasi',
            'created_at' => now()->subHours(3),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c1->id,
            'user_id' => $siswa->id,
            'actor_name' => $siswa->name,
            'actor_role' => 'Siswa',
            'action' => 'dibuat',
            'status_from' => null,
            'status_to' => 'menunggu_verifikasi',
            'notes' => 'Pengaduan baru diajukan oleh siswa.',
            'created_at' => now()->subHours(3),
        ]);

        // Ticket 2: Didisposisikan (Guru piket sudah verifikasi & tugaskan ke Sarpras)
        $c2 = Complaint::create([
            'ticket_code' => 'PGD-202608-0002',
            'user_id' => $siswa2->id,
            'reporter_name' => 'Siswa Anonim (Dirahasiakan)',
            'reporter_nisn' => null,
            'reporter_phone' => null,
            'reporter_email' => null,
            'reporter_class' => 'X IPS 1',
            'is_anonymous' => true,
            'category_id' => $catSarpras->id,
            'title' => 'AC Ruang Kelas X IPS 1 Bocor Air Menetes ke Meja Siswa',
            'description' => 'AC unit di bagian belakang kelas X IPS 1 bocor parah dan meneteskan air ke meja baris 3. Suara blower juga berderit kencang sehingga mengganggu konsentrasi belajar.',
            'location' => 'Ruang Kelas X IPS 1 Lantai 1',
            'priority' => 'sedang',
            'status' => 'didisposisikan',
            'assigned_to' => $petugasSarpras->id,
            'assigned_by' => $guruPiket->id,
            'verified_at' => now()->subHours(12),
            'assigned_at' => now()->subHours(12),
            'created_at' => now()->subHours(14),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c2->id,
            'user_id' => null,
            'actor_name' => 'Siswa Anonim',
            'actor_role' => 'Siswa',
            'action' => 'dibuat',
            'status_from' => null,
            'status_to' => 'menunggu_verifikasi',
            'notes' => 'Pengaduan diajukan secara anonim.',
            'created_at' => now()->subHours(14),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c2->id,
            'user_id' => $guruPiket->id,
            'actor_name' => $guruPiket->name,
            'actor_role' => 'Guru Piket',
            'action' => 'didisposisikan',
            'status_from' => 'menunggu_verifikasi',
            'status_to' => 'didisposisikan',
            'notes' => 'Laporan valid. Menugaskan Pak Joko (Sarpras) untuk segera memeriksa dan membersihkan talang pembuangan AC.',
            'created_at' => now()->subHours(12),
        ]);

        // Ticket 3: Sedang Diproses (Kasus BK sedang mediasi)
        $c3 = Complaint::create([
            'ticket_code' => 'PGD-202608-0003',
            'user_id' => $siswa->id,
            'reporter_name' => $siswa->name,
            'reporter_nisn' => $siswa->nisn_nip,
            'reporter_phone' => $siswa->phone,
            'reporter_email' => $siswa->email,
            'reporter_class' => 'XI MIPA 2',
            'is_anonymous' => false,
            'category_id' => $catBk->id,
            'title' => 'Indikasi Perundungan Verbal dan Pengucilan di Kantin Belakang',
            'description' => 'Ada sekelompok siswa yang kerap melontarkan kata-kata merendahkan dan menghadang teman saat menuju kantin belakang sekolah pada jam istirahat kedua.',
            'location' => 'Area Lorong Kantin Belakang',
            'priority' => 'darurat',
            'status' => 'diproses',
            'assigned_to' => $guruBk->id,
            'assigned_by' => $guruPiket->id,
            'verified_at' => now()->subDays(1)->subHours(4),
            'assigned_at' => now()->subDays(1)->subHours(4),
            'processed_at' => now()->subDays(1)->subHours(1),
            'created_at' => now()->subDays(1)->subHours(6),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c3->id,
            'user_id' => $siswa->id,
            'actor_name' => $siswa->name,
            'actor_role' => 'Siswa',
            'action' => 'dibuat',
            'status_from' => null,
            'status_to' => 'menunggu_verifikasi',
            'notes' => 'Laporan sensitif diajukan oleh siswa.',
            'created_at' => now()->subDays(1)->subHours(6),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c3->id,
            'user_id' => $guruPiket->id,
            'actor_name' => $guruPiket->name,
            'actor_role' => 'Guru Piket',
            'action' => 'didisposisikan',
            'status_from' => 'menunggu_verifikasi',
            'status_to' => 'didisposisikan',
            'notes' => 'Diteruskan ke Tim BK (Pak Bambang) untuk penanganan psikologis dan mediasi tertutup.',
            'created_at' => now()->subDays(1)->subHours(4),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c3->id,
            'user_id' => $guruBk->id,
            'actor_name' => $guruBk->name,
            'actor_role' => 'Guru BK',
            'action' => 'diproses',
            'status_from' => 'didisposisikan',
            'status_to' => 'diproses',
            'notes' => 'Tim BK telah memanggil saksi dan pelapor ke ruang konseling untuk pengambilan keterangan mendalam secara aman.',
            'created_at' => now()->subDays(1)->subHours(1),
        ]);
        ComplaintResponse::create([
            'complaint_id' => $c3->id,
            'user_id' => $guruBk->id,
            'sender_name' => $guruBk->name,
            'sender_role' => 'Guru BK',
            'message' => 'Terima kasih atas keberanianmu melapor. Tim BK menjamin kerahasiaan identitas dan sedang mengambil langkah pembinaan preventif bersama wali kelas.',
            'is_internal' => false,
            'created_at' => now()->subDays(1)->subHours(1),
        ]);

        // Ticket 4: Menunggu Persetujuan Kepsek (Petugas sarpras sudah perbaiki & unggah laporan)
        $c4 = Complaint::create([
            'ticket_code' => 'PGD-202608-0004',
            'user_id' => $siswa2->id,
            'reporter_name' => $siswa2->name,
            'reporter_nisn' => $siswa2->nisn_nip,
            'reporter_phone' => $siswa2->phone,
            'reporter_email' => $siswa2->email,
            'reporter_class' => 'X IPS 1',
            'is_anonymous' => false,
            'category_id' => $catKebersihan->id,
            'title' => 'Pintu Kamar Mandi Siswa Gedung B Rusak dan Kran Air Patah',
            'description' => 'Engsel pintu toilet siswa pria bilik nomor 2 terlepas dan kran wastafel patah sehingga air meluap ke lantai.',
            'location' => 'Toilet Siswa Gedung B Lantai 1',
            'priority' => 'tinggi',
            'status' => 'menunggu_persetujuan',
            'assigned_to' => $petugasSarpras->id,
            'assigned_by' => $guruPiket->id,
            'verified_at' => now()->subDays(2),
            'assigned_at' => now()->subDays(2),
            'processed_at' => now()->subDays(1),
            'submitted_for_approval_at' => now()->subHours(4),
            'resolution_notes' => 'Telah dilakukan penggantian engsel stainless heavy-duty baru, pemasangan kunci selot baru, serta penggantian kran wastafel kuningan 1/2 inch. Aliran air sudah lancar dan tidak ada kebocoran.',
            'created_at' => now()->subDays(2)->subHours(5),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c4->id,
            'user_id' => $siswa2->id,
            'actor_name' => $siswa2->name,
            'actor_role' => 'Siswa',
            'action' => 'dibuat',
            'status_from' => null,
            'status_to' => 'menunggu_verifikasi',
            'notes' => 'Pengaduan fasilitas toilet rusak.',
            'created_at' => now()->subDays(2)->subHours(5),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c4->id,
            'user_id' => $petugasSarpras->id,
            'actor_name' => $petugasSarpras->name,
            'actor_role' => 'Petugas Sarpras',
            'action' => 'diajukan_persetujuan',
            'status_from' => 'diproses',
            'status_to' => 'menunggu_persetujuan',
            'notes' => 'Perbaikan selesai dilaksanakan. Mengajukan laporan berita acara penyelesaian ke Kepala Sekolah.',
            'created_at' => now()->subHours(4),
        ]);

        // Ticket 5: Selesai (Kepsek menyetujui, Siswa memberi rating 5 bintang)
        $c5 = Complaint::create([
            'ticket_code' => 'PGD-202608-0005',
            'user_id' => $siswa->id,
            'reporter_name' => $siswa->name,
            'reporter_nisn' => $siswa->nisn_nip,
            'reporter_phone' => $siswa->phone,
            'reporter_email' => $siswa->email,
            'reporter_class' => 'XI MIPA 2',
            'is_anonymous' => false,
            'category_id' => $catSarpras->id,
            'title' => 'Lampu Koridor Menuju Perpustakaan Sering Berkedip & Mati',
            'description' => 'Lampu LED di lorong penghubung perpustakaan utama mati sejak 2 hari lalu sehingga gelap saat sore hari ketika siswa belajar di perpus.',
            'location' => 'Lorong Penghubung Gedung C ke Perpustakaan',
            'priority' => 'sedang',
            'status' => 'selesai',
            'assigned_to' => $petugasSarpras->id,
            'assigned_by' => $guruPiket->id,
            'verified_at' => now()->subDays(4),
            'assigned_at' => now()->subDays(4),
            'processed_at' => now()->subDays(3),
            'submitted_for_approval_at' => now()->subDays(2),
            'resolved_at' => now()->subDays(1),
            'resolution_notes' => 'Penggantian 2 unit bohlam LED Philips 18 Watt dan perapihan saklar otomatis lorong.',
            'approval_notes' => 'Laporan perbaikan telah ditinjau dan disetujui. Bukti foto valid dan lorong sudah kembali terang.',
            'satisfaction_rating' => 5,
            'satisfaction_feedback' => 'Terima kasih banyak atas respon cepatnya! Lorong sekarang sudah terang benderang dan nyaman dilewati saat sore.',
            'feedback_submitted_at' => now()->subDays(1)->addHours(2),
            'created_at' => now()->subDays(5),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c5->id,
            'user_id' => $kepsek->id,
            'actor_name' => $kepsek->name,
            'actor_role' => 'Kepala Sekolah',
            'action' => 'disetujui',
            'status_from' => 'menunggu_persetujuan',
            'status_to' => 'selesai',
            'notes' => 'Kepala Sekolah menyetujui penutupan pengaduan.',
            'created_at' => now()->subDays(1),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c5->id,
            'user_id' => $siswa->id,
            'actor_name' => $siswa->name,
            'actor_role' => 'Siswa',
            'action' => 'feedback_diberikan',
            'status_from' => 'selesai',
            'status_to' => 'selesai',
            'notes' => 'Siswa memberikan penilaian 5/5 Bintang.',
            'created_at' => now()->subDays(1)->addHours(2),
        ]);

        // Ticket 6: Ditolak (Laporan tidak valid oleh Guru Piket)
        $c6 = Complaint::create([
            'ticket_code' => 'PGD-202608-0006',
            'user_id' => null,
            'reporter_name' => 'Tamu Sekolah',
            'reporter_phone' => '081299999999',
            'is_anonymous' => true,
            'category_id' => $catKeamanan->id,
            'title' => 'Helm Hilang di Tempat Parkir Luar Sekolah',
            'description' => 'Saya memarkir motor di warung seberang luar pagar sekolah dan helm saya hilang.',
            'location' => 'Luar Pagar Sekolah / Warung Seberang Jalan',
            'priority' => 'rendah',
            'status' => 'ditolak',
            'assigned_by' => $guruPiket->id,
            'verified_at' => now()->subDays(3),
            'rejection_reason' => 'Pengaduan di luar wilayah yurisdiksi dan tanggung jawab keamanan internal sekolah. Siswa disarankan memarkir kendaraan di dalam area parkir resmi sekolah yang dijaga petugas satpam.',
            'created_at' => now()->subDays(3)->subHours(2),
        ]);
        ComplaintLog::create([
            'complaint_id' => $c6->id,
            'user_id' => $guruPiket->id,
            'actor_name' => $guruPiket->name,
            'actor_role' => 'Guru Piket',
            'action' => 'ditolak',
            'status_from' => 'menunggu_verifikasi',
            'status_to' => 'ditolak',
            'notes' => 'Laporan ditolak dan diarsipkan: Di luar yurisdiksi keamanan sekolah.',
            'created_at' => now()->subDays(3),
        ]);
    }
}
