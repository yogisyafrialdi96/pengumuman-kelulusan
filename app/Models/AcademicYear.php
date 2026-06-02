<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'announcement_datetime',
        'is_published',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_active' => 'boolean',
            'announcement_datetime' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get years that are currently accessible to the public.
     * A year is accessible if it's manually published OR if the
     * scheduled announcement datetime has passed.
     */
    public function scopeAccessible(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('is_published', true)
              ->orWhere(function (Builder $q2) {
                  $q2->whereNotNull('announcement_datetime')
                     ->where('announcement_datetime', '<=', Carbon::now());
              });
        });
    }

    // ── Accessors ──────────────────────────────────────────────

    /**
     * Check if this academic year's announcement is currently accessible.
     */
    public function getIsAccessibleAttribute(): bool
    {
        if ($this->is_published) {
            return true;
        }

        if ($this->announcement_datetime !== null) {
            return Carbon::now()->gte($this->announcement_datetime);
        }

        return false;
    }

    /**
     * Get the publish status label for display.
     */
    public function getPublishStatusLabelAttribute(): string
    {
        if ($this->is_published) {
            return 'Dipublikasikan';
        }

        if ($this->announcement_datetime !== null) {
            if (Carbon::now()->gte($this->announcement_datetime)) {
                return 'Otomatis Terbuka';
            }

            return 'Dijadwalkan: ' . $this->announcement_datetime->translatedFormat('d M Y, H:i');
        }

        return 'Belum Dipublikasikan';
    }

    // ── Methods ────────────────────────────────────────────────

    public function publish(): void
    {
        $this->update(['is_published' => true]);
    }

    public function unpublish(): void
    {
        $this->update(['is_published' => false]);
    }

    public function activate(): void
    {
        static::where('is_active', true)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
