<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function complaints()
    {
        return $this->belongsToMany(Tag::class, 'complaint_tag', 'tag_id', 'complaint_id');
    }
}
