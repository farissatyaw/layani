<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'tittle',
        'body',
        'location',
        'photo',
        'isUserGenerated',
        'status',
        'user_id',
        'admin_id',
        'datetaken',
    ];

    public function tags()
    {
        return $this->belongsToMany(App\Models\Tag::class);
    }
}
