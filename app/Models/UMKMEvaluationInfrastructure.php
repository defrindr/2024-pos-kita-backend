<?php

namespace App\Models;

use App\Models\UMKMInfrastructures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UMKMEvaluationInfrastructure extends Model
{
    use HasFactory;

    protected $table = 'umkm_evaluation_infrastructure';

    protected $guarded = ['id'];

    public function evaluation()
    {
        return $this->belongsTo(UMKMEvaluations::class, 'id_umkm_evaluation');
    }

    public function infrastructure()
    {
        return $this->belongsTo(UMKMInfrastructures::class, 'id_infrastructure');
    }
}
