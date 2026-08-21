<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nisn_nip',
        'phone',
        'department',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role helper methods
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isGuruPiket(): bool
    {
        return $this->role === 'guru_piket';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['guru_piket', 'petugas', 'kepala_sekolah', 'admin']);
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'siswa' => 'bg-emerald-100 text-emerald-700 border-emerald-300',
            'guru_piket' => 'bg-amber-100 text-amber-700 border-amber-300',
            'petugas' => 'bg-blue-100 text-blue-700 border-blue-300',
            'kepala_sekolah' => 'bg-purple-100 text-purple-700 border-purple-300',
            'admin' => 'bg-rose-100 text-rose-700 border-rose-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'siswa' => 'Siswa / Pelapor',
            'guru_piket' => 'Guru Piket / BK',
            'petugas' => 'Petugas (' . ($this->department ?? 'Sarpras/Teknisi') . ')',
            'kepala_sekolah' => 'Kepala Sekolah',
            'admin' => 'Administrator',
            default => ucfirst($this->role),
        };
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }
}
