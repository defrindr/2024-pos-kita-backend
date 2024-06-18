<?php

namespace App\Models;

use App\Models\UMKMGroups;
use App\Models\UMKMGroupList;
use Illuminate\Database\Eloquent\Model;
use App\Models\UMKMEvaluationInfrastructure;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UMKMEvaluations extends Model
{
    use HasFactory;

    protected $table = 'umkm_evaluations';

    protected $guarded = ['id'];

    public function evaluationInfrastructures()
    {
        return $this->hasMany(UMKMEvaluationInfrastructure::class, 'id_umkm_evaluation');
    }

    public function umkmProfile()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function umkmGroupName()
    {
        return $this->hasOne(UMKMGroupList::class, 'id_user', 'id_user')->with('umkmGroup');
    }
}
