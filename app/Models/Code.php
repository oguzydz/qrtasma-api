<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;
    protected $table = 'qrt_animal_code_util';
    protected $fillable = ['util_id', 'code_length'];
    protected $guarded = ['id'];
    public $primaryKey = 'util_id';
    public $timestamps = false;
}
