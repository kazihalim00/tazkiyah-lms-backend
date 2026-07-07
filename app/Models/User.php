<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import the HasApiTokens trait from Sanctum
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Add HasApiTokens here along with HasFactory and Notifiable
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'role',
        'timezone',
        'gender',
        'total_points',
        'last_read_ayah_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function ibadahTrackers()
    {
        return $this->hasMany(IbadahTracker::class);
    }

    public function spiritualJournals()
    {
        return $this->hasMany(SpiritualJournal::class);
    }

    // Relationship: A user has many chat logs with Noor AI
    public function chatLogs()
    {
        return $this->hasMany(ChatLog::class);
    }
    // App\Models\User.php
    public function companions()
    {
        return $this->belongsToMany(User::class, 'companions', 'user_id', 'partner_id')
            ->withPivot('status')
            ->withTimestamps();
    }
    // Calculate badge and icon dynamically based on points and gender
    // Calculate badge, icon, and tree growth stage dynamically
    public function getBadgeAttribute()
    {
        $points = $this->total_points ?? 0;
        $isFemale = strtolower($this->gender) === 'female';

        // Level 7: The True Believer (Tree Stage 8 - Fully grown glowing tree)
        if ($points >= 250000) {
            return [
                'name' => $isFemale ? 'Muminah' : 'Mumin',
                'icon' => '👑',
                'tree_stage' => 8
            ];
        }
        // Level 6: The Truthful (Tree Stage 7 - Tree with flowers/fruits)
        elseif ($points >= 100000) {
            return [
                'name' => $isFemale ? 'Siddiqah' : 'Siddiq',
                'icon' => '🌟',
                'tree_stage' => 7
            ];
        }
        // Level 5: The Doer of Excellence (Tree Stage 6 - Large beautiful tree)
        elseif ($points >= 50000) {
            return [
                'name' => $isFemale ? 'Muhsinah' : 'Muhsin',
                'icon' => '🕊️',
                'tree_stage' => 6
            ];
        }
        // Level 4: The God-fearing/Pious (Tree Stage 5 - Tree with dense leaves)
        elseif ($points >= 20000) {
            return [
                'name' => $isFemale ? 'Muttaqiyah' : 'Muttaqi',
                'icon' => '🛡️',
                'tree_stage' => 5
            ];
        }
        // Level 3: The Striver (Tree Stage 4 - Growing small tree)
        elseif ($points >= 8000) {
            return [
                'name' => $isFemale ? 'Mujahidah' : 'Mujahid',
                'icon' => '⚔️',
                'tree_stage' => 4
            ];
        }
        // Level 2: The Devoted Worshipper (Tree Stage 3 - Plant with multiple leaves)
        elseif ($points >= 3000) {
            return [
                'name' => $isFemale ? 'Abidah' : 'Abid',
                'icon' => '📿',
                'tree_stage' => 3
            ];
        }
        // Level 1: The Seeker of Knowledge (Tree Stage 2 - Small sprout)
        elseif ($points >= 1000) {
            return [
                'name' => $isFemale ? 'Talibah' : 'Talib',
                'icon' => '📖',
                'tree_stage' => 2
            ];
        }

        // Level 0: Default beginner (Tree Stage 1 - Just a seed/soil)
        return [
            'name' => $isFemale ? 'Mubtadiyah' : 'Mubtadi',
            'icon' => '🌱',
            'tree_stage' => 1
        ];
    }
    public function myPartners()
    {
        return $this->belongsToMany(User::class, 'accountability_partners', 'partner_id', 'user_id')
            ->wherePivot('status', 'accepted');
    }

    public function partneredWith()
    {
        return $this->belongsToMany(User::class, 'accountability_partners', 'user_id', 'partner_id')
            ->wherePivot('status', 'accepted');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function getImageUrlAttribute()
    {
        if (!$this->image)
            return null;
        return str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image);
    }
}