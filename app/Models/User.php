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
    public function getLevelAttribute()
    {
        $points = $this->total_points ?? 0;

        if ($points >= 5000)
            return 'Mumin (মুমিন)';
        if ($points >= 2500)
            return 'Devoted (নিবেদিত)';
        if ($points >= 1000)
            return 'Seeker (অনুসন্ধানী)';
        if ($points >= 300)
            return 'Consistent (ধারাবাহিক)';

        return 'Beginner (শিক্ষানবিস)';
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
}