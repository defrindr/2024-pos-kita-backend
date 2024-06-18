<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicMaterials extends Model
{
    use HasFactory;
    protected $table = "material_images";
    protected $guarded = ["id"];

    public function topic()
    {
        return $this->belongsTo(Topics::class, 'id_topic');
    }
}
