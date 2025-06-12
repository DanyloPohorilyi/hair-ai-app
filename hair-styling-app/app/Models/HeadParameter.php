<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadParameter extends Model
{
    use HasFactory;

    protected $table = 'head_parameters';
    protected $primaryKey = 'head_param_id';
    protected $fillable = ['image_id', 'width', 'height', 'face_shape', 'aspect_ratio'];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
