<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AccountabilityPartner
 *
 * @property int $id
 * @property int $user_id
 * @property int $partner_id
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class AccountabilityPartner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'partner_id',
        'status',
    ];

    /**
     * Get the user who sent the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the partner who received the request.
     */
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}