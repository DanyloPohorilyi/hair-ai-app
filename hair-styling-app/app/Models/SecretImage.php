<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecretImage extends Model
{
    use HasFactory;

    protected $table = 'secret_table';
    protected $primaryKey = 'image_id';
    public $timestamps = false;

    protected $fillable = ['original_path', 'generated_path', 'haircut'];
}
