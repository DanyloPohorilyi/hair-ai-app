<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'image';
    protected $primaryKey = 'image_id';
    protected $fillable = ['user_id', 'image_path', 'image_size', 'image_format'];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function headParameters()
    {
        return $this->hasOne(HeadParameter::class, 'image_id');
    }

    public function processedImages()
    {
        return $this->hasMany(ProcessedImage::class, 'image_id');
    }

    public function keyPoints()
    {
        return $this->hasMany(KeyPoint::class, 'image_id');
    }
}
