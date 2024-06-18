<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLabels extends Model
{
    use HasFactory;
    protected $table = 'm_news_label';
    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function label()
    {
        return $this->belongsTo('App\Models\LabelNews', 'id_label');
    }
    public function news()
    {
        return $this->belongsTo('App\Models\News', 'id_news');
    }
}
