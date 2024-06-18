<?php

namespace App\Models\LMS;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;
    protected $table = "forum_replies";
    protected $guarded = ["id"];

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'id_class');
    }

    public function user()
    {
        return $this->belongsTo(User::class, "id_user");
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, "id_lecturer");
    }
}
