<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedImage extends Model
{
    use HasFactory;

    protected $table = 'processed_image';
    protected $primaryKey = 'processed_image_id';
    protected $fillable = ['image_id', 'processed_image_path', 'processing_status', 'image_type'];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
