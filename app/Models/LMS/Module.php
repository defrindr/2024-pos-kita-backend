<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = 'modules';
    protected $guarded = ['id'];

    public function topic()
    {
        return $this->hasMany(Topics::class, 'id_module');
    }
}
