<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentReplies extends Model
{
    use HasFactory;
    protected $table = "assignment_replies";
    protected $guarded = ['id'];

    public function images()
    {
        return $this->hasMany(AssignmentImages::class);
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'id_class', 'id');
    }
}
