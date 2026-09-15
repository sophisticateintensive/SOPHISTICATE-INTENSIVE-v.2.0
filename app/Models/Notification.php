<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'term_id',
        'admin_id',
        'title',
        'message',
        'is_sent_to_all',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_sent_to_all' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where(function ($q) use ($studentId) {
            $q->where('student_id', $studentId)
              ->orWhere('is_sent_to_all', true);
        });
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
