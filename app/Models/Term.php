<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'term_name',
        'is_locked',
        'is_current',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_current' => 'boolean',
    ];

    /**
     * Get the academic year that owns this term
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all results for this term
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Scope for current active term
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope for unlocked terms
     */
    public function scopeActive($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Get the current active term model
     */
    public static function current()
    {
        return static::with('academicYear')->where('is_current', true)->first()
            ?? static::with('academicYear')->where('is_locked', false)->orderBy('id', 'desc')->first()
            ?? static::with('academicYear')->orderBy('id', 'desc')->first();
    }

    /**
     * Get active term ID
     */
    public static function getActiveTermId()
    {
        return static::current()?->id;
    }

    /**
     * Get active academic year ID
     */
    public static function getActiveAcademicYearId()
    {
        return static::current()?->academic_year_id ?? AcademicYear::current()?->id;
    }

    /**
     * Atomically set this term as the active semester
     */
    public function setAsCurrent()
    {
        // Unset current flag on all terms
        static::query()->update(['is_current' => false]);
        
        // Set this term as current and unlock it
        $this->update([
            'is_current' => true,
        ]);

        // Unset current flag on all academic years and set parent as current
        AcademicYear::query()->update(['is_current' => false]);
        if ($this->academic_year_id) {
            AcademicYear::where('id', $this->academic_year_id)->update(['is_current' => true]);
        }

        return $this;
    }
}