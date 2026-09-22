<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $referrer_master_id
 * @property int $referred_master_id
 * @property int $status
 * @property string $created_at
 *
 * @property Master $referrerMaster
 * @property Master $referredMaster
 * @property array<ReferralEarning> $earnings
 */
class Referral extends Model
{
    use HasFactory;

    public const PROGRAM_MASTER_INVITE = 'master_invite';
    public const PROGRAM_INFLUENCER = 'influencer';

    public const STATUS_PENDING = 'pending';
    public const STATUS_REWARDED = 'rewarded';

    protected $fillable = [
        'referrer_master_id',
        'referred_master_id',
        'status',
    ];

    public function referrerMaster(): BelongsTo
    {
        return $this->belongsTo(Master::class, 'referrer_master_id');
    }

    public function referredMaster(): BelongsTo
    {
        return $this->belongsTo(Master::class, 'referred_master_id');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(ReferralEarning::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REWARDED);
    }
}
