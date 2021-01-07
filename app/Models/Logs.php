<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    protected $table = 'qrt_token_logs';
    protected $fillable = ['token', 'headers', 'body', 'url', 'tarih'];
    protected $guarded = ['id'];
    public $primaryKey = 'log_id';
    public $timestamps = false;
}
