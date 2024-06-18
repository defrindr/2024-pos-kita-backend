<?php

namespace App\Models\LMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentImages extends Model
{
    use HasFactory;
    protected $table = "assignment_images";
    protected $guarded = ['id'];

    public function assignment()
    {
        return $this->belongsTo(AssignmentReplies::class, 'id_assignment_reply', 'id');
    }
}
