<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'donation_sector',
        'payment_status',
        'trx_id',
        'payment_id'
    ];
}