<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'topic_questions';
    protected $guarded = ['id'];

    public function topic()
    {
        return $this->belongsTo(Topics::class, 'id_topic');
    }
}
