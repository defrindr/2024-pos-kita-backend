<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UMKMGroupList extends Model
{
    use HasFactory;
    protected $table = 'umkm_group_list';

    protected $guarded = ['id'];

    public function umkmGroup()
    {
        return $this->belongsTo(UMKMGroups::class, 'id_umkm_group');
    }
}
