<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class chat extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'chats';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'message'
    ];

    protected $dates = ['deleted_at'];

    public function sender()
    {
        return $this->hasOne('App\Models\User', 'id','from_user_id');
    }

    public function receiver()
    {
        return $this->hasOne('App\Models\User', 'id','to_user_id');
    }

}