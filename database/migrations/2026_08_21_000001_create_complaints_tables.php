<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('folder');
            $table->string('color')->default('blue');
            $table->text('description')->nullable();
            $table->string('default_role_target')->nullable(); // sarpras, bk, wali_kelas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Complaints (Pengaduan)
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique(); // e.g. PGD-202608-0001
            
            // Reporter Info (Support anonymous or guest reporting)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_name');
            $table->string('reporter_nisn')->nullable();
            $table->string('reporter_phone')->nullable();
            $table->string('reporter_email')->nullable();
            $table->string('reporter_class')->nullable(); // e.g. X MIPA 1
            $table->boolean('is_anonymous')->default(false);

            // Complaint Details
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable(); // e.g. Lab Komputer 2, Kelas XII IPS 3
            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            
            // Status Workflow:
            // menunggu_verifikasi -> (ditolak | didisposisikan) -> diproses -> menunggu_persetujuan -> selesai
            $table->enum('status', [
                'menunggu_verifikasi',
                'ditolak',
                'didisposisikan',
                'diproses',
                'menunggu_persetujuan',
                'selesai'
            ])->default('menunggu_verifikasi');

            // Handling & Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Petugas terkait
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete(); // Guru piket
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('submitted_for_approval_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            // Rejection / Notes
            $table->text('rejection_reason')->nullable();
            $table->text('resolution_notes')->nullable(); // Catatan penanganan petugas
            $table->text('approval_notes')->nullable(); // Catatan persetujuan kepsek

            // Student / Complainant Satisfaction Feedback
            $table->tinyInteger('satisfaction_rating')->nullable(); // 1 - 5
            $table->text('satisfaction_feedback')->nullable();
            $table->timestamp('feedback_submitted_at')->nullable();

            $table->timestamps();
        });

        // 3. Attachments (Bukti Foto Awal & Bukti Penyelesaian)
        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable(); // image/jpeg, pdf, etc.
            $table->bigInteger('file_size')->nullable();
            $table->enum('type', ['evidence', 'resolution'])->default('evidence');
            $table->timestamps();
        });

        // 4. Activity Logs / Audit Trail Timeline
        Schema::create('complaint_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('actor_role')->nullable();
            $table->string('action'); // dibuat, diverifikasi, ditolak, didisposisikan, diproses, diajukan_persetujuan, disetujui, feedback_diberikan
            $table->string('status_from')->nullable();
            $table->string('status_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Discussion / Responses / Progress Notes
        Schema::create('complaint_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_name');
            $table->string('sender_role');
            $table->text('message');
            $table->boolean('is_internal')->default(false); // Internal staff note or visible to complainant
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_responses');
        Schema::dropIfExists('complaint_logs');
        Schema::dropIfExists('complaint_attachments');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('categories');
    }
};
