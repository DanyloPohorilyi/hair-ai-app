<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hairstyle extends Model
{
    use HasFactory;

    protected $table = 'hairstyle';
    protected $primaryKey = 'hairstyle_id';
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'image_path', 'recommended_for'];
}
