<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'logo',
        'address',
        'latitude',
        'longitude',
        'phone',
        'whatsapp_number',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the owner (user) that owns the store.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the offers for the store.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Get the live streams for the store.
     */
    public function liveStreams(): HasMany
    {
        return $this->hasMany(LiveStream::class);
    }

    /**
     * Get the active live stream for the store.
     */
    public function activeStream()
    {
        return $this->hasOne(LiveStream::class)->where('is_active', true)->latest();
    }

    /**
     * Scope to only active stores.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}