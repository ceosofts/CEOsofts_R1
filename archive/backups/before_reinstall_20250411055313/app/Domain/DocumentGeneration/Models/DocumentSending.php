<?php

namespace App\Domain\DocumentGeneration\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSending extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'document_id',
        'document_type',
        'sent_at',
        'sent_by',
        'sent_to',
        'sent_cc',
        'sent_bcc',
        'subject',
        'body',
        'status',
        'result',
        'tracking_id',
        'error_message',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the company that owns this sending record.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the document that was sent.
     */
    public function document()
    {
        return $this->belongsTo(GeneratedDocument::class, 'document_id');
    }

    /**
     * Get the user who sent this document.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope a query to only include sendings of a specific document type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to only include sendings with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include sendings to a specific email.
     */
    public function scopeSentTo($query, string $email)
    {
        return $query->where('sent_to', 'like', '%' . $email . '%');
    }

    /**
     * Scope a query to only include sendings within a date range.
     */
    public function scopeSentBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('sent_at', [$startDate, $endDate]);
    }

    /**
     * Check if the document was successfully sent.
     */
    public function wasSuccessful(): bool
    {
        return $this->result === 'sent';
    }

    /**
     * Check if the document was opened.
     */
    public function wasOpened(): bool
    {
        return in_array($this->status, ['opened', 'clicked']);
    }

    /**
     * Check if the document links were clicked.
     */
    public function wasClicked(): bool
    {
        return $this->status === 'clicked';
    }

    /**
     * Check if the sending had an error.
     */
    public function hadError(): bool
    {
        return $this->result === 'error' || !empty($this->error_message);
    }

    /**
     * Format the sent date for display.
     */
    public function formattedSentDate($format = 'd/m/Y H:i'): string
    {
        return $this->sent_at ? $this->sent_at->format($format) : '-';
    }

    /**
     * Get a summary of the sending.
     */
    public function getSummary(): string
    {
        return "Sent to {$this->sent_to} on " . $this->formattedSentDate() . " - Status: {$this->status}";
    }
}
