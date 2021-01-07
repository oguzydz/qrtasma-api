<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    use HasFactory;
    protected $table = 'qrt_auth';
    protected $fillable = ['token', 'user_id', 'created_date', 'expire_date'];
    protected $guarded = ['id'];
    public $primaryKey = 'token_id';
    public $timestamps = false;
}
