<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animals extends Model
{
    use HasFactory;
    protected $table = 'qrt_animals';
    protected $fillable = ['todo_id','product_id', 'animal_code', 'animal_name', 'animal_surname', 'animal_breed', 'animal_sex', 'animal_birth', 'animal_image', 'status', 'tarih'];
    protected $guarded = ['id'];
    public $primaryKey = 'animal_id';
    public $timestamps = false;
}
