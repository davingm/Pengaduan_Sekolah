<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'user_id',
        'reporter_name',
        'reporter_nisn',
        'reporter_phone',
        'reporter_email',
        'reporter_class',
        'is_anonymous',
        'category_id',
        'title',
        'description',
        'location',
        'priority',
        'status',
        'assigned_to',
        'assigned_by',
        'verified_at',
        'assigned_at',
        'processed_at',
        'submitted_for_approval_at',
        'resolved_at',
        'rejection_reason',
        'resolution_notes',
        'approval_notes',
        'satisfaction_rating',
        'satisfaction_feedback',
        'feedback_submitted_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'verified_at' => 'datetime',
        'assigned_at' => 'datetime',
        'processed_at' => 'datetime',
        'submitted_for_approval_at' => 'datetime',
        'resolved_at' => 'datetime',
        'feedback_submitted_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedByGuru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    public function evidenceAttachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class)->where('type', 'evidence');
    }

    public function resolutionAttachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class)->where('type', 'resolution');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ComplaintLog::class)->orderBy('created_at', 'asc');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ComplaintResponse::class)->orderBy('created_at', 'asc');
    }

    // Status formatting helpers
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi Guru Piket',
            'ditolak' => 'Ditolak / Diarsipkan',
            'didisposisikan' => 'Didisposisikan ke Petugas',
            'diproses' => 'Sedang Ditangani Petugas',
            'menunggu_persetujuan' => 'Menunggu Persetujuan Kepsek',
            'selesai' => 'Selesai & Ditutup',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800 border-amber-300 ring-amber-500/20',
            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-300 ring-rose-500/20',
            'didisposisikan' => 'bg-indigo-100 text-indigo-800 border-indigo-300 ring-indigo-500/20',
            'diproses' => 'bg-sky-100 text-sky-800 border-sky-300 ring-sky-500/20',
            'menunggu_persetujuan' => 'bg-purple-100 text-purple-800 border-purple-300 ring-purple-500/20',
            'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-300 ring-emerald-500/20',
            default => 'bg-slate-100 text-slate-800 border-slate-300 ring-slate-500/20',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'amber',
            'ditolak' => 'rose',
            'didisposisikan' => 'indigo',
            'diproses' => 'sky',
            'menunggu_persetujuan' => 'purple',
            'selesai' => 'emerald',
            default => 'slate',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'darurat' => 'bg-rose-500 text-white shadow-rose-500/30',
            'tinggi' => 'bg-orange-500 text-white shadow-orange-500/30',
            'sedang' => 'bg-amber-500 text-white shadow-amber-500/30',
            'rendah' => 'bg-slate-500 text-white shadow-slate-500/30',
            default => 'bg-slate-400 text-white',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'darurat' => 'Darurat / Sangat Mendesak',
            'tinggi' => 'Tinggi',
            'sedang' => 'Sedang',
            'rendah' => 'Rendah',
            default => ucfirst($this->priority),
        };
    }

    // Step progress helper (1-4)
    public function getProgressStepAttribute(): int
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 1,
            'ditolak' => 1,
            'didisposisikan' => 2,
            'diproses' => 2,
            'menunggu_persetujuan' => 3,
            'selesai' => 4,
            default => 1,
        };
    }
}
