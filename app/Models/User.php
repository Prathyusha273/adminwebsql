<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = [];

    // Disable Laravel's automatic timestamp management
    public $timestamps = false;

    protected $casts = [
        'isActive' => 'boolean',
        'wallet_amount' => 'integer',
        'orderCompleted' => 'integer',
    ];
}
