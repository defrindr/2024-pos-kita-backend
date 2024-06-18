<?php

namespace App\Models\LMS;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassMember extends Model
{
    use HasFactory;
    protected $table = 'class_members';
    protected $guarded = ['id'];

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
