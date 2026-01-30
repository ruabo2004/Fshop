<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualTryon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_image',
        'garment_image',
        'result_image',
        'fitroom_task_id',
        'clothing_type',
        'quality',
        'status',
        'processing_time',
        'error_message',
        'credits_used',
        'is_public',
        'share_token',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'credits_used' => 'decimal:2',
        'processing_time' => 'integer',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full URL for user image
     */
    public function getUserImageUrlAttribute()
    {
        return asset('storage/' . $this->user_image);
    }

    /**
     * Get full URL for garment image
     */
    public function getGarmentImageUrlAttribute()
    {
        return asset('storage/' . $this->garment_image);
    }

    /**
     * Get full URL for result image
     */
    public function getResultImageUrlAttribute()
    {
        return $this->result_image ? asset($this->result_image) : null;
    }

    /**
     * Check if try-on is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if try-on is processing
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if try-on failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Generate share token
     */
    public function generateShareToken()
    {
        $this->share_token = bin2hex(random_bytes(32));
        $this->save();
        return $this->share_token;
    }

    /**
     * Scope for public try-ons
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for completed try-ons
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
