<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'notification';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'content',
        'user_id',
        'type', // 0=GENERAL;1=GIGS;2=GUILD;3=LEVELUP
        'icon_type', // 1=DRESS;2=TOOLS;3=CAMERA;4=PHONE;5=JOB;6=DOLLAR;,
        'is_seen',
        'url'
    ];

    protected $dates = ['deleted_at'];
}
