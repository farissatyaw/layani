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
        return $this->belongsToMany(Tag::class, 'complaint_tag', 'complaint_id', 'tag_id');
    }

    public function accept(User $user)
    {
        $this->admin_id = $user->id;
        $this->datetaken = now();
        $this->status = 'inprogress';
        $this->save();
    }
}
