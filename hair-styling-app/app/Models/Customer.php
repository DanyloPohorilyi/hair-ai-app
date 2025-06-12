<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $table = 'customer';
    protected $primaryKey = 'user_id';
    protected $fillable = ['name', 'email', 'password', 'photo', 'is_admin'];
    protected $hidden = ['password'];

    protected $casts = [
        'is_admin' => 'boolean', // Переконуємося, що це завжди true/false
    ];
    public function images()
    {
        return $this->hasMany(Image::class, 'user_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'user_id');
    }
}
