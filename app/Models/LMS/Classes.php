<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $guarded = ['id'];

    public function member()
    {
        return $this->hasMany(ClassMember::class, 'class_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topics::class, 'id_topic');
    }

    public function forum()
    {
        return $this->hasMany(Forum::class, 'id_class');
    }
}
