<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyPoint extends Model
{
    use HasFactory;

    protected $table = 'key_points';
    protected $primaryKey = 'keypoint_id';
    protected $fillable = ['image_id', 'type', 'x_coordinate', 'y_coordinate'];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
