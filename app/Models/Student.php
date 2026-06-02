<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'nis',
        'nisn',
        'nama_siswa',
        'nama_orang_tua',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'status' => StudentStatus::class,
            'tanggal_lahir' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeByStatus(Builder $query, StudentStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nama_siswa', 'like', "%{$keyword}%")
              ->orWhere('nis', 'like', "%{$keyword}%")
              ->orWhere('nisn', 'like', "%{$keyword}%")
              ->orWhere('nama_orang_tua', 'like', "%{$keyword}%");
        });
    }

    /**
     * Search student for public announcement by NIS/NISN + tanggal lahir.
     */
    public function scopePublicSearch(Builder $query, string $identifier, string $tanggalLahir): Builder
    {
        return $query->where(function (Builder $q) use ($identifier) {
            $q->where('nis', $identifier)
              ->orWhere('nisn', $identifier);
        })->where('tanggal_lahir', $tanggalLahir);
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getFormattedTanggalLahirAttribute(): string
    {
        return $this->tanggal_lahir->translatedFormat('d F Y');
    }

    public function getTempatTanggalLahirAttribute(): string
    {
        return "{$this->tempat_lahir}, {$this->formatted_tanggal_lahir}";
    }
}
