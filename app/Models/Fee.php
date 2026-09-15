<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'term_id',
        'type',
        'description',
        'amount',
        'paid',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid' => 'decimal:2',
        'due_date' => 'date',
    ];

    /**
     * Get the student associated with this fee
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year associated with this fee
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the term associated with this fee
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the balance for this fee
     */
    public function getBalanceAttribute()
    {
        return $this->amount - $this->paid;
    }

    /**
     * Check if the fee is fully paid
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->balance <= 0;
    }

    /**
     * Check if the fee is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->balance > 0 && $this->due_date < now();
    }

    /**
     * Update status based on payment and due date
     */
    public function updateStatus()
    {
        if ($this->balance <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid > 0 && $this->balance > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date < now()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }

        $this->saveQuietly();

        return $this;
    }

    /**
     * Record a payment
     */
    public function recordPayment($amount)
    {
        $this->paid += $amount;
        $this->updateStatus();

        return $this;
    }

    /**
     * Scope for active fees (not fully paid)
     */
    public function scopeActive($query)
    {
        return $query->whereRaw('paid < amount');
    }

    /**
     * Scope for overdue fees
     */
    public function scopeOverdue($query)
    {
        return $query->whereRaw('paid < amount')
            ->where('due_date', '<', now());
    }

    /**
     * Scope for fees by academic year
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope for fees by term
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }
}