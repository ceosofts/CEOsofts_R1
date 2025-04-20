<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'tax_id',
        'status',
        'metadata',
        'contact_person',
        'website',
        'note',
        'type', // individual, company
        'code',
        'credit_limit',
        // เพิ่มฟิลด์ใหม่
        'contact_person_position',
        'contact_person_email',
        'contact_person_phone',
        'contact_person_line_id',
        'payment_term_type',
        'discount_rate',
        'reference_id',
        'social_media',
        'customer_group',
        'customer_rating',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'bank_branch',
        'is_supplier',
        'last_contacted_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'metadata' => 'json',
        'social_media' => 'json',
        'credit_limit' => 'float',
        'discount_rate' => 'float',
        'is_supplier' => 'boolean',
        'last_contacted_date' => 'date',
    ];

    /**
     * Get the company that owns the customer.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the quotations for the customer.
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the credit term from metadata.
     */
    public function getCreditTermAttribute()
    {
        if (is_string($this->metadata)) {
            $metadata = json_decode($this->metadata, true);
            return $metadata['credit_term'] ?? null;
        }
        
        return $this->metadata['credit_term'] ?? null;
    }

    /**
     * Get the industry from metadata.
     */
    public function getIndustryAttribute()
    {
        if (is_string($this->metadata)) {
            $metadata = json_decode($this->metadata, true);
            return $metadata['industry'] ?? null;
        }
        
        return $this->metadata['industry'] ?? null;
    }

    /**
     * Get the sales region from metadata.
     */
    public function getSalesRegionAttribute()
    {
        if (is_string($this->metadata)) {
            $metadata = json_decode($this->metadata, true);
            return $metadata['sales_region'] ?? null;
        }
        
        return $this->metadata['sales_region'] ?? null;
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
    
    /**
     * Get total purchase amount.
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->orders()->sum('total_amount');
    }
    
    /**
     * Get last order date.
     */
    public function getLastOrderDateAttribute()
    {
        $lastOrder = $this->orders()->latest('order_date')->first();
        return $lastOrder ? $lastOrder->order_date : null;
    }

    /**
     * Get the full contact information.
     */
    public function getFullContactInfoAttribute()
    {
        $info = [];
        
        if ($this->contact_person) {
            $info[] = $this->contact_person;
            
            if ($this->contact_person_position) {
                $info[0] .= " ({$this->contact_person_position})";
            }
        }
        
        if ($this->contact_person_phone) {
            $info[] = "Tel: {$this->contact_person_phone}";
        }
        
        if ($this->contact_person_email) {
            $info[] = "Email: {$this->contact_person_email}";
        }
        
        if ($this->contact_person_line_id) {
            $info[] = "LINE: {$this->contact_person_line_id}";
        }
        
        return !empty($info) ? implode(' | ', $info) : null;
    }

    /**
     * Get formatted payment term info.
     */
    public function getPaymentTermInfoAttribute()
    {
        $info = ucfirst($this->payment_term_type ?: 'credit');
        
        if ($this->payment_term_type == 'credit' && $this->credit_term) {
            $info .= " ({$this->credit_term} วัน)";
        }
        
        return $info;
    }

    /**
     * Get social media as decoded array.
     */
    public function getSocialMediaArrayAttribute()
    {
        if (is_string($this->social_media)) {
            return json_decode($this->social_media, true) ?: [];
        }
        
        return $this->social_media ?: [];
    }

    /**
     * สร้างรหัสลูกค้าอัตโนมัติตามรูปแบบ CUSyyyyMMxxxx
     *
     * @return string
     */
    public static function generateCustomerCode(): string
    {
        $prefix = 'CUS';
        $now = Carbon::now();
        $year = $now->format('Y');
        $month = $now->format('m');
        
        // ดึงลูกค้าล่าสุดในเดือนและปีนี้
        $latestCustomer = self::where('code', 'like', $prefix . $year . $month . '%')
            ->orderBy('code', 'desc')
            ->first();
            
        // หากไม่มีลูกค้าในเดือนนี้ เริ่มที่ 0001
        if (!$latestCustomer) {
            $nextNumber = '0001';
        } else {
            // ตัดเอาเฉพาะตัวเลข 4 ตัวสุดท้าย และเพิ่มอีก 1
            $lastNumber = (int) substr($latestCustomer->code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $year . $month . $nextNumber;
    }
}
