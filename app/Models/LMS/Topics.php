<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topics extends Model
{
    use HasFactory;
    protected $table = 'topics';
    protected $guarded = ['id'];

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'id_topic');
    }

    public function question()
    {
        return $this->hasMany(Question::class, 'id_topic');
    }

    public function material()
    {
        return $this->hasMany(TopicMaterials::class, 'id_topic');
    }
}
