<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UMKMInfrastructures extends Model
{
    use HasFactory;

    protected $table = 'umkm_infrastructures';

    protected $guarded = ['id'];
}
