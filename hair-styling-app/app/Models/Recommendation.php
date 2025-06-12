<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $table = 'recommendation';
    protected $primaryKey = 'recommendation_id';
    protected $fillable = ['user_id', 'image_id', 'hairstyle_id', 'confidence_score'];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function hairstyle()
    {
        return $this->belongsTo(Hairstyle::class, 'hairstyle_id');
    }
}
