<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_name',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    /**
     * Get all students for this academic year
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get all terms for this academic year
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get current active academic year
     */
    public static function current()
    {
        return static::where('is_current', true)->first()
            ?? static::orderBy('id', 'desc')->first();
    }

    /**
     * Set this academic year as current
     */
    public function setAsCurrent()
    {
        static::query()->update(['is_current' => false]);
        $this->update(['is_current' => true]);
        return $this;
    }
}
