<?php

namespace App\Models;

use App\Models\NewsLabels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;
    protected $table = 'news';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function newsLabels()
    {
        return $this->hasMany(NewsLabels::class, 'id_news');
    }
}
