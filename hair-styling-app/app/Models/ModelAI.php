<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelAI extends Model
{
    use HasFactory;

    protected $table = 'model';
    protected $primaryKey = 'model_id';
    protected $fillable = ['model_type', 'version', 'last_updated'];

    public $timestamps = false;
}
