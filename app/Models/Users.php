<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $table = 'qrt_users';
    protected $fillable = ['real_name', 'real_surname', 'username', 'password', 'email', 'phone', 'identification_number', 'nationality', 'user_type', 'ip_address', 'loggedAt', 'createdAt'];
    protected $guarded = ['id'];
    public $primaryKey = 'user_id';
    public $timestamps = false;
}
